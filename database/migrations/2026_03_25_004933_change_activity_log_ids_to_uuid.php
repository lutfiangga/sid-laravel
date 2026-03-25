<?php

declare(strict_types=1);

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
        Schema::table('activity_log', function (Blueprint $blueprint) {
            $blueprint->string('subject_id', 36)->nullable()->change();
            $blueprint->string('causer_id', 36)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $blueprint) {
            $blueprint->bigInteger('subject_id')->unsigned()->nullable()->change();
            $blueprint->bigInteger('causer_id')->unsigned()->nullable()->change();
        });
    }
};
