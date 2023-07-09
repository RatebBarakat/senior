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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('location')->nullable();
            $table->text('bio')->nullable();
            $table->enum('blood_type', ['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-'])->nullable();
            $table->morphs('user');
            $table->string('avatar', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
