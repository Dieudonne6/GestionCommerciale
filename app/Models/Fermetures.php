<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fermetures extends Model
{
    use HasFactory;

    protected $table = 'fermetures';
    protected $primaryKey = 'idfermeture';

    protected $fillable = [
        'qtestock',
        'idPro',
        'idU',
        'date',
        'heure',
    ];

    public $timestamps = true;

    public function stock()
    {
        return $this->belongsTo(Stocke::class, 'idPro', 'idPro');
    }

    // relation avec utilisateurs
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'idU', 'idU');
    }
}
