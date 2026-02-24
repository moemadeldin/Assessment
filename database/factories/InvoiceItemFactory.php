<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

final class InvoiceItemFactory extends Factory
{
    public function definition(): array
    {
        $qty = $this->faker->numberBetween(1, 100);
        $unitPrice = $this->faker->randomFloat(2, 10, 1000);

        return [
            'invoice_id' => Invoice::factory(),
            'description' => $this->faker->sentence(),
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'total' => round($qty * $unitPrice, 2),
        ];
    }
}
