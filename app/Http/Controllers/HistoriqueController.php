<?php

namespace App\Http\Controllers;

use App\Models\Voiture;
use Illuminate\Http\Request;

class HistoriqueController extends Controller
{
    public function getHistoriqueVehicule($voitureId)
    {
        $voiture = Voiture::with(['interventions' => function($query) {
            $query->orderBy('date', 'desc');
        }, 'alertes' => function($query) {
            $query->orderBy('dateEchance', 'desc');
        }])->findOrFail($voitureId);

        // Combine interventions and alerts into a single timeline
        $timeline = collect();

        // Add interventions to timeline
        foreach ($voiture->interventions as $intervention) {
            $timeline->push([
                'type' => 'intervention',
                'date' => $intervention->date,
                'title' => 'Intervention: ' . $intervention->type,
                'description' => $intervention->remarques,
                'cout' => $intervention->cout,
                'garage' => $intervention->garage,
                'id' => $intervention->idIntervention,
                'status' => null
            ]);
        }

        // Add alerts to timeline
        foreach ($voiture->alertes as $alerte) {
            $timeline->push([
                'type' => 'alerte',
                'date' => $alerte->dateEchance,
                'title' => 'Alerte: ' . $alerte->type,
                'description' => 'Kilométrage: ' . $alerte->kilometrageEchance . ' km',
                'cout' => null,
                'garage' => null,
                'id' => $alerte->idAlerte,
                'status' => $alerte->statut
            ]);
        }

        // Sort by date descending
        $timeline = $timeline->sortByDesc('date')->values();

        return response()->json([
            'voiture' => [
                'id' => $voiture->idVoiture,
                'marque' => $voiture->marque,
                'modele' => $voiture->modele,
                'annee' => $voiture->annee,
                'kilometrage' => $voiture->kilometrage
            ],
            'timeline' => $timeline
        ]);
    }
}
