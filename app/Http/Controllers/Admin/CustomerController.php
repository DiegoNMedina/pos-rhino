<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $authUser = $request->user();
        $storeId = $authUser ? $authUser->store_id : null;

        $customers = Customer::query()
            ->when($storeId !== null, function ($builder) use ($storeId) {
                $builder->where('store_id', $storeId);
            })
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where(function ($inner) use ($q) {
                    $inner
                        ->where('name', 'like', '%'.$q.'%')
                        ->orWhere('phone', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $authUser = $request->user();
        $payload = $request->validated();
        if ($authUser && $authUser->store_id !== null) {
            $payload['store_id'] = $authUser->store_id;
        }

        Customer::query()->create($payload);

        return redirect()->route('admin.customers.index')->with('success', 'Cliente creado.');
    }

    public function edit(Customer $customer)
    {
        $authUser = request()->user();
        if ($authUser && $authUser->store_id !== null) {
            abort_unless((int) $customer->store_id === (int) $authUser->store_id, 404);
        }

        return view('admin.customers.edit', [
            'customer' => $customer,
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $authUser = $request->user();
        if ($authUser && $authUser->store_id !== null) {
            abort_unless((int) $customer->store_id === (int) $authUser->store_id, 404);
        }

        $customer->fill($request->validated());
        $customer->save();

        return redirect()->route('admin.customers.index')->with('success', 'Cliente actualizado.');
    }

    public function destroy(Customer $customer)
    {
        $authUser = request()->user();
        if ($authUser && $authUser->store_id !== null) {
            abort_unless((int) $customer->store_id === (int) $authUser->store_id, 404);
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Cliente eliminado.');
    }
}
