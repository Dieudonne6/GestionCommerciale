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
        Schema::table('detail_transfert_magasins', function (Blueprint $table) {
            // Ajouter la colonne manquante pour la clé étrangère vers transfert_magasins
            $table->unsignedBigInteger('idTransMag')->after('idPro');
            $table->foreign('idTransMag')->references('idTransMag')->on('transfert_magasins')->onDelete('cascade');
            
            // Changer le type de qteTransferer en integer
            $table->integer('qteTransferer')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_transfert_magasins', function (Blueprint $table) {
            $table->dropForeign(['idTransMag']);
            $table->dropColumn('idTransMag');
            $table->string('qteTransferer')->change();
        });
    }
};
