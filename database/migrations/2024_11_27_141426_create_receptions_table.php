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
        Schema::create('receptions', function (Blueprint $table) {
            $table->bigIncrements('idReception');            
            $table->string('numReception');
            $table->dateTime('dateReception');
            $table->string('RefNumBonReception');
            $table->unsignedBigInteger('idCmd'); // Colonne pour la clé étrangère
            $table->foreign('idCmd')->references('idCmd')->on('commandes')->onDelete('cascade');
            $table->unsignedBigInteger('idE'); // Colonne pour la clé étrangère
            $table->foreign('idE')->references('idE')->on('exercices')->onDelete('cascade');   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receptions');
    }
};
