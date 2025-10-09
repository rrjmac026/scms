<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // User Info
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            
            // Contact & Address
            $table->string('email')->unique();
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            
            // Google OAuth
            $table->string('google_id')->nullable()->unique();
            
            // Role & Auth
            $table->enum('role', ['admin', 'counselor', 'student'])->default('student');
            $table->string('status')->default('active');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Profile photo from Jetstream
            $table->string('profile_photo_path', 2048)->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        // Add CHECK constraint to enforce @lccdo.edu.ph email domain
        // This prevents ANY non-university email from being inserted at database level
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE users 
                ADD CONSTRAINT check_university_email_domain 
                CHECK (email LIKE '%@lccdo.edu.ph')
            ");
        } elseif (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE users 
                ADD CONSTRAINT check_university_email_domain 
                CHECK (email LIKE '%@lccdo.edu.ph')
            ");
        }
        // Note: SQLite doesn't support adding constraints after table creation
        // For SQLite (local dev), the controller validation is sufficient

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop constraint first (if it exists)
        if (in_array(DB::getDriverName(), ['mysql', 'pgsql'])) {
            try {
                DB::statement("ALTER TABLE users DROP CONSTRAINT check_university_email_domain");
            } catch (\Exception $e) {
                // Constraint might not exist, continue
            }
        }

        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};