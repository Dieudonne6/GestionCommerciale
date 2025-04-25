<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Entreprise;
use App\Models\Stocke;
use App\Models\Inventaire;
use App\Models\TransfertMagasin;
use App\Models\Produit;



class Magasin extends Model
{
    use HasFactory;

    protected $primaryKey = 'idMag'; // Clé primaire de la table

    protected $fillable = [
        'libelle',
        'codeMagasin',
        'Adresse',
        'idE',
    ];


    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'idE');
    }

    public function stocke() {
        return $this->hasMany(Stocke::class, 'idMag');
    }

    public function inventaire() {
        return $this->hasMany(Inventaire::class, 'idMag');
    }

    public function transfertMagasin() {
        return $this->hasMany(TransfertMagasin::class, 'idMag');
    }

    
}
