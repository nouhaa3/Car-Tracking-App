<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'idRole';
    protected $fillable = ['nomRole', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function users() {
        return $this->hasMany(User::class, 'role_id');
    }
}