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
        Schema::create('ventes', function (Blueprint $table) {
            $table->bigIncrements('idV');            
            $table->string('numV');
            $table->string('descV');
            $table->string('modePaiement');
            $table->string('montantTTC');
            $table->string('montantHT');
            $table->dateTime('dateOperation');
            $table->unsignedBigInteger('idCL'); // Colonne pour la clé étrangère
            $table->foreign('idCL')->references('idCL')->on('clients')->onDelete('cascade');
            $table->unsignedBigInteger('idU'); // Colonne pour la clé étrangère
            $table->foreign('idU')->references('idU')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('idE'); // Colonne pour la clé étrangère
            $table->foreign('idE')->references('idE')->on('exercices')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
