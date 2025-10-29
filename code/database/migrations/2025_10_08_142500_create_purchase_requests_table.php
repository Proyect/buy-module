<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('department_id')->constrained('departments');
            $table->date('request_date');
            $table->date('required_date');
            $table->string('priority'); // low, medium, high
            $table->string('status');   // pending, approved, rejected, completed
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('currency', 10)->default('ARS');
            $table->text('justification')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users');
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('erp_request_id')->nullable();
            $table->timestamps();
        });

        // Helpful indexes
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->index(['status', 'priority']);
            $table->index(['request_date']);
            $table->index(['department_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
