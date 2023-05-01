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
        Schema::create('center_reports', function (Blueprint $table) {
            $table->id();
            $table->text('file_name');
            $table->foreignId('center_id')->constrained('donation_centers','id')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins','id')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('center_reports');
    }
};
