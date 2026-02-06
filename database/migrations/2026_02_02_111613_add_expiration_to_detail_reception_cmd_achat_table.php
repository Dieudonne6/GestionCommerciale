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
        Schema::table('detail_reception_cmd_achats', function (Blueprint $table) {
               $table->date('expiration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_reception_cmd_achats', function (Blueprint $table) {
                $table-> dropColmu('expiration');
        });
    }
};
