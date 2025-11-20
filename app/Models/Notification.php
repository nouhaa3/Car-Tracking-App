<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $primaryKey = 'idNotification';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'metadata',
        'priority',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'idUser');
    }

    /**
     * Scopes
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Helper methods
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    public function getTimeAgo()
    {
        return $this->created_at->diffForHumans();
    }

    public function getIcon()
    {
        return match($this->type) {
            'document_expiring' => 'bi-exclamation-triangle',
            'document_expired' => 'bi-x-circle',
            'alert_generated' => 'bi-bell',
            'alert_critical' => 'bi-exclamation-octagon',
            'intervention_created' => 'bi-wrench',
            'intervention_completed' => 'bi-check-circle',
            'intervention_upcoming' => 'bi-calendar-event',
            'system_announcement' => 'bi-megaphone',
            'vehicle_maintenance_due' => 'bi-tools',
            default => 'bi-info-circle'
        };
    }

    public function getColor()
    {
        return match($this->priority) {
            'critical' => '#EF4444',
            'high' => '#F59E0B',
            'medium' => '#0EA5E9',
            'low' => '#10B981',
            default => '#6B7280'
        };
    }

    public function getBackgroundColor()
    {
        return match($this->priority) {
            'critical' => 'rgba(239, 68, 68, 0.1)',
            'high' => 'rgba(245, 158, 11, 0.1)',
            'medium' => 'rgba(14, 165, 233, 0.1)',
            'low' => 'rgba(16, 185, 129, 0.1)',
            default => 'rgba(107, 114, 128, 0.1)'
        };
    }
}
