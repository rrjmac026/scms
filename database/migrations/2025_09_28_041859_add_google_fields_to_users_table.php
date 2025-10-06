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
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->index(); // Add index for lookups
            $table->json('google_token')->nullable(); // Use json instead of text for better structure
            $table->timestamp('google_token_expires_at')->nullable();
            // Remove google_refresh_token - it's included in google_token JSON
        });

        // Add google_event_id to appointments table instead of users table
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id',
                'google_token', 
                'google_token_expires_at'
            ]);
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('google_event_id');
        });
    }
};