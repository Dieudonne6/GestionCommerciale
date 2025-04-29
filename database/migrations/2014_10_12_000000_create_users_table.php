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
            $table->bigIncrements('idU'); // Crée une clé primaire nommée 'idU'
            $table->string('login');
            $table->string('nomU');
            $table->string('adresseU');
            $table->string('telephone');
            $table->string('mail')->unique();
            $table->string('password');
            // $table->unsignedBigInteger('roleID'); // Colonne pour la clé étrangère
            // $table->foreign('roleID')->references('id')->on('roles')->onDelete('cascade');
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
