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



    // role

   public function role()
    {
        return $this->belongsTo(\App\Models\Role::class, 'idRole');
    }

    public function hasRole($role)
    {
        if (!$this->role) return false;
        return strtolower(trim($this->role->libelle)) === strtolower(trim($role));
    }

    public function hasAnyRole(array $roles)
    {
        if (!$this->role) return false;
        $lib = strtolower(trim($this->role->libelle));
        $roles = array_map(fn($r) => strtolower(trim($r)), $roles);
        return in_array($lib, $roles, true);
    }


    public function canAnyMenu(array $menus, string $action = 'view'): bool
    {
        if (!$this->role) return false;

        return \App\Models\Menu::whereIn('code', $menus)
            ->whereHas('roles', function ($q) use ($action) {
                $q->where('roles.idRole', $this->idRole)
                ->where("can_$action", 1);
            })
            ->exists();
    }
}
