<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counseling_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');
            $table->foreignId('counselor_id')
                  ->constrained('counselors')
                  ->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->text('concern')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['pending', 'ongoing', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counseling_sessions');
    }
};
