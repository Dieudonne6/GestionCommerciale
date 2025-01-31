<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reception extends Model
{
    use HasFactory;
     
    protected $table = 'receptions'; // Nom de la table
    protected $primaryKey = 'idReception'; // Clé primaire de votre table
    public $timestamps = false;

    protected $fillable = [
        'numReception',
        'dateReception',
        'RefNumBonReception',
        'idCmd',
        'idE',
    ];
}