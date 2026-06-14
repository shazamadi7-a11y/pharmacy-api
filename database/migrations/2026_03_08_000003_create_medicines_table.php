<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('brand_name');
            $table->string('scientific_name');
            $table->string('dosage_form');
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity')->default(0);
            $table->date('expiry_date')->nullable();
            $table->boolean('requires_prescription')->default(false);
            $table->boolean('is_available')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
