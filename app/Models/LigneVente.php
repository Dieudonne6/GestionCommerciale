<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneVente extends Model
{
    use HasFactory;

    protected $primaryKey = 'idLVente'; // Clé primaire de votre table

    protected $fillable = [
        'prixLVente',
        'qteLVente',
        'lastStockLVente',
        'idV',
        'idP',
    ];
}
