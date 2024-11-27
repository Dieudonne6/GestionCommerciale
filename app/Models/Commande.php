<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $primaryKey = 'idCmd'; // Clé primaire de votre table

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
}
