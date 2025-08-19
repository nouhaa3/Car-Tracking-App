<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        return Document::with('intervention')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomFichier' => 'required|string|max:255',
            'typeFichier' => 'required|string|max:255',
            'chemin' => 'required|string|max:255',
            'dateUpload' => 'required|date',
            'intervention_id' => 'required|exists:interventions,idIntervention',
        ]);

        $document = Document::create($validated);

        return response()->json($document, 201);
    }

    public function show(string $id)
    {
        return Document::with('intervention')->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $document = Document::findOrFail($id);

        $validated = $request->validate([
            'nomFichier' => 'sometimes|string|max:255',
            'typeFichier' => 'sometimes|string|max:255',
            'chemin' => 'sometimes|string|max:255',
            'dateUpload' => 'sometimes|date',
            'intervention_id' => 'sometimes|exists:interventions,idIntervention',
        ]);

        $document->update($validated);

        return response()->json($document);
    }

    public function destroy(string $id)
    {
        $document = Document::findOrFail($id);
        $document->delete();

        return response()->json(null, 204);
    }
}
