<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final readonly class CreateUserAction
{
    public function execute(array $data): User
    {
        return User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'email_verified_at' => now(),
            'password' => Hash::make($data['password']),
        ]);
    }
}
