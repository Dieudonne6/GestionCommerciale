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
        Schema::create('commandes', function (Blueprint $table) {
            $table->bigIncrements('idCmd');            
            $table->string('numCmd');
            $table->string('descCmd');
            $table->string('montantTTC');
            $table->string('montantHT');
            $table->string('delai');
            $table->dateTime('dateOperation');
            $table->dateTime('dateRemise');
            $table->unsignedBigInteger('idF'); // Colonne pour la clé étrangère
            $table->foreign('idF')->references('idF')->on('fournisseurs')->onDelete('cascade');
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
        Schema::dropIfExists('commandes');
    }
};
