<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Utilisateur;

class Role extends Model
{
    use HasFactory;
    protected $primaryKey = 'idRole'; // Clé primaire de la table

    protected $fillable = [
        'libelle',
    ];

    public function utilisateur() {
        return $this->hasMany(Utilisateur::class, 'idRole');
    }
}
