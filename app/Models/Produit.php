<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategorieProduit;
use App\Models\FamilleProduit;
use App\Models\DetailTransfertMagasin;
use App\Models\Stocke;
use App\Models\DetailInventaire;
use App\Models\DetailVente;
use App\Models\DetailCommandeAchat;
use App\Models\DetailProforma;
use App\Models\Magasin;


class Produit extends Model
{
    use HasFactory;

    protected $primaryKey = 'idPro'; // Clé primaire de la table

    protected $fillable = [
        'libelle',
        'prix',
        'marge',
        'prixAchatTheorique',
        'desc',
        'image',
        'stockAlert',
        'stockMinimum',
        'idCatPro',
        'idFamPro',
    ];

    public function categorieProduit() {
        return $this->belongsTo(CategorieProduit::class, 'idCatPro');
    }

    public function familleProduit() {
        return $this->belongsTo(FamilleProduit::class, 'idFamPro');
    }

    public function datailTransfertMag() {
        return $this->hasMany(DetailTransfertMagasin::class, 'idPro');
    }

    public function stocke() {
        return $this->hasOne(Stocke::class, 'idPro');
    }

    public function detailInventaire() {
        return $this->hasMany(DetailInventaire::class, 'idPro');
    }

    public function detailVente() {
        return $this->hasMany(DetailVente::class, 'idPro');
    }

    public function detailCommandeAchat() {
        return $this->hasMany(DetailCommandeAchat::class, 'idPro');
    }

    public function detailProforma() {
        return $this->hasMany(DetailProforma::class, 'idPro');
    }

}
