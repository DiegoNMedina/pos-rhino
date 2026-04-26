<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Stripe\StripeClient;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'plan' => ['nullable', 'in:starter,pro,enterprise'],
            'billing_method' => ['nullable', 'in:stripe,transfer'],
        ]);

        $plan = (string) ($request->input('plan') ?: 'pro');
        $billingMethod = (string) ($request->input('billing_method') ?: 'stripe');

        $store = Store::query()->create([
            'code' => strtoupper(Str::random(8)),
            'name' => $request->name,
            'plan' => $plan,
            'subscription_status' => 'inactive',
            'billing_method' => $billingMethod === 'transfer' ? 'transfer' : null,
            'trial_ends_at' => null,
            'subscription_ends_at' => null,
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => UserRole::ADMIN,
            'store_id' => $store->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($billingMethod === 'stripe') {
            $stripeSecret = (string) config('services.stripe.secret');
            $priceId = (string) config('services.stripe.prices.'.$plan);
            if ($stripeSecret !== '' && $priceId !== '') {
                $stripe = new StripeClient($stripeSecret);

                $session = $stripe->checkout->sessions->create([
                    'mode' => 'subscription',
                    'payment_method_types' => ['card'],
                    'customer_email' => $user->email,
                    'client_reference_id' => (string) $store->id,
                    'metadata' => [
                        'store_id' => (string) $store->id,
                        'plan' => $plan,
                    ],
                    'subscription_data' => [
                        'metadata' => [
                            'store_id' => (string) $store->id,
                            'plan' => $plan,
                        ],
                    ],
                    'line_items' => [
                        [
                            'price' => $priceId,
                            'quantity' => 1,
                        ],
                    ],
                    'success_url' => route('billing.success', [], true).'?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('pricing', [], true).'?plan='.$plan,
                ]);

                return redirect()->away($session->url);
            }
        }

        return redirect()->route('pricing', ['plan' => $plan]);
    }
}
