<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use App\Models\Entreprise;
use App\Models\CommandeAchat;
use App\Models\Vente;
use App\Models\ReceptionCmdAchat;

class Utilisateur extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'utilisateurs';
    protected $primaryKey = 'idU'; // Clé primaire de la table

    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'mail',
        'idRole',
        'password',
        'idE',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getAuthIdentifierName()
    {
        return 'idU';
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'idRole');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'idE');
    }

    public function commandeAchat()
    {
        return $this->hasMany(CommandeAchat::class, 'idU');
    }

    public function vente()
    {
        return $this->hasMany(Vente::class, 'idU');
    }

    public function receptionCmdAchat()
    {
        return $this->hasMany(ReceptionCmdAchat::class, 'idU');
    }
}
