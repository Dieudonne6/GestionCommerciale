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
        'idMagSource',
    ];

    public function magasin() {
        return $this->belongsTo(Magasin::class, 'idMag');
    }
    
    public function magasinSource() {
        return $this->belongsTo(Magasin::class, 'idMagSource');
    }
    
    public function details() {
        return $this->hasMany(DetailTransfertMagasin::class, 'idTransMag');
    }
}
