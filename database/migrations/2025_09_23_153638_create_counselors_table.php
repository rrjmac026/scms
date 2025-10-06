<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counselors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_number')->unique();
            $table->string('assigned_grade_level')->nullable();
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('bio')->nullable();
            $table->string('status')->default('active');
            $table->json('availability_schedule')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselors');
    }
};
