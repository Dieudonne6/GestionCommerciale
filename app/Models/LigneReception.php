<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneReception extends Model
{
    use HasFactory;

    protected $primaryKey = 'idLReception'; // Clé primaire de votre table

    protected $fillable = [
        'qteReception',
        'lastStock',
        'lastCUMP',
        'prixUn',
        'idReception',
    ];

    public function produit()
{
    return $this->belongsTo(Produit::class, 'idP', 'idP');
}
}