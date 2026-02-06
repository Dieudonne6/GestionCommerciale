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
            $table->foreignId('categorie_tarifaire_id')
                ->nullable()
                ->after('montantTotal')
                ->constrained('categories_tarifaires')
                ->nullOnDelete();

            $table->decimal('total_brut', 12, 2)->default(0);
            $table->decimal('total_reduction', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->foreignId('categorie_tarifaire_id')
                ->nullable()
                ->after('montantTotal')
                ->constrained('categories_tarifaires')
                ->nullOnDelete();

            $table->decimal('total_brut', 12, 2)->default(0);
            $table->decimal('total_reduction', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);
        });
    }
};
