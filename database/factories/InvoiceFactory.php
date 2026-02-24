<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $taxRates = [0, 5, 10, 15, 20];

        $subtotal = $this->faker->randomFloat(2, 100, 10000);

        $taxRate = $this->faker->randomElement($taxRates);
        $tax = round($subtotal * ($taxRate / 100), 2);

        $total = round($subtotal + $tax, 2);
        $invoiceDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $dueDate = $this->faker->optional()->dateTimeBetween($invoiceDate, $invoiceDate->format('Y-m-d').' +6 months');

        return [
            'user_id' => User::factory(),
            'customer_id' => Customer::factory(),
            'invoice_number' => 'INV-'.$this->faker->unique()->numerify('########'),
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax' => $tax,
            'total' => $total,
            'status' => $this->faker->randomElement(InvoiceStatus::cases()),
            'invoice_date' => $invoiceDate,
            'due_date' => $dueDate,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
