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
        Schema::create('detail_reception_cmd_achats', function (Blueprint $table) {
            $table->bigIncrements('idDetailRecepCmdAchat');            
            $table->string('qteReceptionne');
            $table->string('prixUnit');
            $table->unsignedBigInteger('idRecep'); // Colonne pour la clé étrangère
            $table->foreign('idRecep')->references('idRecep')->on('reception_cmd_achats')->onDelete('cascade'); 
            $table->unsignedBigInteger('idDetailCom'); // Colonne pour la clé étrangère
            $table->foreign('idDetailCom')->references('idDetailCom')->on('detail_commande_achats')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_reception_cmd_achats');
    }
};
