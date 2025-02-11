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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->bigIncrements('idU');            
            $table->string('nom');
            $table->string('adresse');
            $table->string('telephone');
            $table->string('mail');
            $table->unsignedBigInteger('idRole'); // Colonne pour la clé étrangère
            $table->foreign('idRole')->references('idRole')->on('roles')->onDelete('cascade'); 
            $table->unsignedBigInteger('idE'); // Colonne pour la clé étrangère
            $table->foreign('idE')->references('idE')->on('entreprises')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
