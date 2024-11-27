<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';
    protected $primaryKey = 'id'; // Clé primaire de votre table
    public $incrementing = true;

    protected $fillable = [
        'libelleRole',
    ];
}