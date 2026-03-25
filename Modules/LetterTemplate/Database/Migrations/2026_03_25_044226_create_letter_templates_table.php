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
        Schema::create('letter_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('kode')->unique();
            $table->longText('content');
            $table->json('placeholders')->nullable();
            $table->integer('margin_top')->default(20);
            $table->integer('margin_bottom')->default(20);
            $table->integer('margin_left')->default(25);
            $table->integer('margin_right')->default(20);
            $table->string('orientation')->default('portrait');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_templates');
    }
};
