<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::transaction(function (): void {
            $user = User::factory()->create([
                'name' => 'Mohamed',
                'email' => 'mohamed@gmail.com',
                'password' => Hash::make('0123456789'),
            ]);

            Customer::factory()
                ->count(100)
                ->for($user)
                ->has(
                    Invoice::factory()
                        ->count(1)
                        ->for($user)
                        ->hasItems(random_int(1, 5))
                )
                ->create();
        });

    }
}
