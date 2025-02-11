<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Magasin;
use App\Models\Exercice;
use App\Models\DetailInventaire;


class Inventaire extends Model
{
    use HasFactory;

    protected $primaryKey = 'idInventaire'; // Clé primaire de la table

    protected $fillable = [
        'dateInv',
        'numeroInv',
        'description',
        'statutInv',
        'idMag',
        'idExercice',
    ];

    public function magasin() {
        return $this->belongsTo(Magasin::class, 'idMag');
    }

    public function exercice() {
        return $this->belongsTo(Exercice::class, 'idExercice');
    }

    public function detailInventaire() {
        return $this->hasMany(DetailInventaire::class, 'idInventaire');
    }
}
