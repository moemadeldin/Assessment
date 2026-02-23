<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Actions\Customer\CreateCustomerAction;
use App\Actions\Customer\DeleteCustomerAction;
use App\Actions\Customer\UpdateCustomerAction;
use App\Http\Requests\Customer\FilterCustomerRequest;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use App\Queries\Customer\GetCustomersQuery;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final readonly class CustomerController
{
    public function __construct(
        private GetCustomersQuery $getCustomersQuery,
    ) {}

    public function index(FilterCustomerRequest $request): View
    {
        $customers = $this->getCustomersQuery->execute($request->validated());

        return view('customers.index', [
            'customers' => $customers,
            'filters' => $request->validated(),
        ]);
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(#[CurrentUser()] User $user, StoreCustomerRequest $request, CreateCustomerAction $action): RedirectResponse
    {
        $action->execute($request->validated(), $user);

        return to_route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load('user');

        return view('customers.show', ['customer' => $customer]);
    }

    public function edit(Customer $customer): View
    {

        return view('customers.edit', ['customer' => $customer]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer, UpdateCustomerAction $action): RedirectResponse
    {
        $action->execute($customer, $request->validated());

        return to_route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer, DeleteCustomerAction $action): RedirectResponse
    {
        $action->execute($customer);

        return to_route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
