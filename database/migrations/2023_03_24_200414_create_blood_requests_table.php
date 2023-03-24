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
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('hospital_name');
            $table->string('hospital_location');
            $table->string('contact_name')->nullable();
            $table->string('contact_phone_number');
            $table->enum('blood_type_needed',['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
            $table->float('quantity_needed',5,2);
            $table->enum('urgency_level',['immediate','24 hours']);
            $table->enum('status',['pending','fulfilled','cancelled'])->default('pending');
            $table->foreignId('center_id')->nullable()->constrained('donation_centers')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
