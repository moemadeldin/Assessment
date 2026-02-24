<?php

declare(strict_types=1);

use App\Enums\SalesReturnStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_returns', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('return_number')->unique();
            $table->date('return_date');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default(SalesReturnStatus::Pending->value);
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'customer_id']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'invoice_id']);
            $table->index(['user_id', 'return_date']);
            $table->softDeletes();
        });
    }
};
