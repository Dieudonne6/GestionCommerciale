<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Entreprise;
use App\Models\Utilisateur;
use App\Models\CommandeAchat;
use App\Models\Vente;



class Entreprise extends Model
{
    use HasFactory;
    protected $primaryKey = 'idE'; // Clé primaire de la table

    protected $fillable = [
        'IFU',
        'nom',
        'logo',
        'adresse',
        'telephone',
        'mail',
        'RCCM',
        'regime',
        'idParent',
    ];


    public function parent()
    {
        return $this->belongsTo(Entreprise::class, 'idParent');
    }

    // Définition de la relation enfants
    public function enfants()
    {
        return $this->hasMany(Entreprise::class, 'idParent');
    }

    public function utilisateur() {
        return $this->hasMany(Utilisateur::class, 'idE');
    }

    public function magasin() {
        return $this->hasMany(Magasin::class, 'idE');
    }

    public function commandeAchat() {
        return $this->hasMany(CommandeAchat::class, 'idE');
    }

    public function vente() {
        return $this->hasMany(Vente::class, 'idE');
    }
}
