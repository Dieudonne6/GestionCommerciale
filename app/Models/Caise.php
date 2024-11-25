<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caise extends Model
{
    use HasFactory;

    protected $primaryKey = 'idCais'; // Clé primaire de votre table

    protected $fillable = [
        'codeCais',
        'libelleCais',
    ];
}
