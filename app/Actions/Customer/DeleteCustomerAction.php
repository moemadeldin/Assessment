<?php

declare(strict_types=1);

namespace App\Actions\Customer;

use App\Models\Customer;

final readonly class DeleteCustomerAction
{
    public function execute(Customer $customer): void
    {
        $customer->delete();
    }
}
