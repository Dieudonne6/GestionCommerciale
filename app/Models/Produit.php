<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $primaryKey = 'idP'; // Clé primaire de votre table

    protected $fillable = [
        'NomP',
        'descP',
        'imgP',
        'qteP',
        'stockDown',
        'PrixVente',
        'categorieP',
        'userId',
        'Magasin',
    ];
}
