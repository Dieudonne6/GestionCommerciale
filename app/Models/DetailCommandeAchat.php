<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;
use App\Models\CommandeAchat;
use App\Models\DetailReceptionCmdAchat;

class DetailCommandeAchat extends Model
{
    use HasFactory;

    protected $table = 'detail_commande_achats';
    protected $primaryKey = 'idDetailCom';

    protected $fillable = [
        'idDetailCom',
        'qteCmd',
        'prixUnit',
        'montantHT',
        'montantTTC',
        'TVA',
        'idPro',
        'idCommande',
        'qteRestante'
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'idPro');
    }

    public function commandeAchat()
    {
        return $this->belongsTo(CommandeAchat::class, 'idCommande', 'idCommande');
    }

    public function detailReceptionCmdAchat()
    {
        return $this->hasMany(DetailReceptionCmdAchat::class, 'idDetailCom');
    }
}