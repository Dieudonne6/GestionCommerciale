<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Fournisseur;
use App\Models\Exercice;
use App\Models\Entreprise;
use App\Models\Utilisateur;
use App\Models\DetailCommandeAchat;
use App\Models\ReceptionCmdAchat;
use App\Models\FactureNormalisee;


class CommandeAchat extends Model
{
    use HasFactory;

    protected $primaryKey = 'idCommande'; // Clé primaire de la table

    protected $fillable = [
        'dateOp',
        'montantTotalHT',
        'montantTotalTTC',
        'reference',
        'delailivraison',
        'statutCom',
        'idF',
        'idExercice',
        'idE',
        'idU',
    ];


    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'idF');
    }

    public function exercice()
    {
        return $this->belongsTo(Exercice::class, 'idExercice');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'idE');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'idU');
    }

    public function detailCommandeAchat() {
        return $this->hasMany(DetailCommandeAchat::class, 'idCommande');
    }

    public function receptionCmdAchat() {
        return $this->hasMany(ReceptionCmdAchat::class, 'idCommande');
    }
    
    public function factureNormalise() {
        return $this->hasMany(FactureNormalisee::class, 'idCommande');
    }
}
