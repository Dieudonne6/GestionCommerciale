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
        Schema::create('ligne_ventes', function (Blueprint $table) {
            $table->bigIncrements('idLVente');            
            $table->string('prixLVente');
            $table->string('qteLVente');
            $table->string('lastStockLVente');
            $table->unsignedBigInteger('idV'); // Colonne pour la clé étrangère
            $table->foreign('idV')->references('idV')->on('ventes')->onDelete('cascade');            
            $table->unsignedBigInteger('idP'); // Colonne pour la clé étrangère
            $table->foreign('idP')->references('idP')->on('produits')->onDelete('cascade');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_ventes');
    }
};
