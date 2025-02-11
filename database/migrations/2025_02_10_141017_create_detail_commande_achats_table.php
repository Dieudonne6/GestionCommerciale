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
        Schema::create('detail_commande_achats', function (Blueprint $table) {
            $table->bigIncrements('idDetailCom');            
            $table->string('qteCmd');
            $table->string('prixUnit');
            $table->string('montantHT');
            $table->string('montantTTC');
            $table->string('qteRestante');
            $table->unsignedBigInteger('idCommande'); // Colonne pour la clé étrangère
            $table->foreign('idCommande')->references('idCommande')->on('commande_achats')->onDelete('cascade'); 
            $table->unsignedBigInteger('idPro'); // Colonne pour la clé étrangère
            $table->foreign('idPro')->references('idPro')->on('produits')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_commande_achats');
    }
};
