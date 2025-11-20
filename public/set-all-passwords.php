<?php
// Set passwords for all users based on their names
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    echo "=================================================\n";
    echo "      SETTING PASSWORDS FOR ALL USERS\n";
    echo "=================================================\n\n";
    
    $users = DB::table('users')->select('id', 'prenom', 'nom', 'email')->get();
    
    if ($users->isEmpty()) {
        echo "No users found.\n";
        exit(1);
    }
    
    echo "Setting passwords...\n\n";
    echo sprintf("%-30s %-30s %-20s\n", "Email", "Password", "Status");
    echo str_repeat("-", 80) . "\n";
    
    foreach ($users as $user) {
        // Create password: firstname + 123
        // Example: Mohamed -> mohamed123
        $password = strtolower($user->prenom) . '123';
        
        // Update password
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($password),
                'updated_at' => now(),
            ]);
        
        echo sprintf("%-30s %-30s %-20s\n", $user->email, $password, "✓ Updated");
    }
    
    echo "\n" . str_repeat("=", 80) . "\n\n";
    echo "✅ All passwords have been set successfully!\n\n";
    echo "Users can now log in with:\n";
    echo "  Email: their email address\n";
    echo "  Password: firstname123 (lowercase)\n\n";
    echo "Examples:\n";
    echo "  - Mohamed Ben Ali → mohamed123\n";
    echo "  - Sami Trabelsi → sami123\n";
    echo "  - Ons Jelassi → ons123\n\n";
    
} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
