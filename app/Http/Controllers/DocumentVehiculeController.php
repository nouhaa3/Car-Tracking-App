<?php

namespace App\Http\Controllers;

use App\Models\DocumentVehicule;
use App\Models\Voiture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentVehiculeController extends Controller
{
    public function index($voitureId)
    {
        $voiture = Voiture::findOrFail($voitureId);
        $documents = $voiture->documentsVehicule()->orderBy('created_at', 'desc')->get();
        
        return response()->json($documents);
    }

    public function store(Request $request, $voitureId)
    {
        $request->validate([
            'type' => 'required|in:Carte grise,Assurance,Contrôle technique,Garantie,Facture achat,Autre',
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'date_expiration' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $voiture = Voiture::findOrFail($voitureId);

        $file = $request->file('fichier');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('documents/vehicules/' . $voitureId, $filename, 'public');

        $document = DocumentVehicule::create([
            'voiture_id' => $voitureId,
            'type' => $request->type,
            'nom_fichier' => $file->getClientOriginalName(),
            'chemin' => $path,
            'date_expiration' => $request->date_expiration,
            'notes' => $request->notes
        ]);

        return response()->json([
            'message' => 'Document ajouté avec succès',
            'document' => $document
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $document = DocumentVehicule::findOrFail($id);

        $request->validate([
            'type' => 'sometimes|in:Carte grise,Assurance,Contrôle technique,Garantie,Facture achat,Autre',
            'fichier' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'date_expiration' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        // If new file is uploaded, replace the old one
        if ($request->hasFile('fichier')) {
            // Delete old file
            if (Storage::disk('public')->exists($document->chemin)) {
                Storage::disk('public')->delete($document->chemin);
            }

            // Store new file
            $file = $request->file('fichier');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('documents/vehicules/' . $document->voiture_id, $filename, 'public');

            $document->nom_fichier = $file->getClientOriginalName();
            $document->chemin = $path;
        }

        // Update other fields
        if ($request->has('type')) {
            $document->type = $request->type;
        }
        
        $document->date_expiration = $request->date_expiration;
        $document->notes = $request->notes;
        $document->save();

        return response()->json([
            'message' => 'Document mis à jour avec succès',
            'document' => $document
        ]);
    }

    public function download($id)
    {
        $document = DocumentVehicule::findOrFail($id);
        
        if (!Storage::disk('public')->exists($document->chemin)) {
            return response()->json(['error' => 'Fichier introuvable'], 404);
        }

        return response()->download(
            storage_path('app/public/' . $document->chemin),
            $document->nom_fichier
        );
    }

    public function destroy($id)
    {
        $document = DocumentVehicule::findOrFail($id);
        
        // Delete file from storage
        if (Storage::disk('public')->exists($document->chemin)) {
            Storage::disk('public')->delete($document->chemin);
        }

        $document->delete();

        return response()->json(['message' => 'Document supprimé avec succès']);
    }

    public function expiring()
    {
        $documents = DocumentVehicule::whereNotNull('date_expiration')
            ->whereDate('date_expiration', '>', now())
            ->whereDate('date_expiration', '<=', now()->addDays(30))
            ->with('voiture')
            ->get();

        return response()->json($documents);
    }

    public function expired()
    {
        $documents = DocumentVehicule::whereNotNull('date_expiration')
            ->whereDate('date_expiration', '<', now())
            ->with('voiture')
            ->get();

        return response()->json($documents);
    }
}
