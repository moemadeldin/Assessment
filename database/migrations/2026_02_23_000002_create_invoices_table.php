<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->foreignUuid('customer_id')
                ->constrained('customers')
                ->restrictOnDelete();
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('sales_return_total', 10, 2)->default(0);
            $table->string('status')->default(InvoiceStatus::Draft->value);
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->unique(['user_id', 'invoice_number']);
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'customer_id']);
            $table->index(['user_id', 'status']);
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
