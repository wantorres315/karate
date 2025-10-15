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
        Schema::create('escaloes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: "Infantis", "Juvenis", etc.
            $table->date('start_date'); // Data inicial da faixa
            $table->date('end_date');   // Data final da faixa
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escaloes');
    }
};
