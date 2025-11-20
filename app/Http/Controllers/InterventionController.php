<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InterventionController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        return Intervention::with('voiture', 'documents')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'date' => 'required|date',
            'cout' => 'required|numeric',
            'garage' => 'required|string|max:255',
            'remarques' => 'nullable|string',
            'voiture_id' => 'required|exists:voitures,idVoiture',
        ]);

        $intervention = Intervention::create($validated);
        $intervention->load('voiture');
        
        // Send notification
        $this->notificationService->notifyInterventionCreated($intervention);

        return response()->json($intervention, 201);
    }

    public function show(string $id)
    {
        return Intervention::with('documents')->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $intervention = Intervention::findOrFail($id);
        $wasCompleted = $intervention->statut === 'Terminée';

        $validated = $request->validate([
            'type' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'cout' => 'sometimes|numeric',
            'garage' => 'sometimes|string|max:255',
            'remarques' => 'nullable|string',
            'statut' => 'sometimes|in:En attente,En cours,Terminée',
            'voiture_id' => 'sometimes|exists:voitures,idVoiture',
        ]);

        $intervention->update($validated);
        $intervention->load('voiture');
        
        // Send notification if intervention just completed
        if (!$wasCompleted && $intervention->statut === 'Terminée') {
            $this->notificationService->notifyInterventionCompleted($intervention);
        }

        return response()->json($intervention);
    }

    public function destroy(string $id)
    {
        $intervention = Intervention::findOrFail($id);
        $intervention->delete();

        return response()->json(null, 204);
    }

    public function getRecentHistory()
    {
        $interventions = Intervention::with('voiture')
            ->select('interventions.*')
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($intervention) {
                return [
                    'id' => $intervention->idIntervention,
                    'type' => $intervention->type,
                    'date' => $intervention->date,
                    'cout' => $intervention->cout,
                    'vehicule' => $intervention->voiture 
                        ? "{$intervention->voiture->marque} {$intervention->voiture->modele}"
                        : 'N/A'
                ];
            });

        return response()->json($interventions);
    }

    /**
     * Retourne le nombre d'interventions pour le mois courant
     */
    public function countByMonth()
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        $count = Intervention::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->count();

        return response()->json(['total' => $count]);
    }

    /**
     * Retourne les interventions groupées par type
     */
    public function countByType()
    {
        $data = Intervention::select('type', \DB::raw('count(*) as total'))
            ->groupBy('type')
            ->get();

        return response()->json($data);
    }

    /**
     * Duplicate an existing intervention
     */
    public function duplicate($id)
    {
        $intervention = Intervention::findOrFail($id);
        
        $newIntervention = $intervention->replicate();
        $newIntervention->date = now();
        $newIntervention->save();

        return response()->json([
            'message' => 'Intervention dupliquée avec succès',
            'intervention' => $newIntervention->load('voiture')
        ], 201);
    }
}
