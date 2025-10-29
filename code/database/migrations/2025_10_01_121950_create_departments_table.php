<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('budget_limit', 15, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('erp_department_id')->nullable();
            $table->timestamps();
            
            // Índices para optimización
            $table->index('code');
            $table->index('manager_id');
            $table->index('is_active');
            $table->index('erp_department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
