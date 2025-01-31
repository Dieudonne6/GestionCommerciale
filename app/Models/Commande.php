<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Fournisseur;
use App\Models\LigneCommande;

class Commande extends Model
{
    use HasFactory;

    protected $primaryKey = 'idCmd'; // Clé primaire de votre table
    public $timestamps = false;
    protected $table = 'commandes'; // Nom de la table
    protected $fillable = [
        'numCmd',
        'descCmd',
        'montantTTC',
        'montantHT',
        'delai',
        'dateOperation',
        'dateRemise',
        'idF',
        'idU',
        'idE',
    ];
//     public function lignes()
// {
//     return $this->hasMany(LigneCommande::class, 'idCmd');
// }
public function fournisseur()
{
    return $this->belongsTo(Fournisseur::class, 'idF', 'idF');
}

public function lignesCommande() {
    return $this->hasMany(LigneCommande::class, 'idCmd');
}

}
