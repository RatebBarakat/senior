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
        Schema::table('appointments', function (Blueprint $table) {
            DB::statement('ALTER TABLE appointments ADD CONSTRAINT check_one_of_two_fields CHECK ((user_id IS NULL AND social_id IS NOT NULL) OR (user_id IS NOT NULL AND social_id IS NULL))');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            DB::statement('ALTER TABLE appointments DROP CONSTRAINT check_one_of_two_fields CHECK ((user_id IS NULL AND social_id IS NOT NULL) OR (user_id IS NOT NULL AND social_id IS NULL))');
        });
    }
};
