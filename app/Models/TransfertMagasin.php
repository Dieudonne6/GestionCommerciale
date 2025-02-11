<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Magasin;

class TransfertMagasin extends Model
{
    use HasFactory;

    protected $primaryKey = 'idTransMag'; // Clé primaire de la table

    protected $fillable = [
        'dateTransfert',
        'referenceTransfert',
        'idMag',
    ];

    public function magasin() {
        return $this->belongsTo(Magasin::class, 'idMag');
    }
}
