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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('idC');            
            $table->string('IFU');
            $table->string('nom');
            $table->string('adresse');
            $table->string('telephone');
            $table->string('mail');
            $table->unsignedBigInteger('idCatCl'); // Colonne pour la clé étrangère
            $table->foreign('idCatCl')->references('idCatCl')->on('categorie_clients')->onDelete('cascade');   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
