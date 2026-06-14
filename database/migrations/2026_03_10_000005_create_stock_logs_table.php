<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->integer('quantity_changed');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->timestamps();

            $table->index(['medicine_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
