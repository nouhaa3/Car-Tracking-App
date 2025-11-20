<?php
// List all users in the database
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=================================================\n";
    echo "           EXISTING USERS IN DATABASE\n";
    echo "=================================================\n\n";
    
    $users = DB::table('users')
        ->leftJoin('roles', 'users.role_id', '=', 'roles.idRole')
        ->select('users.*', 'roles.nomRole')
        ->get();
    
    if ($users->isEmpty()) {
        echo "No users found in database.\n\n";
        echo "Would you like to create an admin user? (yes/no): ";
    } else {
        echo "Total Users: " . count($users) . "\n\n";
        echo sprintf("%-5s %-20s %-20s %-30s %-15s\n", "ID", "First Name", "Last Name", "Email", "Role");
        echo str_repeat("-", 95) . "\n";
        
        foreach ($users as $user) {
            echo sprintf(
                "%-5s %-20s %-20s %-30s %-15s\n",
                $user->id,
                $user->prenom,
                $user->nom,
                $user->email,
                $user->nomRole ?? 'N/A'
            );
        }
        
        echo "\n" . str_repeat("=", 95) . "\n\n";
        echo "You can log in with any of these email addresses.\n";
        echo "If you forgot the password, I can reset it for you.\n\n";
        echo "To reset a password, run: php public/reset-password.php [email]\n";
    }
    
} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
