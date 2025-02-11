<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;


class FamilleProduit extends Model
{
    use HasFactory;
    protected $primaryKey = 'idFamPro'; // Clé primaire de la table

    protected $fillable = [
        'codeFamille',
        'libelle',
        'TVA',
    ];

    public function produit() {
        return $this->hasMany(Produit::class, 'idFamPro');
    }
}
