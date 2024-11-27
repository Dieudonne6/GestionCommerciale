<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercice extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'idE'; // Clé primaire de votre table

    protected $fillable = [
        'annee',
        'statut',
        'dateDebut',
        'dateFin',
    ];
}