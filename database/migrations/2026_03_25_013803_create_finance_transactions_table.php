<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('finance_period_id')->constrained('finance_periods')->cascadeOnDelete();
            $table->foreignUuid('finance_account_id')->constrained('finance_accounts')->cascadeOnDelete();
            
            $table->enum('type', ['pemasukan', 'pengeluaran', 'pembiayaan']);
            $table->date('transaction_date');
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->string('evidence_file')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};
