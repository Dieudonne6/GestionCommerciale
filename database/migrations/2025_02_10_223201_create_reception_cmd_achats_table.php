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
        Schema::create('reception_cmd_achats', function (Blueprint $table) {
            $table->bigIncrements('idRecep');            
            $table->date('date');
            $table->string('reference');
            $table->string('numBordereauLivraison');
            $table->string('statutRecep');
            $table->unsignedBigInteger('idExercice'); // Colonne pour la clé étrangère
            $table->foreign('idExercice')->references('idExercice')->on('exercices')->onDelete('cascade'); 
            $table->unsignedBigInteger('idCommande'); // Colonne pour la clé étrangère
            $table->foreign('idCommande')->references('idCommande')->on('commande_achats')->onDelete('cascade'); 
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
        Schema::dropIfExists('reception_cmd_achats');
    }
};
