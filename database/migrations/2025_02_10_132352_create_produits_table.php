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
            $table->bigIncrements('idPro');            
            $table->string('libelle');
            $table->string('prix');
            $table->string('desc');
            $table->binary('image');
            $table->string('stockAlert');
            $table->string('stockMinimum');
            $table->unsignedBigInteger('idCatPro'); // Colonne pour la clé étrangère
            $table->foreign('idCatPro')->references('idCatPro')->on('categorie_produits')->onDelete('cascade');  
            $table->unsignedBigInteger('idFamPro'); // Colonne pour la clé étrangère
            $table->foreign('idFamPro')->references('idFamPro')->on('famille_produits')->onDelete('cascade');  
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
