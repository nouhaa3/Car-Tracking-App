<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\DocumentVehicule;
use App\Models\Alerte;
use App\Models\Intervention;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public function createNotification($userId, $type, $title, $message, $metadata = [], $priority = 'medium')
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'metadata' => $metadata,
            'priority' => $priority
        ]);
    }

    /**
     * Create notification for multiple users
     */
    public function notifyUsers($userIds, $type, $title, $message, $metadata = [], $priority = 'medium')
    {
        $notifications = [];
        
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'metadata' => $metadata,
                'priority' => $priority,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        Notification::insert($notifications);
    }

    /**
     * Notify all admins
     */
    public function notifyAdmins($type, $title, $message, $metadata = [], $priority = 'medium')
    {
        $adminIds = User::whereHas('role', function($query) {
            $query->where('nomRole', 'Admin');
        })->pluck('idUser')->toArray();
        
        if (!empty($adminIds)) {
            $this->notifyUsers($adminIds, $type, $title, $message, $metadata, $priority);
        }
    }

    /**
     * Check for expiring documents and create notifications
     */
    public function checkExpiringDocuments()
    {
        $now = now();
        $thirtyDaysFromNow = $now->copy()->addDays(30);
        
        // Documents expiring in 30 days (first warning)
        $expiringDocs = DocumentVehicule::whereNotNull('date_expiration')
            ->whereBetween('date_expiration', [$now->addDays(29), $thirtyDaysFromNow])
            ->with('voiture.user')
            ->get();
        
        foreach ($expiringDocs as $doc) {
            if ($doc->voiture && $doc->voiture->user) {
                $daysRemaining = $now->diffInDays($doc->date_expiration, false);
                
                $this->createNotification(
                    $doc->voiture->user->idUser,
                    'document_expiring',
                    'Document expire bientôt',
                    "Le document '{$doc->type}' du véhicule {$doc->voiture->marque} {$doc->voiture->modele} expire dans {$daysRemaining} jours",
                    [
                        'document_id' => $doc->idDocument,
                        'voiture_id' => $doc->voiture->idVoiture,
                        'days_remaining' => $daysRemaining,
                        'link' => "/voitures/{$doc->voiture->idVoiture}"
                    ],
                    'high'
                );
            }
        }
        
        // Documents expiring in 7 days (urgent warning)
        $urgentDocs = DocumentVehicule::whereNotNull('date_expiration')
            ->whereBetween('date_expiration', [$now->addDays(6), $now->addDays(7)])
            ->with('voiture.user')
            ->get();
        
        foreach ($urgentDocs as $doc) {
            if ($doc->voiture && $doc->voiture->user) {
                $this->createNotification(
                    $doc->voiture->user->idUser,
                    'document_expiring',
                    '⚠️ Document expire dans 7 jours',
                    "URGENT: Le document '{$doc->type}' du véhicule {$doc->voiture->marque} {$doc->voiture->modele} expire dans 7 jours",
                    [
                        'document_id' => $doc->idDocument,
                        'voiture_id' => $doc->voiture->idVoiture,
                        'days_remaining' => 7,
                        'link' => "/voitures/{$doc->voiture->idVoiture}"
                    ],
                    'critical'
                );
            }
        }
        
        // Expired documents
        $expiredDocs = DocumentVehicule::whereNotNull('date_expiration')
            ->whereDate('date_expiration', '<', $now)
            ->with('voiture.user')
            ->get();
        
        foreach ($expiredDocs as $doc) {
            if ($doc->voiture && $doc->voiture->user) {
                // Check if we haven't already sent an expired notification today
                $existingNotification = Notification::where('user_id', $doc->voiture->user->idUser)
                    ->where('type', 'document_expired')
                    ->whereJsonContains('metadata->document_id', $doc->idDocument)
                    ->whereDate('created_at', $now->toDateString())
                    ->first();
                
                if (!$existingNotification) {
                    $this->createNotification(
                        $doc->voiture->user->idUser,
                        'document_expired',
                        '🚨 Document expiré',
                        "Le document '{$doc->type}' du véhicule {$doc->voiture->marque} {$doc->voiture->modele} est expiré",
                        [
                            'document_id' => $doc->idDocument,
                            'voiture_id' => $doc->voiture->idVoiture,
                            'link' => "/voitures/{$doc->voiture->idVoiture}"
                        ],
                        'critical'
                    );
                }
            }
        }
    }

    /**
     * Notify when a critical alert is generated
     */
    public function notifyAlertGenerated(Alerte $alerte)
    {
        if ($alerte->priorite === 'critique' && $alerte->voiture && $alerte->voiture->user) {
            $this->createNotification(
                $alerte->voiture->user->idUser,
                'alert_critical',
                '🚨 Alerte critique générée',
                "Une alerte critique '{$alerte->type}' a été générée pour {$alerte->voiture->marque} {$alerte->voiture->modele}",
                [
                    'alerte_id' => $alerte->idAlerte,
                    'voiture_id' => $alerte->voiture->idVoiture,
                    'link' => "/alertes/{$alerte->idAlerte}"
                ],
                'critical'
            );
            
            // Also notify admins
            $this->notifyAdmins(
                'alert_critical',
                '🚨 Alerte critique système',
                "Alerte critique '{$alerte->type}' pour {$alerte->voiture->marque} {$alerte->voiture->modele}",
                [
                    'alerte_id' => $alerte->idAlerte,
                    'voiture_id' => $alerte->voiture->idVoiture,
                    'link' => "/alertes/{$alerte->idAlerte}"
                ],
                'critical'
            );
        } elseif ($alerte->voiture && $alerte->voiture->user) {
            $this->createNotification(
                $alerte->voiture->user->idUser,
                'alert_generated',
                'Nouvelle alerte',
                "Une alerte '{$alerte->type}' a été générée pour {$alerte->voiture->marque} {$alerte->voiture->modele}",
                [
                    'alerte_id' => $alerte->idAlerte,
                    'voiture_id' => $alerte->voiture->idVoiture,
                    'link' => "/alertes/{$alerte->idAlerte}"
                ],
                'medium'
            );
        }
    }

    /**
     * Notify when intervention is created
     */
    public function notifyInterventionCreated(Intervention $intervention)
    {
        if ($intervention->voiture && $intervention->voiture->user) {
            $this->createNotification(
                $intervention->voiture->user->idUser,
                'intervention_created',
                'Nouvelle intervention planifiée',
                "Une intervention '{$intervention->type}' a été planifiée pour {$intervention->voiture->marque} {$intervention->voiture->modele}",
                [
                    'intervention_id' => $intervention->idIntervention,
                    'voiture_id' => $intervention->voiture->idVoiture,
                    'link' => "/interventions/{$intervention->idIntervention}"
                ],
                'medium'
            );
        }
    }

    /**
     * Notify when intervention is completed
     */
    public function notifyInterventionCompleted(Intervention $intervention)
    {
        if ($intervention->voiture && $intervention->voiture->user) {
            $this->createNotification(
                $intervention->voiture->user->idUser,
                'intervention_completed',
                '✅ Intervention terminée',
                "L'intervention '{$intervention->type}' pour {$intervention->voiture->marque} {$intervention->voiture->modele} est terminée",
                [
                    'intervention_id' => $intervention->idIntervention,
                    'voiture_id' => $intervention->voiture->idVoiture,
                    'link' => "/interventions/{$intervention->idIntervention}"
                ],
                'low'
            );
        }
    }

    /**
     * Check for upcoming interventions (within 3 days)
     */
    public function checkUpcomingInterventions()
    {
        $threeDaysFromNow = now()->addDays(3);
        
        $upcomingInterventions = Intervention::whereBetween('dateDebut', [now(), $threeDaysFromNow])
            ->where('statut', '!=', 'Terminée')
            ->with('voiture.user')
            ->get();
        
        foreach ($upcomingInterventions as $intervention) {
            if ($intervention->voiture && $intervention->voiture->user) {
                $daysUntil = now()->diffInDays($intervention->dateDebut, false);
                
                $this->createNotification(
                    $intervention->voiture->user->idUser,
                    'intervention_upcoming',
                    'Intervention à venir',
                    "L'intervention '{$intervention->type}' pour {$intervention->voiture->marque} {$intervention->voiture->modele} est prévue dans {$daysUntil} jours",
                    [
                        'intervention_id' => $intervention->idIntervention,
                        'voiture_id' => $intervention->voiture->idVoiture,
                        'days_until' => $daysUntil,
                        'link' => "/interventions/{$intervention->idIntervention}"
                    ],
                    'medium'
                );
            }
        }
    }

    /**
     * Send system announcement to all users
     */
    public function sendAnnouncement($title, $message, $priority = 'medium')
    {
        $allUserIds = User::pluck('idUser')->toArray();
        
        $this->notifyUsers(
            $allUserIds,
            'system_announcement',
            $title,
            $message,
            [],
            $priority
        );
    }

    /**
     * Clean up old read notifications (older than 30 days)
     */
    public function cleanupOldNotifications($days = 30)
    {
        Notification::where('is_read', true)
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }

    /**
     * Get notification statistics for user
     */
    public function getUserStats($userId)
    {
        return [
            'total' => Notification::forUser($userId)->count(),
            'unread' => Notification::forUser($userId)->unread()->count(),
            'critical' => Notification::forUser($userId)->unread()->critical()->count(),
            'recent' => Notification::forUser($userId)->recent(7)->count()
        ];
    }
}
