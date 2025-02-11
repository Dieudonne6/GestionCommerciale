<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategorieFournisseur;
use App\Models\CommandeAchat;


class Fournisseur extends Model
{
    use HasFactory;

    protected $primaryKey = 'idF'; // Clé primaire de la table

    protected $fillable = [
        'IFU',
        'nom',
        'adresse',
        'telephone',
        'mail',
        'idCatFour',
    ];

    public function categorieFournisseur() {
        return $this->belongsTo(CategorieFournisseur::class, 'idCatFour');
    }

    public function commandeAchat() {
        return $this->hasMany(CommandeAchat::class, 'idF');
    }
}
