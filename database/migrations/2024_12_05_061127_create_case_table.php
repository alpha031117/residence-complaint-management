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
        Schema::create('case', function (Blueprint $table) {
            $table->id();
            //Foreign key from complaint table
            $table->foreignId('complaint_id')->constrained('complaints')->onDelete('cascade');

            // Start date
            $table->timestamp('start_date')->nullable();
            // End date
            $table->timestamp('end_date')->nullable();

            // Case status
            $table->string('case_status')->default('pending');
            
            // Foreign key from users table
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case');
    }
};
