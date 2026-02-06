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
        Schema::create('categories_tarifaires', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // VIP, GROS, PARTENAIRE
            $table->string('libelle');
            $table->enum('type_reduction', ['pourcentage', 'fixe']);
            $table->decimal('valeur_reduction', 10, 2);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories_tarifaires');
    }
};
