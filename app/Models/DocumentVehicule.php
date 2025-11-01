<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVehicule extends Model
{
    use HasFactory;

    protected $table = 'documents_vehicule';
    protected $primaryKey = 'idDocument';

    protected $fillable = [
        'voiture_id',
        'type',
        'nom_fichier',
        'chemin',
        'date_expiration',
        'notes'
    ];

    protected $casts = [
        'date_expiration' => 'datetime',
    ];

    public function voiture()
    {
        return $this->belongsTo(Voiture::class, 'voiture_id', 'idVoiture');
    }

    public function isExpired()
    {
        return $this->date_expiration && $this->date_expiration->isPast();
    }

    public function isExpiringSoon($days = 30)
    {
        if (!$this->date_expiration) {
            return false;
        }
        
        return $this->date_expiration->isFuture() 
            && $this->date_expiration->diffInDays(now()) <= $days;
    }
}
