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
        Schema::create('detail_inventaires', function (Blueprint $table) {
            $table->bigIncrements('idDetailInv');            
            $table->date('stockTheorique');
            $table->string('stockReel');
            $table->string('prixUnit');
            $table->unsignedBigInteger('idInventaire'); // Colonne pour la clé étrangère
            $table->foreign('idInventaire')->references('idInventaire')->on('inventaires')->onDelete('cascade');  
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
        Schema::dropIfExists('detail_inventaires');
    }
};
