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
        Schema::table('facture_normalisees', function (Blueprint $table) {
            $table->binary('qrcode')->nullable()->after('date'); 
            $table->integer('statut')->default(0)->after('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facture_normalisees', function (Blueprint $table) {
            $table->dropColumn(['qrcode', 'statut']);
        });
    }
};
