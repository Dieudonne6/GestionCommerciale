<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vente;

class ModePaiement extends Model
{
    use HasFactory;
    protected $table = 'mode_paiements'; // Nom de la table
    protected $primaryKey = 'idModPaie'; // Clé primaire de la table

    protected $fillable = [
        'libelle',
    ];

    public function vente() {
        return $this->hasMany(Vente::class, 'idModPaie');
    }
}
