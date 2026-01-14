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
        Schema::table('famille_produits', function (Blueprint $table) {
            $table->string('groupe')->nullable()->after('TVA'); // chemin du fichier (storage)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('famille_produits', function (Blueprint $table) {
            $table->dropColumn(['groupe']);
        });
    }
};
