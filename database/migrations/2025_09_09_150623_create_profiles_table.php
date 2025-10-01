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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string("number_kak");
            $table->string("number_fnkp")->nullable();
            $table->string("number_cit")->nullable();
            $table->string("number_tptd")->nullable();
            $table->string("number_jks")->nullable();
            $table->foreignId('arbitrator_id')->nullable()->index();
            $table->date("admission_date")->nullable();
            $table->text("photo")->nullable();
            $table->string("father_name")->nullable();
            $table->string("mother_name")->nullable();
            $table->string("document_type")->nullable();
            $table->string("document_number")->nullable();
            $table->date("birth_date")->nullable();
            $table->string("nationality")->nullable();
            $table->string("profession")->nullable();
            $table->string("address")->nullable();
            $table->string("postal_code")->nullable();
            $table->string("city")->nullable();
            $table->string("district")->nullable();
            $table->string("cell_number")->nullable();
            $table->string("phone_number")->nullable();
            $table->string("email")->nullable();
            $table->string("contact")->nullable();
            $table->string("contact_number")->nullable();
            $table->string("contact_cell")->nullable();
            $table->string("contact_email")->nullable();
            $table->text("observations")->nullable();
            $table->foreignId("club_id")->nullable()->index();
            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
