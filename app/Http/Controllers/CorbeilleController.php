<?php

namespace App\Http\Controllers;

use App\Models\Voiture;
use App\Models\Intervention;
use Illuminate\Http\Request;

class CorbeilleController extends Controller
{
    public function getVoituresTrashed()
    {
        $voitures = Voiture::onlyTrashed()
            ->with('user:id,nom,prenom')
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->json($voitures);
    }

    public function getInterventionsTrashed()
    {
        $interventions = Intervention::onlyTrashed()
            ->with('voiture:idVoiture,marque,modele')
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->json($interventions);
    }

    public function restoreVoiture($id)
    {
        $voiture = Voiture::onlyTrashed()->findOrFail($id);
        $voiture->restore();

        return response()->json([
            'message' => 'Véhicule restauré avec succès',
            'voiture' => $voiture
        ]);
    }

    public function restoreIntervention($id)
    {
        $intervention = Intervention::onlyTrashed()->findOrFail($id);
        $intervention->restore();

        return response()->json([
            'message' => 'Intervention restaurée avec succès',
            'intervention' => $intervention
        ]);
    }

    public function forceDeleteVoiture($id)
    {
        $voiture = Voiture::onlyTrashed()->findOrFail($id);
        $voiture->forceDelete();

        return response()->json(['message' => 'Véhicule supprimé définitivement']);
    }

    public function forceDeleteIntervention($id)
    {
        $intervention = Intervention::onlyTrashed()->findOrFail($id);
        $intervention->forceDelete();

        return response()->json(['message' => 'Intervention supprimée définitivement']);
    }
}
