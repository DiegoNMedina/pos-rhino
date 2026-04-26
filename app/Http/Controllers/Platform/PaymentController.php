<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $payments = SubscriptionPayment::query()
            ->with('store')
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where(function ($inner) use ($q) {
                    $inner
                        ->where('reference_id', 'like', '%'.$q.'%')
                        ->orWhere('status', 'like', '%'.$q.'%')
                        ->orWhere('currency', 'like', '%'.$q.'%')
                        ->orWhere('event_type', 'like', '%'.$q.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('platform.payments.index', [
            'payments' => $payments,
            'q' => $q,
        ]);
    }
}
