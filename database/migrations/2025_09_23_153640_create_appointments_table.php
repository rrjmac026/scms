<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('counselor_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('counseling_category_id')
                ->constrained('counseling_categories')
                ->onDelete('cascade');
            $table->date('preferred_date');
            $table->time('preferred_time');
            $table->enum('status', ['pending', 'approved', 'declined', 'completed', 'rejected'])->default('pending');
            $table->text('concern')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
