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
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->bigIncrements('idF');            
            $table->string('IFU');
            $table->string('nom');
            $table->string('adresse');
            $table->string('telephone');
            $table->string('mail');
            $table->unsignedBigInteger('idCatFour'); // Colonne pour la clé étrangère
            $table->foreign('idCatFour')->references('idCatFour')->on('categorie_fournisseurs')->onDelete('cascade');   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};
