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
        Schema::create('detail_fermetures', function (Blueprint $table) {
            $table->bigIncrements('idDetailFermeture');
            $table->unsignedBigInteger('idFermeture');
            $table->unsignedBigInteger('idPro');
            $table->integer('qteStocke');
            $table->timestamps();

            $table->foreign('idFermeture')->references('idFermeture')->on('fermetures')->onDelete('cascade');
            $table->foreign('idPro')->references('idPro')->on('stockes');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('detail_fermetures');
    }
};
