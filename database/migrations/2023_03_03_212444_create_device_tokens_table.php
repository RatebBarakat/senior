<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->text('token');
            $table->string('device_name')->nullable();
            $table->foreignId('user_id')->constrained('users','id')
                ->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('admins','id')
                ->cascadeOnDelete();
            $table->foreignId('social_id')->constrained('socials-users','id')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
