<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Proforma;
use App\Models\Produit;


class DetailProforma extends Model
{
    use HasFactory;

    protected $primaryKey = 'idDetailProforma'; // Clé primaire de la table

    protected $fillable = [
        'qteProforma',
        'prixUnit',
        'montantHT',
        'montantTTC',
        'idProforma',
        'idPro',
    ];

    public function proforma() {
        return $this->belongsTo(Proforma::class, 'idProforma');
    }

    public function produit() {
        return $this->belongsTo(Produit::class, 'idPro');
    }
}
