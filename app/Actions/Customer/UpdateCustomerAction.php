<?php

declare(strict_types=1);

namespace App\Actions\Customer;

use App\Models\Customer;

final readonly class UpdateCustomerAction
{
    public function execute(Customer $customer, array $data): Customer
    {
        $customer->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return $customer;
    }
}
