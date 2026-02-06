<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategorieTarifaire;
use Carbon\Carbon;

class CategorieTarifaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'code' => 'STD',
                'libelle' => 'Tarif Standard',
                'type_reduction' => 'fixe',
                'valeur_reduction' => 0,
                'actif' => true,
            ],
            [
                'code' => 'VIP',
                'libelle' => 'Client VIP',
                'type_reduction' => 'pourcentage',
                'valeur_reduction' => 10,
                'actif' => true,
            ],
            [
                'code' => 'GROS',
                'libelle' => 'Client Grossiste',
                'type_reduction' => 'pourcentage',
                'valeur_reduction' => 15,
                'actif' => true,
            ],
            [
                'code' => 'PROMO',
                'libelle' => 'Tarif Promotionnel',
                'type_reduction' => 'fixe',
                'valeur_reduction' => 700,
                'actif' => true,
            ],
            [
                'code' => 'ARCH',
                'libelle' => 'Ancien Client',
                'type_reduction' => 'pourcentage',
                'valeur_reduction' => 5,
                'actif' => false,
            ],
        ];

        foreach ($categories as $categorie) {
            CategorieTarifaire::updateOrCreate(
                ['code' => $categorie['code']],
                $categorie
            );
        }
    }
}
