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
        Schema::create('commande_achats', function (Blueprint $table) {
            $table->bigIncrements('idCommande');            
            $table->date('dateOp');
            $table->string('montantTotalHT');
            $table->string('montantTotalTTC');
            $table->string('reference');
            $table->string('delailivraison');
            $table->string('statutCom');
            $table->unsignedBigInteger('idF'); // Colonne pour la clé étrangère
            $table->foreign('idF')->references('idF')->on('fournisseurs')->onDelete('cascade');  
            $table->unsignedBigInteger('idExercice'); // Colonne pour la clé étrangère
            $table->foreign('idExercice')->references('idExercice')->on('exercices')->onDelete('cascade');  
            $table->unsignedBigInteger('idE'); // Colonne pour la clé étrangère
            $table->foreign('idE')->references('idE')->on('entreprises')->onDelete('cascade');  
            $table->unsignedBigInteger('idU'); // Colonne pour la clé étrangère
            $table->foreign('idU')->references('idU')->on('utilisateurs')->onDelete('cascade');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commande_achats');
    }
};
