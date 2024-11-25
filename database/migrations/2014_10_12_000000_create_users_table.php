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
        Schema::create('users', function (Blueprint $table) {
            $table->idU(); // Identifiant utilisateur
            $table->string('login');
            $table->string('nomU');
            $table->string('adresseU');
            $table->string('telephone');
            $table->string('password');
            $table->unsignedBigInteger('roleID'); // Colonne pour la clé étrangère
            $table->foreign('roleID')->references('id')->on('roles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
