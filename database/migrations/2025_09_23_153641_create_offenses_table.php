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
        Schema::create('offenses', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('counselor_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('counseling_session_id')->nullable()->constrained()->onDelete('set null');
            $table->string('offense');
            $table->string('remarks')->nullable();
            $table->date('date');
            $table->string('status')->nullable();
            $table->boolean('resolved')->default(false);
            $table->string('solution')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offenses');
    }
};
