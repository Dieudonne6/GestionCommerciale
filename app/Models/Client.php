<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $primaryKey = 'idCl'; // Clé primaire de votre table

    protected $fillable = [
        'identiteCl',
        // 'PrenomCl',
        'AdresseCl',
        'ContactCl',
    ];
}
