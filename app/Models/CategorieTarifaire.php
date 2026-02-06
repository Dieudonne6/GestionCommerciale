<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieTarifaire extends Model
{

    protected $table = 'categories_tarifaires';

    protected $fillable = [
        'code',
        'libelle',
        'type_reduction',
        'valeur_reduction',
        'actif'
    ];

    // public function clients()
    // {
    //     return $this->hasMany(Client::class);
    // }

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }
}

