<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;

    protected $primaryKey = 'idV'; // Clé primaire de votre table

    protected $fillable = [
        'numV',
        'descV',
        'modePaiement',
        'montantTTC',
        'montantHT',
        'dateOperation',
        'idCL',
        'idU',
        'idE',
    ];
}
