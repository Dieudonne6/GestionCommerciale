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
            if (Schema::hasColumn('fournisseurs', 'NomF')) {
                $table->dropColumn('NomF'); // Supprimer 'NomF' si elle existe
            }
            if (Schema::hasColumn('fournisseurs', 'PrenomF')) {
                $table->dropColumn('PrenomF'); // Supprimer 'PrenomF' si elle existe
            }
            if (!Schema::hasColumn('fournisseurs', 'identiteF')) {
                $table->string('identiteF', 191)->after('idF');
            }
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
