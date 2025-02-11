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
        Schema::create('stockes', function (Blueprint $table) {
            $table->bigIncrements('idStocke');            
            $table->string('qteStocke');
            $table->string('CUMP');
            $table->unsignedBigInteger('idPro'); // Colonne pour la clé étrangère
            $table->foreign('idPro')->references('idPro')->on('produits')->onDelete('cascade');  
            $table->unsignedBigInteger('idMag'); // Colonne pour la clé étrangère
            $table->foreign('idMag')->references('idMag')->on('magasins')->onDelete('cascade');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockes');
    }
};
