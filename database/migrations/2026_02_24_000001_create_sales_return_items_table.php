<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_return_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('sales_return_id')->constrained('sales_returns')->cascadeOnDelete();
            $table->foreignUuid('invoice_item_id')->nullable()->constrained('invoice_items')->nullOnDelete();
            $table->string('description');
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
            $table->index('sales_return_id');
            $table->index('invoice_item_id');
            $table->softDeletes();
        });
    }
};
