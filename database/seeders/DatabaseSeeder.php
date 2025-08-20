<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les rôle par défaut
        DB::table('roles')->insert([
            'idRole' => 1,
            'nomRole' => 'Admin',
        ]);

        DB::table('roles')->insert([
            'idRole' => 2,
            'nomRole' => 'Agent',
        ]);

        DB::table('roles')->insert([
            'idRole' => 3,
            'nomRole' => 'Technicien',
        ]);
        // Appeler UserSeeder
        $this->call(UserSeeder::class);
    }
}
