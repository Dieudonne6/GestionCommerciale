<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Exercice;
use App\Models\Client;
use App\Models\DetailProforma;

class Proforma extends Model
{
    use HasFactory;
    protected $primaryKey = 'idProforma'; // Clé primaire de la table

    protected $fillable = [
        'dateOperation',
        'reference',
        'nomClient',
        'telClient',
        'montantTotal',
        'idC',
        'idExercice',
    ];

    public function exercice() {
        return $this->belongsTo(Exercice::class, 'idExercice');
    }

    public function client() {
        return $this->belongsTo(Client::class, 'idC');
    }

    public function detailProforma() {
        return $this->hasMany(DetailProforma::class, 'idProforma');
    }
}
