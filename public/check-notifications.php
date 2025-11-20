<?php
// Check and create test notifications
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=================================================\n";
    echo "         CHECKING NOTIFICATIONS\n";
    echo "=================================================\n\n";
    
    // Get admin user (Mohamed Ben Ali)
    $admin = DB::table('users')->where('email', 'm.benali@carapp.tn')->first();
    
    if (!$admin) {
        echo "✗ Admin user not found\n";
        exit(1);
    }
    
    echo "Admin User: {$admin->prenom} {$admin->nom} (ID: {$admin->id})\n\n";
    
    // Check existing notifications
    $notifications = DB::table('notifications')->where('user_id', $admin->id)->get();
    
    echo "Existing notifications: " . count($notifications) . "\n\n";
    
    if ($notifications->isEmpty()) {
        echo "No notifications found. Creating test notifications...\n\n";
        
        // Create test notifications
        $testNotifications = [
            [
                'type' => 'system_announcement',
                'title' => 'Bienvenue sur Car Tracking App',
                'message' => 'Système de gestion des véhicules opérationnel.',
                'priority' => 'medium',
            ],
            [
                'type' => 'alert_generated',
                'title' => 'Nouvelle alerte générée',
                'message' => 'Une alerte de maintenance a été générée pour un véhicule.',
                'priority' => 'high',
            ],
            [
                'type' => 'document_expiring',
                'title' => 'Document expirant bientôt',
                'message' => 'Un document de véhicule expire dans 7 jours.',
                'priority' => 'medium',
            ],
            [
                'type' => 'intervention_created',
                'title' => 'Nouvelle intervention créée',
                'message' => 'Une nouvelle intervention a été planifiée.',
                'priority' => 'low',
            ],
            [
                'type' => 'alert_critical',
                'title' => 'Alerte critique',
                'message' => 'Intervention urgente requise pour un véhicule.',
                'priority' => 'critical',
            ],
        ];
        
        foreach ($testNotifications as $notif) {
            DB::table('notifications')->insert([
                'user_id' => $admin->id,
                'type' => $notif['type'],
                'title' => $notif['title'],
                'message' => $notif['message'],
                'priority' => $notif['priority'],
                'is_read' => false,
                'metadata' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        echo "✓ Created " . count($testNotifications) . " test notifications\n\n";
    } else {
        echo "Notifications:\n";
        echo str_repeat("-", 80) . "\n";
        echo sprintf("%-5s %-40s %-20s %-10s\n", "ID", "Title", "Type", "Read");
        echo str_repeat("-", 80) . "\n";
        
        foreach ($notifications as $notif) {
            $id = $notif->idNotification ?? $notif->id ?? 'N/A';
            $title = isset($notif->title) ? substr($notif->title, 0, 38) : 'No title';
            $type = $notif->type ?? 'unknown';
            
            // Check for is_read or read_at
            $isRead = 'N/A';
            if (isset($notif->is_read)) {
                $isRead = $notif->is_read ? 'Yes' : 'No';
            } elseif (isset($notif->read_at)) {
                $isRead = $notif->read_at ? 'Yes' : 'No';
            }
            
            echo sprintf(
                "%-5s %-40s %-20s %-10s\n",
                $id,
                $title,
                $type,
                $isRead
            );
        }
        echo "\n";
    }
    
    // Final count
    $finalCount = DB::table('notifications')->where('user_id', $admin->id)->count();
    
    // Check if read_at column exists for unread count
    $columns = DB::select("DESCRIBE notifications");
    $hasReadAt = false;
    $hasIsRead = false;
    
    foreach ($columns as $col) {
        if ($col->Field === 'read_at') $hasReadAt = true;
        if ($col->Field === 'is_read') $hasIsRead = true;
    }
    
    $unreadCount = 0;
    if ($hasIsRead) {
        $unreadCount = DB::table('notifications')->where('user_id', $admin->id)->where('is_read', 0)->count();
    } elseif ($hasReadAt) {
        $unreadCount = DB::table('notifications')->where('user_id', $admin->id)->whereNull('read_at')->count();
    }
    
    echo "=================================================\n";
    echo "Total notifications: {$finalCount}\n";
    if ($hasReadAt || $hasIsRead) {
        echo "Unread notifications: {$unreadCount}\n";
    }
    echo "=================================================\n\n";
    
    echo "✅ Check complete! Log in as Mohamed to see notifications.\n";
    
} catch (\Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
