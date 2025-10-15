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
        Schema::create('valores_aulas', function (Blueprint $table) {
            $table->string('nome');
            $table->decimal('valor_normal', 10, 2);
            $table->decimal('valor_2_membros', 10, 2)->nullable();
            $table->decimal('valor_3_ou_mais_membros', 10, 2)->nullable();
            $table->date("data_inicial");
            $table->date("data_final");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valores_aulas');
    }
};
