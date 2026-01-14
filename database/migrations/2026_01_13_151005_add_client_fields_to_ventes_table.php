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
        Schema::table('ventes', function (Blueprint $table) {
            $table->integer('IFUClient')->nullable()->after('statutVente');
            $table->string('nomClient')->nullable()->after('statutVente');
            $table->string('telClient')->nullable()->after('statutVente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn(['IFUClient', 'nomClient', 'telClient']);
        });
    }
};
