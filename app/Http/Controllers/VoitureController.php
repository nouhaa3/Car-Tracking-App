<?php

namespace App\Http\Controllers;

use App\Models\Voiture;
use App\Exports\VoituresExport;
use App\Imports\VoituresImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class VoitureController extends Controller
{
    public function index()
    {
        return Voiture::all();
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'marque' => 'required|string',
                'modele' => 'required|string',
                'annee' => 'required|integer',
                'kilometrage' => 'required|integer',
                'etat' => 'required|string',
                'statut' => 'required|string',
                'image' => 'nullable|image|max:5120', // 5MB max
            ]);

            $voiture = new Voiture();
            $voiture->marque = $validated['marque'];
            $voiture->modele = $validated['modele'];
            $voiture->annee = $validated['annee'];
            $voiture->kilometrage = $validated['kilometrage'];
            $voiture->etat = $validated['etat'];
            $voiture->statut = $validated['statut'];
            
            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
            
            $voiture->user_id = auth()->id();
            
            if ($request->hasFile('image')) {
                try {
                    $imagePath = $request->file('image')->store('voitures', 'public');
                    $voiture->image = $imagePath;
                } catch (\Exception $e) {
                    \Log::error('Image upload error: ' . $e->getMessage());
                    return response()->json(['error' => 'Erreur lors du téléchargement de l\'image: ' . $e->getMessage()], 500);
                }
            }

            $voiture->save();

            return response()->json([
                'message' => 'Voiture ajoutée avec succès',
                'voiture' => $voiture
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Erreur de validation',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error storing voiture: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Erreur serveur: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }


    public function show($idVoiture)
    {
        $voiture = Voiture::find($idVoiture);

        if (!$voiture) {
            return response()->json(['message' => 'Voiture non trouvée'], 404);
        }

        return response()->json($voiture);
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
        
        // Cascade delete: Delete all related interventions and alerts
        $voiture->interventions()->delete();
        $voiture->alertes()->delete();
        
        // Delete the vehicle
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

    public function countTotal()
    {
        $total = Voiture::count();
        return response()->json(['total' => $total]);
    }

    public function getTopExpensive()
    {
        $topCars = Voiture::select(
            'voitures.idVoiture',
            'voitures.marque',
            'voitures.modele',
            DB::raw('COALESCE(SUM(interventions.cout), 0) as total_cost')
        )
        ->leftJoin('interventions', 'voitures.idVoiture', '=', 'interventions.voiture_id')
        ->groupBy('voitures.idVoiture', 'voitures.marque', 'voitures.modele')
        ->orderBy('total_cost', 'desc')
        ->limit(5)
        ->get();

        return response()->json($topCars);
    }

    public function getByYear()
    {
        $data = Voiture::select('annee', DB::raw('count(*) as total'))
            ->groupBy('annee')
            ->orderBy('annee', 'desc')
            ->get();

        return response()->json($data);
    }

    public function getAvailabilityRate()
    {
        $total = Voiture::count();
        $available = Voiture::where('statut', 'En boutique')->count();
        
        $rate = $total > 0 ? ($available / $total) * 100 : 0;

        return response()->json([
            'total' => $total,
            'available' => $available,
            'rate' => round($rate, 2)
        ]);
    }

    /**
     * Export vehicles to Excel
     */
    public function export()
    {
        return Excel::download(new VoituresExport, 'vehicules_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Import vehicles from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new VoituresImport(auth()->id()), $request->file('file'));

            return response()->json([
                'message' => 'Véhicules importés avec succès',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de l\'importation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $headers = [
            'marque',
            'modele',
            'annee',
            'kilometrage',
            'etat',
            'statut',
        ];

        $exampleData = [
            ['Toyota', 'Corolla', 2020, 50000, 'Bon', 'En boutique'],
            ['Renault', 'Clio', 2019, 75000, 'Excellent', 'En location'],
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $sheet->fromArray($headers, null, 'A1');
        
        // Add example data
        $sheet->fromArray($exampleData, null, 'A2');

        // Style headers
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'template_import_vehicules.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

                $writer->save('php://output');
        exit;
    }

    public function uploadImage(Request $request, $id)
    {
        $voiture = Voiture::findOrFail($id);

        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
        ]);

        try {
            // Delete old image if exists
            if ($voiture->image && \Storage::disk('public')->exists($voiture->image)) {
                \Storage::disk('public')->delete($voiture->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('voitures', 'public');
            $voiture->image = $imagePath;
            $voiture->save();

            return response()->json([
                'message' => 'Image mise à jour avec succès',
                'image' => $imagePath
            ]);
        } catch (\Exception $e) {
            \Log::error('Image upload error: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du téléchargement de l\'image'], 500);
        }
    }

    public function deleteImage($id)
    {
        $voiture = Voiture::findOrFail($id);

        try {
            // Delete image file if exists
            if ($voiture->image && \Storage::disk('public')->exists($voiture->image)) {
                \Storage::disk('public')->delete($voiture->image);
            }

            // Update database
            $voiture->image = null;
            $voiture->save();

            return response()->json([
                'message' => 'Image supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            \Log::error('Image delete error: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression de l\'image'], 500);
        }
    }

}
