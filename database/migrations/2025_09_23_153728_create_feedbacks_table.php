<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('counselor_id')->constrained()->onDelete('cascade');
            $table->foreignId('counseling_session_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->comment('1–5 overall rating');
            $table->text('likes')->nullable();
            $table->text('comments')->nullable();
            
            // 1 to 12 questions
            for ($i = 1; $i <= 12; $i++) {
                $table->tinyInteger("q$i")->unsigned()->nullable()->comment("Question $i rating");
            }

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
