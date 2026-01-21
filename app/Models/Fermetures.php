<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DetailFermetures;


class Fermetures extends Model
{
    use HasFactory;

    protected $table = 'fermetures';
    protected $primaryKey = 'idFermeture';

    protected $fillable = ['idU', 'date', 'heure'];

    public function details()
    {
        return $this->hasMany(DetailFermetures::class, 'idFermeture');
    }
}

