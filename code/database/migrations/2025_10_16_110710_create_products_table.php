<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name')->index();
            $table->text('description')->nullable();

            // Relaciones opcionales (sin FK para evitar dependencias si aún no existen)
            $table->unsignedBigInteger('category_id')->nullable()->index();
            $table->unsignedBigInteger('supplier_id')->nullable()->index();

            $table->decimal('unit_price', 15, 2)->default(0);
            $table->string('currency', 10)->default('ARS');
            $table->string('unit_of_measure', 50)->nullable();

            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('lead_time')->default(0); // días

            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
