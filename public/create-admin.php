<?php
// Create admin user script
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    // Check if admin role exists
    $adminRole = DB::table('roles')->where('nomRole', 'admin')->first();
    
    if (!$adminRole) {
        echo "Creating admin role...\n";
        $adminRoleId = DB::table('roles')->insertGetId([
            'nomRole' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Admin role created with ID: {$adminRoleId}\n\n";
    } else {
        $adminRoleId = $adminRole->idRole;
        echo "✓ Admin role already exists with ID: {$adminRoleId}\n\n";
    }
    
    // Check if admin user exists
    $adminEmail = 'admin@cartracking.com';
    $existingUser = DB::table('users')->where('email', $adminEmail)->first();
    
    if ($existingUser) {
        echo "Admin user already exists!\n";
        echo "Email: {$adminEmail}\n";
        echo "Updating password to: admin123\n\n";
        
        DB::table('users')->where('email', $adminEmail)->update([
            'password' => Hash::make('admin123'),
            'updated_at' => now(),
        ]);
        
        echo "✓ Password updated successfully!\n";
    } else {
        echo "Creating new admin user...\n";
        $userId = DB::table('users')->insertGetId([
            'nom' => 'Admin',
            'prenom' => 'System',
            'email' => $adminEmail,
            'password' => Hash::make('admin123'),
            'role_id' => $adminRoleId,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "✓ Admin user created with ID: {$userId}\n";
    }
    
    echo "\n=== Admin Credentials ===\n";
    echo "Email: admin@cartracking.com\n";
    echo "Password: admin123\n";
    echo "========================\n\n";
    echo "You can now log in with these credentials!\n";
    
} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
