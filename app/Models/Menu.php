<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    // use HasFactory;

    protected $fillable = ['code','label','route'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'menu_role', 'menu_id', 'idRole')
            ->withPivot(['can_view','can_create','can_edit','can_delete'])
            ->withTimestamps();
    }
}
