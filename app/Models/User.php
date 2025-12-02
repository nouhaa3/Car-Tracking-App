<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nom', 
        'prenom', 
        'email', 
        'password', 
        'role_id', 
        'avatar',
        'approval_status',
        'approval_date',
        'approved_by'
    ];

    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }
    
    protected $hidden = ['password', 'remember_token'];

    public function voitures() {
        return $this->hasMany(Voiture::class, 'user_id');
    }
    
}

