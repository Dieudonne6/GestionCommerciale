<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DetailCommandeAchat;
use App\Models\ReceptionCmdAchat;

class DetailReceptionCmdAchat extends Model
{
    use HasFactory;

    protected $primaryKey = 'idDetailRecepCmdAchat'; // Clé primaire de la table

    protected $fillable = [
        'qteReceptionne',
        'prixUnit',
        'idRecep',
        'idDetailCom',
    ];

    public function detailCommandeAchat() {
        return $this->belongsTo(DetailCommandeAchat::class, 'idDetailCom');
    }

    public function receptionCmdAchat() {
        return $this->belongsTo(ReceptionCmdAchat::class, 'idRecep');
    }
}
