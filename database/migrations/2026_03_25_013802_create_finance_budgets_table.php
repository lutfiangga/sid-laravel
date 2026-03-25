<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('finance_period_id')->constrained('finance_periods')->cascadeOnDelete();
            $table->foreignUuid('finance_account_id')->constrained('finance_accounts')->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['finance_period_id', 'finance_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_budgets');
    }
};
