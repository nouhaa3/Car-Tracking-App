<?php

namespace App\Http\Controllers;

use App\Models\Voiture;
use Illuminate\Http\Request;

class VoitureController extends Controller
{
    public function index()
    {
        return Voiture::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'marque' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'annee' => 'required|integer',
            'kilometrage' => 'required|numeric',
            'etat' => 'required|string|max:255',
            'statut' => 'required|in:En boutique,En location,En maintenance',
            'userid' => 'required|exists:users,id',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('voitures', 'public');
        }
        $voiture = Voiture::create($validated);

        return response()->json($voiture, 201);
    }

    public function show(string $id)
    {
        return Voiture::findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $voiture = Voiture::findOrFail($id);

        $validated = $request->validate([
            'marque' => 'sometimes|required|string|max:255',
            'modele' => 'sometimes|required|string|max:255',
            'annee' => 'sometimes|required|integer',
            'kilometrage' => 'sometimes|required|numeric',
            'etat' => 'sometimes|required|string|max:255',
            'statut' => 'sometimes|required|in:En boutique,En location,En maintenance',
        ]);

        $voiture->update($validated);

        return response()->json($voiture);
    }

    public function destroy(string $id)
    {
        $voiture = Voiture::findOrFail($id);
        $voiture->delete();

        return response()->json(null, 204);
    }

    public function countByStatus()
    {
        $data = \App\Models\Voiture::select('statut', \DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->get();

        return response()->json($data);
    }


}
