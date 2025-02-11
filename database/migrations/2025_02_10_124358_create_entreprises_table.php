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
        Schema::create('entreprises', function (Blueprint $table) {
            $table->bigIncrements('idE');            
            $table->string('IFU');
            $table->string('nom');
            $table->binary('logo');
            $table->string('adresse');
            $table->string('telephone');
            $table->string('mail');
            $table->string('RCCM');
            $table->string('regime');
            $table->unsignedBigInteger('idParent')->nullable(); // Clé étrangère vers 'idE' de la même table
            $table->foreign('idParent')->references('idE')->on('entreprises')->onDelete('cascade'); // Définir la relation 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprises');
    }
};
