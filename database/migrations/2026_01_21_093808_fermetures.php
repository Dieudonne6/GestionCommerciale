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
        Schema::create('fermetures', function (Blueprint $table) {
            $table->bigIncrements('idFermeture');
            $table->unsignedBigInteger('idU');
            $table->date('date');
            $table->time('heure');
            $table->timestamps();

            $table->foreign('idU')->references('idU')->on('utilisateurs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fermetures');
    }
};
