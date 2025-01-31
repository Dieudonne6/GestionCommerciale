<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Commande;

class Fournisseur extends Model
{
    use HasFactory;
    protected $primaryKey = 'idF'; // Clé primaire de votre table
    public $timestamps = false;

    protected $fillable = [
        'identiteF',
        // 'PrenomF',
        'AdresseF',
        'ContactF',
    ];
    public function commandes()
{
    return $this->hasMany(Commande::class, 'idF', 'idF');
}

}

