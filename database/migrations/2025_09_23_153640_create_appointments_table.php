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
            
            // Original appointment date/time
            $table->date('preferred_date');
            $table->time('preferred_time');
            
            // Proposed reschedule date/time
            $table->date('proposed_date')->nullable();
            $table->time('proposed_time')->nullable();
            
            // Status with new values
            $table->enum('status', [
                'pending', 
                'approved', 
                'declined', 
                'completed', 
                'rejected',
                'accepted',
                'cancelled'
            ])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('cancelled_reason')->nullable();        
            // Concerns and reasons
            $table->text('concern')->nullable();
            $table->text('reschedule_reason')->nullable();
            $table->text('student_reschedule_response')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Optional: Google Calendar integration
            $table->string('google_event_id')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};