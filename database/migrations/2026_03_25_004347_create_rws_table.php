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
        Schema::create('rws', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('dusun_id')->constrained('dusuns')->onDelete('cascade');
            $table->string('nomor', 5);
            $table->string('ketua')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('nomor');
            $table->unique(['dusun_id', 'nomor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rws');
    }
};
