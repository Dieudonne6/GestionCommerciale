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
        Schema::create('ligne_commandes', function (Blueprint $table) {
            $table->bigIncrements('idLCmd');            
            $table->string('qteCmd');
            $table->string('prix');
            $table->string('qteRestant');
            $table->string('qteLivre');
            $table->string('TVA');
            $table->unsignedBigInteger('idCmd'); // Colonne pour la clé étrangère
            $table->foreign('idCmd')->references('idCmd')->on('commandes')->onDelete('cascade');            
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
        Schema::dropIfExists('ligne_commandes');
    }
};
