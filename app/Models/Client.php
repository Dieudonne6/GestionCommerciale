<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategorieClient;
use App\Models\Proforma;
use App\Models\Vente;


class Client extends Model
{
    use HasFactory;
    protected $primaryKey = 'idC'; // Clé primaire de la table

    protected $fillable = [
        'IFU',
        'nom',
        'adresse',
        'telephone',
        'mail',
        'idCatCl',
    ];

    public function categorieClient() {
        return $this->belongsTo(CategorieClient::class, 'idCatCl');
    }

    public function proforma() {
        return $this->hasMany(Proforma::class, 'idC');
    }

    public function vente() {
        return $this->hasMany(Vente::class, 'idC');
    }
}
