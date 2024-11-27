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
        Schema::create('ligne_receptions', function (Blueprint $table) {
            $table->bigIncrements('idLReception');            
            $table->string('qteReception');
            $table->string('lastStock');
            $table->string('lastCUMP');
            $table->string('prixUn');
            $table->unsignedBigInteger('idReception'); // Colonne pour la clé étrangère
            $table->foreign('idReception')->references('idReception')->on('receptions')->onDelete('cascade');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_receptions');
    }
};
