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
        Schema::table('fournisseurs', function (Blueprint $table) {
            $table->dropColumn(['NomF', 'PrenomF']); // Supprimer les colonnes existantes
            $table->string('identiteF')->after('idF'); // Ajouter la nouvelle colonne après 'idF'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fournisseurs', function (Blueprint $table) {
            $table->string('NomF')->after('idF'); // Ré-ajouter 'NomF'
            $table->string('PrenomF')->after('NomF'); // Ré-ajouter 'PrenomF'
            $table->dropColumn('identiteF'); // Supprimer 'identiteF'
        });
    }
};
