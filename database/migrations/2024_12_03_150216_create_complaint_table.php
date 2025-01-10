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
        // Creating complaint_attachments table
        Schema::create('complaint_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('file_path'); // Path to the attachment file
            $table->string('file_type'); // e.g., 'image', 'pdf', 'docx'
            $table->timestamps();
        }); 

        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
        
            // Foreign key from users user's role = user table
            $table->foreignId('issued_by')->constrained('users')->onDelete('cascade');

            //Foreign key from residence table
            $table->foreignId('residence_id')->constrained('residence')->onDelete('cascade');

            $table->string('complaint_title');
            $table->text('complaint_details');
            $table->string('complaint_feedback')->nullable();
            $table->string('complaint_status')->default('pending');
        
            // Foreign key from complaint_attachments table
            // Once a complaint is deleted, the attachment should be deleted
            $table->foreignId('file_attachment')->nullable()->constrained('complaint_attachments')->onDelete('cascade');
            $table->timestamp('resolved_at')->nullable();
            $table->integer('resolution_time')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
        
            // Foreign key from users table (updated_by)
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint');
    }
};
