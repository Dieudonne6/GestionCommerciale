<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    use HasFactory;
    protected $primaryKey = 'idF'; // Clé primaire de votre table

    protected $fillable = [
        'NomF',
        'PrenomF',
        'AdresseF',
        'ContactF',
    ];
}
