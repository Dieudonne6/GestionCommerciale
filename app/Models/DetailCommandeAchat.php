<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;
use App\Models\Commande;
use App\Models\DetailReceptionCmdAchat;

class DetailCommandeAchat extends Model
{
    use HasFactory;

    protected $primaryKey = 'idDetailCom'; // Clé primaire de la table

    protected $fillable = [
        'qteCmd',
        'prixUnit',
        'montantHT',
        'montantTTC',
        'qteRestante',
        'idCommande',
        'idPro',
    ];

    public function produit() {
        return $this->belongsTo(Produit::class, 'idPro');
    }

    public function commande() {
        return $this->belongsTo(Commande::class, 'idCommande');
    }

    public function detailReceptionCmdAchat() {
        return $this->hasMany(DetailReceptionCmdAchat::class, 'idDetailCom');
    }
}
