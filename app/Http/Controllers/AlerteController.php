<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    public function index()
    {
        return Alerte::with('voiture')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'dateEchance' => 'required|date',
            'kilometrageEchance' => 'nullable|numeric',
            'statut' => 'required|string|max:255',
            'voiture_id' => 'required|exists:voitures,idVoiture',
        ]);

        $alerte = Alerte::create($validated);

        return response()->json($alerte, 201);
    }

    public function show(string $id)
    {
        return Alerte::with('voiture')->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $alerte = Alerte::findOrFail($id);

        $validated = $request->validate([
            'type' => 'sometimes|string|max:255',
            'dateEchance' => 'sometimes|date',
            'kilometrageEchance' => 'nullable|numeric',
            'statut' => 'sometimes|string|max:255',
            'voiture_id' => 'sometimes|exists:voitures,idVoiture',
        ]);

        $alerte->update($validated);

        return response()->json($alerte);
    }

    public function destroy(string $id)
    {
        $alerte = Alerte::findOrFail($id);
        $alerte->delete();

        return response()->json(null, 204);
    }
}
