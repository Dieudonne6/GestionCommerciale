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
            $table->date('dateOperation');
            $table->string('montantTotal');
            $table->string('reference');
            $table->string('statutVente');
            $table->unsignedBigInteger('idC'); // Colonne pour la clé étrangère
            $table->foreign('idC')->references('idC')->on('clients')->onDelete('cascade'); 
            $table->unsignedBigInteger('idU'); // Colonne pour la clé étrangère
            $table->foreign('idU')->references('idU')->on('utilisateurs')->onDelete('cascade'); 
            $table->unsignedBigInteger('idExercice'); // Colonne pour la clé étrangère
            $table->foreign('idExercice')->references('idExercice')->on('exercices')->onDelete('cascade'); 
            $table->unsignedBigInteger('idModPaie'); // Colonne pour la clé étrangère
            $table->foreign('idModPaie')->references('idModPaie')->on('mode_paiements')->onDelete('cascade'); 
            $table->unsignedBigInteger('idE'); // Colonne pour la clé étrangère
            $table->foreign('idE')->references('idE')->on('entreprises')->onDelete('cascade'); 
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
