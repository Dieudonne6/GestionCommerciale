<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role;
use App\Models\Entreprise;
use App\Models\CommandeAchat;
use App\Models\Vente;
use App\Models\ReceptionCmdAchat;

  

class Utilisateur extends Model
{
    use HasFactory;
    protected $primaryKey = 'idU'; // Clé primaire de la table

    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'mail',
        'idRole',
        'idE',
    ];

    public function role() {
        return $this->belongsTo(Role::class, 'idRole');
    }

    public function entreprise() {
        return $this->belongsTo(Entreprise::class, 'idE');
    }

    public function commandeAchat() {
        return $this->hasMany(CommandeAchat::class, 'idU');
    }

    public function vente() {
        return $this->hasMany(Vente::class, 'idU');
    }

    public function receptionCmdAchat() {
        return $this->hasMany(ReceptionCmdAchat::class, 'idU');
    }
}