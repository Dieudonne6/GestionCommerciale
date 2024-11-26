<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    // Indique que ces champs peuvent être remplis massivement
    protected $fillable = ['idC', 'NomC', 'imgC'];

    // Optionnel : si vous voulez que les timestamps (created_at, updated_at) soient gérés automatiquement
    public $timestamps = true;

    // Optionnel : si vous souhaitez une table différente (par défaut, Laravel prendra le nom de la table en pluriel)
    protected $table = 'Categories';
}
