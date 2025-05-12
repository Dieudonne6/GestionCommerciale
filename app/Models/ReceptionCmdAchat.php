<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Exercice;
use App\Models\CommandeAchat;
use App\Models\Utilisateur;
use App\Models\DetailReceptionCmdAchat;

class ReceptionCmdAchat extends Model
{
    use HasFactory;

    protected $table = 'reception_cmd_achats';
    protected $primaryKey = 'idRecep'; // Clé primaire de la table

    protected $fillable = [
        'date',
        'reference',
        'numBordereauLivraison',
        'statutRecep',
        'idExercice',
        'idCommande',
        'idU',
    ];


    public function exercice()
    {
        return $this->belongsTo(Exercice::class, 'idExercice');
    }

    public function commandeAchat()
    {
        return $this->belongsTo(CommandeAchat::class, 'idCommande');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'idU');
    }

    public function detailReceptionCmdAchat() {
        return $this->hasMany(DetailReceptionCmdAchat::class, 'idRecep');
    }
}