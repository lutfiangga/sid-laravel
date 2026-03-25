<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('letter_requests', function (Blueprint $table) {
            // Stores uploaded file paths or drive links per requirement key
            $table->json('attachments')->nullable()->after('data');
            $table->string('rejection_reason')->nullable()->after('workflow_status');
        });
    }

    public function down(): void
    {
        Schema::table('letter_requests', function (Blueprint $table) {
            $table->dropColumn(['attachments', 'rejection_reason']);
        });
    }
};
