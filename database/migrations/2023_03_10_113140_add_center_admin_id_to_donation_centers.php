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
        Schema::table('donation_centers', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()
                ->constrained('admins','id')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donation_centers', function (Blueprint $table) {
            $table->dropColumn('admin_id');
        });
    }
};
