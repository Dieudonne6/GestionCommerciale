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
        Schema::create('proformas', function (Blueprint $table) {
            $table->bigIncrements('idProforma');            
            $table->date('dateOperation');
            $table->string('reference');
            $table->unsignedBigInteger('idC'); // Colonne pour la clé étrangère
            $table->foreign('idC')->references('idC')->on('clients')->onDelete('cascade');   
            $table->unsignedBigInteger('idExercice'); // Colonne pour la clé étrangère
            $table->foreign('idExercice')->references('idExercice')->on('exercices')->onDelete('cascade');   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proformas');
    }
};
