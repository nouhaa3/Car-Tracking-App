<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerte extends Model
{
    use HasFactory;

    protected $primaryKey = 'idAlerte';
    protected $fillable = ['type', 'dateEchance', 'kilometrageEchance', 'statut', 'voiture_id'];

    public function voiture() {
        return $this->belongsTo(Voiture::class, 'voiture_id');
    }
}
