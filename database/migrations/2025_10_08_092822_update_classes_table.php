<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
         Schema::table('classes', function (Blueprint $table) {
            $table->date('startDate')->nullable()->after('week_days');
            $table->date('endDate')->nullable()->after('startDate');
        });
        
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            Schema::dropColumn('startDate');
            Schema::dropColumn('endDate');
        });
    }
};
