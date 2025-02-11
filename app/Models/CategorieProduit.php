<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;


class CategorieProduit extends Model
{
    use HasFactory;
    protected $primaryKey = 'idCatPro'; // Clé primaire de la table

    protected $fillable = [
        'libelle',
    ];

    public function produit() {
        return $this->hasMany(Produit::class, 'idCatPro');
    }
}
