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
        Schema::create('letter_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('penduduk_id');
            $table->uuid('letter_type_id');
            $table->string('nomor_surat')->nullable();
            $table->json('data');
            $table->string('workflow_status')->default('draft');
            $table->uuid('current_official_id')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('penduduk_id')->references('id')->on('penduduks')->onDelete('cascade');
            $table->foreign('letter_type_id')->references('id')->on('letter_types')->onDelete('cascade');
            
            $table->index('workflow_status');
            $table->index('nomor_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_requests');
    }
};
