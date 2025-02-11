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
        Schema::create('transfert_magasins', function (Blueprint $table) {
            $table->bigIncrements('idTransMag');            
            $table->date('dateTransfert');
            $table->string('referenceTransfert');
            $table->unsignedBigInteger('idMag'); // Colonne pour la clé étrangère
            $table->foreign('idMag')->references('idMag')->on('magasins')->onDelete('cascade');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfert_magasins');
    }
};
