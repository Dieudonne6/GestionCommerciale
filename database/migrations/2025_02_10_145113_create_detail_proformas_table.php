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
        Schema::create('detail_proformas', function (Blueprint $table) {
            $table->bigIncrements('idDetailProforma');            
            $table->string('qteProforma');
            $table->string('prixUnit');
            $table->string('montantHT');
            $table->string('montantTTC');
            $table->unsignedBigInteger('idProforma'); // Colonne pour la clé étrangère
            $table->foreign('idProforma')->references('idProforma')->on('proformas')->onDelete('cascade');   
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
        Schema::dropIfExists('detail_proformas');
    }
};
