<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voiture extends Model
{
    use HasFactory;

    protected $primaryKey = 'idVoiture';
    protected $fillable = ['marque', 'modele', 'annee', 'kilometrage', 'etat', 'statut', 'user_id'];
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/'.$this->image) : asset('images/default-car.png');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function alertes() {
        return $this->hasMany(Alerte::class, 'voiture_id');
    }

    public function interventions() {
        return $this->hasMany(Intervention::class, 'voiture_id');
    }
}
