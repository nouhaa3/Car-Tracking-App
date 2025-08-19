<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    use HasFactory;

    protected $primaryKey = 'idIntervention';
    protected $fillable = ['type', 'date', 'cout', 'garage', 'remarques', 'voiture_id'];

    public function voiture() {
        return $this->belongsTo(Voiture::class, 'voiture_id');
    }

    public function documents() {
        return $this->hasMany(Document::class, 'intervention_id');
    }
}