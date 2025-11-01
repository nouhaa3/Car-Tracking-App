<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Intervention extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'idIntervention';
    protected $fillable = ['type', 'date', 'cout', 'garage', 'remarques', 'voiture_id', 'assigned_to'];

    public function voiture() {
        return $this->belongsTo(Voiture::class, 'voiture_id');
    }

    public function documents() {
        return $this->hasMany(Document::class, 'intervention_id');
    }

    public function assignedTo() {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}