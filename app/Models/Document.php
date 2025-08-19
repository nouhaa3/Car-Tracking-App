<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $primaryKey = 'idDocument';
    protected $fillable = ['nomFichier', 'typeFichier', 'chemin', 'dateUpload', 'intervention_id'];

    public function intervention() {
        return $this->belongsTo(Intervention::class, 'intervention_id');
    }
}