<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;


class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            ['code'=>'tableaudebord','label'=>'Tableau de Bord','route'=>'tableaudebord'],
            ['code'=>'profile','label'=>'profile','route'=>'profile'],
            ['code'=>'categoriesF','label'=>'Catégories de fournisseurs','route'=>'categoriesF'],
            ['code'=>'fournisseur','label'=>'Fournisseurs','route'=>'fournisseur'],

            ['code'=>'commandeAchat','label'=>'Commandes d\'Achat','route'=>'commandeAchat'],

            ['code'=>'magasins','label'=>'Magasins','route'=>'magasins'],

            ['code'=>'familleProduit','label'=>'Familles de produits','route'=>'familleProduit'],
            ['code'=>'categorieProduit','label'=>'Catégories de produits','route'=>'categorieProduit'],
            ['code'=>'Produits','label'=>'Produits','route'=>'Produits'],

            ['code'=>'inventaires','label'=>'Inventaire','route'=>'inventaires'],
            ['code'=>'receptions','label'=>'Réception des Commandes Achats','route'=>'receptions'],
            ['code'=>'consulterStocks','label'=>'Consulter les stocks','route'=>'consulterStocks'],
            ['code'=>'transferts','label'=>'Transferts entre magasins','route'=>'transferts'],

            ['code'=>'fermetures','label'=>'Fermeture de la journée','route'=>'fermetures'],
            ['code'=>'ventes','label'=>'Ventes','route'=>'ventes'],

            ['code'=>'facturation','label'=>'Facturation','route'=>'facturation'],
            ['code'=>'proformat','label'=>'Proforma','route'=>'proformat'],
            ['code'=>'categorieclient','label'=>'Catégories de clients','route'=>'categorieclient'],
            ['code'=>'clients','label'=>'Clients','route'=>'clients'],

            // ['code'=>'modepaiement','label'=>'Modes de Paiement','route'=>'modepaiement'],
            // ['code'=>'role','label'=>'Role','route'=>'role'],
            // ['code'=>'entreprise','label'=>'Entreprises','route'=>'entreprise'],

            // ['code'=>'utilisateurs','label'=>'Utilisateurs','route'=>'utilisateurs'],
            // ['code'=>'caisses','label'=>'Caisses','route'=>'caisses'],
            // ['code'=>'exercice','label'=>'Exercice','route'=>'exercice'],
            // ['code'=>'gestionpermission','label'=>'Gestion des permision','route'=>'menupermissions'],

        ];

        foreach ($menus as $m) {
            Menu::updateOrCreate(['code'=>$m['code']], $m);
        }
    }
    
}
