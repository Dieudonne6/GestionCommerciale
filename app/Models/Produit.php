<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $table = 'produits'; // Nom de la table
    protected $primaryKey = 'idP'; // Clé primaire de votre table
    public $timestamps = false;

    protected $fillable = [
        'NomP',
        'descP',
        'imgP',
        // 'qteP',
        'stockDown',
        'PrixVente',
        'categorieP',
        'userId',
        'Magasin',
    ];

    public function categorie()
{
    return $this->belongsTo(Categorie::class, 'categorieP', 'idC');
}
    public function ligneCommandes()
{
    return $this->hasMany(LigneCommande::class, 'idP', 'idP');
}

}