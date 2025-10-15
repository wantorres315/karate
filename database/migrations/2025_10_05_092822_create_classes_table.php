<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date("startDate");
            $table->date("endDate");
            $table->time('start_time');
            $table->time('end_time');
            $table->json("week_days")->nullable();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('class_profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['class_id', 'profile_id']); // evita duplicidade
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_profile');
        Schema::dropIfExists('classes');
    }
};
