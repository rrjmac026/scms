<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('counselor_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned()->comment('1–5 overall rating');
            $table->text('comments')->nullable();

            // 1 to 10 nani na question aron di na mag isa2 og code sa table
            for ($i = 1; $i <= 10; $i++) {
                $table->tinyInteger("q$i")->unsigned()->nullable()->comment("Question $i rating");
            }

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
