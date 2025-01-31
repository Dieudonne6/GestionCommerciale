<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $primaryKey = 'idC'; // Clé primaire de votre table
    public $timestamps = false;

    protected $fillable = [
        'NomC',
        'codeC',
        'imgC',
    ];
}
