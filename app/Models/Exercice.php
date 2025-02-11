<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Proforma;
use App\Models\CommandeAchat;
use App\Models\Inventaire;
use App\Models\Vente;
use App\Models\ReceptionCmdAchat;


class Exercice extends Model
{
    use HasFactory;
    protected $primaryKey = 'idExercice'; // Clé primaire de la table

    protected $fillable = [
        'annee',
        'designation',
        'dateDebut',
        'dateFin',
        'statutExercice',
    ];

    public function proforma() {
        return $this->hasMany(Proforma::class, 'idC');
    }

    
    public function commandeAchat() {
        return $this->hasMany(CommandeAchat::class, 'idExercice');
    }

    public function inventaire() {
        return $this->hasMany(Inventaire::class, 'idExercice');
    }

    public function vente() {
        return $this->hasMany(Vente::class, 'idExercice');
    }

    public function receptionCmdAchat() {
        return $this->hasMany(ReceptionCmdAchat::class, 'idExercice');
    }
}
