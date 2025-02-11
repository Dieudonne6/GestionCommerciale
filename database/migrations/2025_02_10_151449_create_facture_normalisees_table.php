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
        Schema::create('facture_normalisees', function (Blueprint $table) {
            $table->bigIncrements('idFacture');            
            $table->json('itemFacture');
            $table->string('CODEMECEF');
            $table->string('nim');
            $table->string('counter');
            $table->string('montantTotal');
            $table->string('montantTotalTTC');
            $table->string('TotalTVA');
            $table->string('groupeTaxation');
            $table->date('date');
            $table->unsignedBigInteger('idV'); // Colonne pour la clé étrangère
            $table->foreign('idV')->references('idV')->on('ventes')->onDelete('cascade');  
            $table->unsignedBigInteger('idCommande'); // Colonne pour la clé étrangère
            $table->foreign('idCommande')->references('idCommande')->on('commande_achats')->onDelete('cascade');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facture_normalisees');
    }
};
