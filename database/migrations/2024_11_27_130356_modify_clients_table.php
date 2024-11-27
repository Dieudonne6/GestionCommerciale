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
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['NomCl', 'PrenomCl']); // Supprimer les colonnes existantes
            $table->string('identiteCl')->after('idCl'); // Ajouter la nouvelle colonne après 'idCl'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('NomCl')->after('idCl'); // Ré-ajouter 'NomCl'
            $table->string('PrenomCl')->after('NomCl'); // Ré-ajouter 'PrenomCl'
            $table->dropColumn('identiteCl'); // Supprimer 'identiteCl'
        });
    }
};
