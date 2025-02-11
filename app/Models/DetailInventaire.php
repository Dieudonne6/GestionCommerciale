<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventaire;
use App\Models\Produit;

class DetailInventaire extends Model
{
    use HasFactory;

    protected $primaryKey = 'idDetailInv'; // Clé primaire de la table

    protected $fillable = [
        'stockTheorique',
        'stockReel',
        'prixUnit',
        'idInventaire',
        'idPro',
    ];


    public function inventaire()
    {
        return $this->belongsTo(Inventaire::class, 'idInventaire');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'idPro');
    }
}
