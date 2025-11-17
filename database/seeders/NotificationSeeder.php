<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user (or create sample notifications for all users)
        $user = User::first();
        
        if (!$user) {
            $this->command->warn('No users found. Please create a user first.');
            return;
        }

        $notifications = [
            [
                'user_id' => $user->id,
                'type' => 'alert',
                'title' => 'Maintenance requise',
                'message' => 'Le véhicule ABC-123 nécessite une maintenance dans 3 jours',
                'link' => '/voitures/cataloguevoitures',
                'read' => false,
            ],
            [
                'user_id' => $user->id,
                'type' => 'success',
                'title' => 'Intervention terminée',
                'message' => 'L\'intervention #1234 a été marquée comme terminée',
                'link' => '/interventions/catalogue',
                'read' => false,
            ],
            [
                'user_id' => $user->id,
                'type' => 'warning',
                'title' => 'Alerte kilométrage',
                'message' => 'Le véhicule XYZ-789 a dépassé 100 000 km',
                'link' => '/alertes',
                'read' => true,
            ],
            [
                'user_id' => $user->id,
                'type' => 'message',
                'title' => 'Nouveau message',
                'message' => 'Vous avez reçu un nouveau message de l\'équipe',
                'link' => null,
                'read' => false,
            ],
            [
                'user_id' => $user->id,
                'type' => 'system',
                'title' => 'Mise à jour système',
                'message' => 'Le système sera mis à jour ce soir à 22h00',
                'link' => null,
                'read' => true,
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }

        $this->command->info('Sample notifications created successfully!');
    }
}
