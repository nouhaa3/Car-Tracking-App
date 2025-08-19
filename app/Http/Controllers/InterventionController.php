<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
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

        return response()->json($intervention, 201);
    }

    public function show(string $id)
    {
        return Intervention::with('documents')->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $intervention = Intervention::findOrFail($id);

        $validated = $request->validate([
            'type' => 'sometimes|string|max:255',
            'date' => 'sometimes|date',
            'cout' => 'sometimes|numeric',
            'garage' => 'sometimes|string|max:255',
            'remarques' => 'nullable|string',
            'voiture_id' => 'sometimes|exists:voitures,idVoiture',
        ]);

        $intervention->update($validated);

        return response()->json($intervention);
    }

    public function destroy(string $id)
    {
        $intervention = Intervention::findOrFail($id);
        $intervention->delete();

        return response()->json(null, 204);
    }
}
