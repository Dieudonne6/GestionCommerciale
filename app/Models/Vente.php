<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Utilisateur;
use App\Models\Exercice;
use App\Models\ModePaiement;
use App\Models\Entreprise;
use App\Models\DetailVente;
use App\Models\FactureNormalisee;


class Vente extends Model
{
    use HasFactory;

    
    protected $primaryKey = 'idV'; // Clé primaire de la table

    protected $fillable = [
        'dateOperation',
        'montantTotal',
        'reference',
        'statutVente',
        'idC',
        'idU',
        'idExercice',
        'idModPaie',
        'idE',
    ];

    public function client() {
        return $this->belongsTo(Client::class, 'idC');
    }

    public function utilisateur() {
        return $this->belongsTo(Utilisateur::class, 'idU');
    }

    public function exercice() {
        return $this->belongsTo(Exercice::class, 'idExercice');
    }

    public function modePaiement() {
        return $this->belongsTo(ModePaiement::class, 'idModPaie');
    }

    public function entreprise() {
        return $this->belongsTo(Entreprise::class, 'idE');
    }

    public function detailVente() {
        return $this->hasMany(DetailVente::class, 'idV');
    }

    public function factureNormalise() {
        return $this->hasMany(FactureNormalisee::class, 'idV');
    }
}
