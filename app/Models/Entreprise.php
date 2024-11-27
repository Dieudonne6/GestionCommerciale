<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    use HasFactory;

    protected $table = 'entreprises'; // Nom de la table
    protected $primaryKey = 'idEntreprise'; // Clé primaire

    protected $fillable = [
        'logo',
        'nomEntreprise',
        'adresseEntreprise',
        'emailEntreprise',
        'telephone',
        'IFU',
        'Description',
        'site_web',
    ];

    public $timestamps = true; // Activez si vous utilisez `created_at` et `updated_at`
}