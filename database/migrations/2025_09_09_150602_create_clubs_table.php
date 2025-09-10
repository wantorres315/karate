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
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("acronym")->nullable();
            $table->text("logo")->nullable();
            $table->string("username_fnkp")->nullable();
            $table->string("username_password_fnkp")->nullable();
            $table->string("certificate_fnkp")->nullable();
            $table->string("status_year")->nullable();
            $table->enum("status", ["active", "inactive"]);
            $table->string("address")->nullable();
            $table->string("postal_code")->nullable();
            $table->string("city")->nullable();
            $table->string("district")->nullable();
            $table->string("cell_number")->nullable();
            $table->string("phone_number")->nullable();
            $table->string("email")->nullable();
            $table->string("website")->nullable();
            $table->string("responsible_name")->nullable();
            $table->string("responsible_cell_number")->nullable();
            $table->string("responsible_telephone_number")->nullable();
            $table->string("responsible_position")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
