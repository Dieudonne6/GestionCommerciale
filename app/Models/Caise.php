<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caise extends Model
{
    use HasFactory;
    
    protected $table = 'caises'; // Nom de la table
    protected $primaryKey = 'idCais'; // Clé primaire de votre table
    public $timestamps = true; // Utilisation des timestamps

    protected $fillable = [
        'codeCais',
        'libelleCais',
    ];
}