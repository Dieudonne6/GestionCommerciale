<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Fournisseur;


class CategorieFournisseur extends Model
{
    use HasFactory;
    protected $table = 'categorie_fournisseurs';
    protected $primaryKey = 'idCatFour'; // Clé primaire de la table

    protected $fillable = [
        'libelle',
        'codeCatFour'
    ];

    public function fournisseur() {
        return $this->hasMany(Fournisseur::class, 'idF');
    }
}
