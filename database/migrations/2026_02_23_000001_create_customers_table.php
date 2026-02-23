<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')
                ->constrained()
                ->restrictOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique()->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'email']);
            $table->index(['user_id', 'phone']);
            $table->index(['user_id', 'created_at']);
            $table->softDeletes();
        });
    }
};
