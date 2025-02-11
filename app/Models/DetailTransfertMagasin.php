<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produit;

class DetailTransfertMagasin extends Model
{
    use HasFactory;

    protected $primaryKey = 'idDetailTransMag'; // Clé primaire de la table

    protected $fillable = [
        'qteTransferer',
        'idPro',
    ];

    public function produit() {
        return $this->belongsTo(Produit::class, 'idPro');
    }
}
