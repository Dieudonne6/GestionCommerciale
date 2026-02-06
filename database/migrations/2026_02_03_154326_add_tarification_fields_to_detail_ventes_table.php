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
        Schema::table('detail_ventes', function (Blueprint $table) {
            $table->decimal('prix_unitaire_brut', 10, 2)->after('montantTTC');
            $table->decimal('total_ligne_brut', 12, 2)->after('montantTTC');

            $table->decimal('reduction_ligne', 12, 2)->default(0);
            $table->decimal('total_ligne_net', 12, 2)->default(0);
            $table->decimal('prix_unitaire_net', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_ventes', function (Blueprint $table) {
            $table->dropColumn([
                'prix_unitaire_brut',
                'total_ligne_brut',
                'reduction_ligne',
                'total_ligne_net',
                'prix_unitaire_net'
            ]);
        });
    }
};
