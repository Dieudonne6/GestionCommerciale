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
        Schema::create('inventaires', function (Blueprint $table) {
            $table->bigIncrements('idInventaire');            
            $table->date('dateInv');
            $table->string('numeroInv');
            $table->string('description');
            $table->string('statutInv');
            $table->unsignedBigInteger('idMag'); // Colonne pour la clé étrangère
            $table->foreign('idMag')->references('idMag')->on('magasins')->onDelete('cascade');  
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
        Schema::dropIfExists('inventaires');
    }
};
