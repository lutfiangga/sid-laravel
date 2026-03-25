<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for a test-only table used by BaseModel/Repository/Service tests.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('status')->default('draft');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_items');
    }
};
