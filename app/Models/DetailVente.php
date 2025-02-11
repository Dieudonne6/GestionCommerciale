<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vente;
use App\Models\Produit;

class DetailVente extends Model
{
    use HasFactory;

    protected $primaryKey = 'idDetailV'; // Clé primaire de la table

    protected $fillable = [
        'qte',
        'prixUnit',
        'montantHT',
        'montantTTC',
        'idV',
        'idPro',
    ];


    public function vente()
    {
        return $this->belongsTo(Vente::class, 'idV');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'idPro');
    }
}
