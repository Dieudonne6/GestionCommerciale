<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    use HasFactory;

    protected $primaryKey = 'idLCmd'; // Clé primaire de votre table

    protected $fillable = [
        'qteCmd',
        'prix',
        'qteRestant',
        'qteLivre',
        'TVA',
        'idCmd',
        'idP',
    ];
}
