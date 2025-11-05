<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('club_instructors', function (Blueprint $table) {
            $table->unique(['club_id', 'profile_id'], 'club_instructors_unique');
        });
    }

    public function down(): void
    {
        Schema::table('club_instructors', function (Blueprint $table) {
            $table->dropUnique('club_instructors_unique');
        });
    }
};