<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
   public function run(): void
    {
        User::create([
            'nom' => 'Adem',
            'prenom' => 'Ben Mansour', // you can adjust
            'email' => 'admin@cartrackingapp.com',
            'password' => Hash::make('admin1234'), // default password
            'role_id' => 1, 
        ]);

        User::create([
            'nom' => 'Youssef',
            'prenom' => 'Salmi', // you can adjust
            'email' => 'agent@cartrackingapp.com',
            'password' => Hash::make('agent1234'), // default password
            'role_id' => 2, 
        ]);

        User::create([
            'nom' => 'Mohamed',
            'prenom' => 'Hamrouni', // you can adjust
            'email' => 'technicien@cartrackingapp.com',
            'password' => Hash::make('tech1234'), // default password
            'role_id' => 3, 
        ]);
    }
}
