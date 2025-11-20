<?php
// Reset password for a specific user
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Get email from command line argument
$email = $argv[1] ?? null;

if (!$email) {
    echo "Usage: php public/reset-password.php [email]\n";
    echo "Example: php public/reset-password.php user@example.com\n\n";
    exit(1);
}

try {
    // Find user
    $user = DB::table('users')->where('email', $email)->first();
    
    if (!$user) {
        echo "✗ User not found with email: {$email}\n";
        echo "\nRun 'php public/list-users.php' to see all users.\n";
        exit(1);
    }
    
    // Reset password to default
    $newPassword = 'password123';
    
    DB::table('users')
        ->where('email', $email)
        ->update([
            'password' => Hash::make($newPassword),
            'updated_at' => now(),
        ]);
    
    echo "=================================================\n";
    echo "           PASSWORD RESET SUCCESSFUL\n";
    echo "=================================================\n\n";
    echo "User: {$user->prenom} {$user->nom}\n";
    echo "Email: {$email}\n";
    echo "New Password: {$newPassword}\n\n";
    echo "You can now log in with these credentials!\n";
    echo "=================================================\n";
    
} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
