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
        Schema::create('produits', function (Blueprint $table) {
            $table->bigIncrements('idP');
            $table->string('NomP');
            $table->string('descP');
            $table->string('imgP');
            // $table->string('qteP');
            $table->string('stockDown');
            $table->string('PrixVente');
            $table->unsignedBigInteger('categorieP'); // Colonne pour la clé étrangère
            $table->foreign('categorieP')->references('idC')->on('categories')->onDelete('cascade');
            $table->unsignedBigInteger('userId'); // Colonne pour la clé étrangère
            $table->foreign('userId')->references('idU')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('Magasin'); // Colonne pour la clé étrangère
            $table->foreign('Magasin')->references('idMgs')->on('magasins')->onDelete('cascade');
            $table->timestamps();        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
