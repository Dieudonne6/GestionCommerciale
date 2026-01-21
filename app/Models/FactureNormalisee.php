<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vente;
use App\Models\CommandeAchat;

class FactureNormalisee extends Model
{
    use HasFactory;

    protected $primaryKey = 'idFacture'; // Clé primaire de la table

    protected $fillable = [
        'itemFacture',
        'CODEMECEF',
        'CODEMECEFfacOriginale',
        'nim',
        'counter',
        'montantTotal',
        'montantTotalTTC',
        'TotalTVA',
        'groupeTaxation',
        'statut',
        'qrcode',
        'date',
        'idV',
        'idCommande',
        'regime',
        'idV',
    ];

    public function vente() {
        return $this->belongsTo(Vente::class, 'idV');
    }

    public function commandeAchat() {
        return $this->belongsTo(CommandeAchat::class, 'idCommande');
    }
}
