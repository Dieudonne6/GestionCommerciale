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

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_role', 'idRole', 'menu_id')
            ->withPivot(['can_view','can_create','can_edit','can_delete'])
            ->withTimestamps();
    }
}
