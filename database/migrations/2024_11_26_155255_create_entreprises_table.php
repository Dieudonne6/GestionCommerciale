<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntreprisesTable extends Migration
{
    public function up()
    {
        Schema::create('entreprises', function (Blueprint $table) {
            $table->id('idEntreprise');
            $table->string('logo')->nullable();
            $table->string('nomEntreprise');
            $table->string('adresseEntreprise')->nullable();
            $table->string('emailEntreprise')->nullable();
            $table->string('telephone')->nullable();
            $table->string('IFU')->nullable(); // Identifiant fiscal unique
            $table->text('Description')->nullable();
            $table->string('site_web')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('entreprises');
    }
}