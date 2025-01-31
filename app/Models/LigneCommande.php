<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Commande;

class LigneCommande extends Model
{
    use HasFactory;

    protected $primaryKey = 'idLCmd'; // Clé primaire de votre table
    public $timestamps = false;
    protected $table = 'ligne_commandes'; // Nom de la table
    protected $fillable = [
        'qteCmd',
        'prix',
        'qteRestant',
        'qteLivre',
        'TVA',
        'idCmd',
        'idP',
    ];
    // Dans le modèle Commande.php


//     public function commande()
// {
//     return $this->belongsTo(Commande::class, 'idCmd');
// }
// Dans le modèle LigneCommande.php
public function commande() {
    return $this->belongsTo(Commande::class, 'idCmd');
}

}
