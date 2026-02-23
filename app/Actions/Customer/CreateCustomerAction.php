<?php

declare(strict_types=1);

namespace App\Actions\Customer;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final readonly class CreateCustomerAction
{
    public function execute(array $data, User $user): Model
    {
        return Customer::query()->create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
    }
}
