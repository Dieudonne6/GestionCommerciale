<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;


class CategorieClient extends Model
{
    use HasFactory;
    protected $primaryKey = 'idCatCl'; // Clé primaire de la table

    protected $fillable = ['codeCatCl', 'libelle'];

    public function client() {
        return $this->hasMany(Client::class, 'idC');
    }
}