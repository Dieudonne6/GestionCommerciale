<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Magasin;
use App\Models\Produit;

class Stocke extends Model
{
    use HasFactory;
    
    protected $table = 'stockes';

    protected $primaryKey = 'idStocke'; // Clé primaire de la table

    protected $fillable = [
        'qteStocke',
        'CUMP',
        'idPro',
        'idMag',
    ];

    public function produit() {
        return $this->belongsTo(Produit::class, 'idPro');
    }

    public function magasin() {
        return $this->belongsTo(Magasin::class, 'idMag');
    }
}
