<?php

namespace App\Mail;

use App\Models\Alerte;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertGeneratedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $alerte;
    public $voiture;
    public $priority;
    public $priorityColor;

    /**
     * Create a new message instance.
     */
    public function __construct(Alerte $alerte, string $priority, string $priorityColor)
    {
        $this->alerte = $alerte;
        $this->voiture = $alerte->voiture;
        $this->priority = $priority;
        $this->priorityColor = $priorityColor;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('🔔 Nouvelle Alerte: ' . $this->alerte->type)
            ->view('emails.alert-generated')
            ->with([
                'alerteType' => $this->alerte->type,
                'dateEchance' => $this->alerte->dateEchance,
                'voitureInfo' => $this->voiture ? "{$this->voiture->marque} {$this->voiture->modele}" : 'N/A',
                'kilometrage' => $this->alerte->kilometrageEchance,
                'priority' => $this->priority,
                'priorityColor' => $this->priorityColor,
            ]);
    }
}
