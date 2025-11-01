<?php

namespace App\Http\Controllers;

use App\Models\Voiture;
use App\Models\Intervention;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class RapportController extends Controller
{
    /**
     * Générer un fichier CSV à partir des données
     */
    private function generateCSV($data, $headers, $filename)
    {
        $csvData = [];
        
        // En-têtes
        $csvData[] = array_keys($headers);
        
        // Données
        foreach ($data as $item) {
            $row = [];
            foreach ($headers as $column) {
                $row[] = $item->$column ?? '';
            }
            $csvData[] = $row;
        }
        
        // Créer le contenu CSV
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            foreach ($csvData as $row) {
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '_' . date('Y-m-d') . '.csv"',
        ];
        
        return Response::stream($callback, 200, $headers);
    }

    /**
     * Télécharger le rapport des véhicules
     */
    public function rapportVoitures(Request $request, $format)
    {
        $voitures = Voiture::all();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('rapports.voitures', compact('voitures'));
            return $pdf->download('rapport_voitures_' . date('Y-m-d') . '.pdf');
        }

        return $this->generateCSV($voitures, [
            'ID' => 'id',
            'Marque' => 'marque',
            'Modèle' => 'modele',
            'Immatriculation' => 'immatriculation',
            'Année' => 'annee',
            'Kilométrage' => 'kilometrage',
            'Statut' => 'statut',
            'État' => 'etat',
        ], 'rapport_voitures');
    }

    /**
     * Télécharger le rapport des interventions
     */
    public function rapportInterventions(Request $request, $format)
    {
        $interventions = Intervention::with('voiture')->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('rapports.interventions', compact('interventions'));
            return $pdf->download('rapport_interventions_' . date('Y-m-d') . '.pdf');
        }

        $interventionsData = $interventions->map(function($intervention) {
            return [
                'id' => $intervention->id,
                'voiture' => $intervention->voiture ? $intervention->voiture->marque . ' ' . $intervention->voiture->modele : 'N/A',
                'type' => $intervention->type,
                'description' => $intervention->description,
                'date_intervention' => $intervention->date_intervention,
                'statut' => $intervention->statut,
                'cout' => $intervention->cout,
                'created_at' => $intervention->created_at->format('Y-m-d'),
            ];
        });

        $csvData = [];
        $csvData[] = ['ID', 'Véhicule', 'Type', 'Description', 'Date', 'Statut', 'Coût', 'Créé le'];
        
        foreach ($interventionsData as $item) {
            $csvData[] = array_values((array)$item);
        }
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($csvData as $row) {
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };
        
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rapport_interventions_' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Télécharger le rapport des utilisateurs
     */
    public function rapportUsers(Request $request, $format)
    {
        $users = User::with('role')->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('rapports.users', compact('users'));
            return $pdf->download('rapport_utilisateurs_' . date('Y-m-d') . '.pdf');
        }

        return $this->generateCSV($users, [
            'ID' => 'id',
            'Nom' => 'nom',
            'Prénom' => 'prenom',
            'Email' => 'email',
            'Créé le' => 'created_at',
        ], 'rapport_utilisateurs');
    }

    /**
     * Télécharger le rapport financier
     */
    public function rapportFinancier(Request $request, $format)
    {
        $interventions = Intervention::with('voiture')->get();
        $total = $interventions->sum('cout');
        $count = $interventions->count();
        $moyenne = $count > 0 ? $total / $count : 0;

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('rapports.financier', compact('interventions', 'total', 'count', 'moyenne'));
            return $pdf->download('rapport_financier_' . date('Y-m-d') . '.pdf');
        }
        
        $data = $interventions->map(function($intervention) {
            return [
                'id' => $intervention->id,
                'voiture' => $intervention->voiture ? $intervention->voiture->marque . ' ' . $intervention->voiture->modele : 'N/A',
                'type' => $intervention->type,
                'date' => $intervention->date_intervention,
                'cout' => $intervention->cout,
                'statut' => $intervention->statut,
            ];
        });
        
        $csvData = [];
        $csvData[] = ['ID', 'Véhicule', 'Type', 'Date', 'Coût (MAD)', 'Statut'];
        
        foreach ($data as $item) {
            $csvData[] = array_values((array)$item);
        }
        
        // Ligne vide
        $csvData[] = ['', '', '', '', '', ''];
        // Statistiques
        $csvData[] = ['STATISTIQUES', '', '', '', '', ''];
        $csvData[] = ['Coût Total', '', '', '', $total, 'MAD'];
        $csvData[] = ['Nombre d\'interventions', '', '', '', $count, ''];
        $csvData[] = ['Coût Moyen', '', '', '', round($moyenne, 2), 'MAD'];
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($csvData as $row) {
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };
        
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rapport_financier_' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Télécharger le rapport complet
     */
    public function rapportComplet(Request $request, $format)
    {
        $voitures = Voiture::all();
        $interventions = Intervention::with('voiture')->get();
        $users = User::with('role')->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('rapports.complet', compact('voitures', 'interventions', 'users'));
            return $pdf->download('rapport_complet_' . date('Y-m-d') . '.pdf');
        }
        
        $csvData = [];
        
        // Section Véhicules
        $csvData[] = ['=== RAPPORT VÉHICULES ==='];
        $csvData[] = ['ID', 'Marque', 'Modèle', 'Immatriculation', 'Statut', 'État'];
        foreach ($voitures as $v) {
            $csvData[] = [$v->id, $v->marque, $v->modele, $v->immatriculation, $v->statut, $v->etat];
        }
        $csvData[] = [''];
        
        // Section Interventions
        $csvData[] = ['=== RAPPORT INTERVENTIONS ==='];
        $csvData[] = ['ID', 'Véhicule', 'Type', 'Date', 'Coût', 'Statut'];
        foreach ($interventions as $i) {
            $csvData[] = [
                $i->id,
                $i->voiture ? $i->voiture->marque . ' ' . $i->voiture->modele : 'N/A',
                $i->type,
                $i->date_intervention,
                $i->cout,
                $i->statut
            ];
        }
        $csvData[] = [''];
        
        // Section Utilisateurs
        $csvData[] = ['=== RAPPORT UTILISATEURS ==='];
        $csvData[] = ['ID', 'Nom', 'Prénom', 'Email', 'Rôle'];
        foreach ($users as $u) {
            $csvData[] = [$u->id, $u->nom, $u->prenom, $u->email, $u->role ? $u->role->nomRole : 'N/A'];
        }
        $csvData[] = [''];
        
        // Statistiques globales
        $csvData[] = ['=== STATISTIQUES GLOBALES ==='];
        $csvData[] = ['Total Véhicules', $voitures->count()];
        $csvData[] = ['Véhicules Disponibles', $voitures->where('statut', 'disponible')->count()];
        $csvData[] = ['Véhicules en Maintenance', $voitures->where('statut', 'maintenance')->count()];
        $csvData[] = ['Total Interventions', $interventions->count()];
        $csvData[] = ['Coût Total Interventions', $interventions->sum('cout') . ' MAD'];
        $csvData[] = ['Total Utilisateurs', $users->count()];
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($csvData as $row) {
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };
        
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rapport_complet_' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Télécharger un rapport personnalisé
     */
    public function rapportCustom(Request $request, $format)
    {
        // Initialiser les variables
        $voitures = null;
        $interventions = null;
        $users = null;
        $includeFinancier = $request->includeFinancier ?? false;

        // Déterminer le label de la période
        $periodLabels = [
            '7days' => '7 derniers jours',
            '30days' => '30 derniers jours',
            '3months' => '3 derniers mois',
            'year' => 'Année en cours',
            'all' => 'Toutes les données'
        ];
        $periodLabel = $periodLabels[$request->period] ?? 'Période personnalisée';

        // Véhicules
        if ($request->includeVoitures) {
            $voitures = Voiture::all();
        }
        
        // Interventions avec filtrage de période
        if ($request->includeInterventions) {
            $query = Intervention::with('voiture');
            
            switch ($request->period) {
                case '7days':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case '30days':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
                case '3months':
                    $query->where('created_at', '>=', now()->subMonths(3));
                    break;
                case 'year':
                    $query->where('created_at', '>=', now()->startOfYear());
                    break;
            }
            
            $interventions = $query->get();
        }
        
        // Utilisateurs
        if ($request->includeUsers) {
            $users = User::with('role')->get();
        }

        // Générer le PDF si demandé
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('rapports.custom', compact('voitures', 'interventions', 'users', 'includeFinancier', 'periodLabel'));
            return $pdf->download('rapport_personnalise_' . date('Y-m-d') . '.pdf');
        }

        // Sinon générer le CSV
        $csvData = [];
        $csvData[] = ['=== RAPPORT PERSONNALISÉ ==='];
        $csvData[] = ['Période: ' . $periodLabel];
        $csvData[] = [''];
        
        // Véhicules CSV
        if ($voitures && $voitures->count() > 0) {
            $csvData[] = ['=== VÉHICULES ==='];
            $csvData[] = ['ID', 'Marque', 'Modèle', 'Immatriculation', 'Statut'];
            foreach ($voitures as $v) {
                $csvData[] = [$v->id, $v->marque, $v->modele, $v->immatriculation, $v->statut];
            }
            $csvData[] = [''];
        }
        
        // Interventions CSV
        if ($interventions && $interventions->count() > 0) {
            $csvData[] = ['=== INTERVENTIONS ==='];
            $csvData[] = ['ID', 'Véhicule', 'Type', 'Date', 'Coût', 'Statut'];
            foreach ($interventions as $i) {
                $csvData[] = [
                    $i->id,
                    $i->voiture ? $i->voiture->marque . ' ' . $i->voiture->modele : 'N/A',
                    $i->type,
                    $i->date_intervention,
                    $i->cout,
                    $i->statut
                ];
            }
            
            if ($includeFinancier) {
                $csvData[] = [''];
                $csvData[] = ['Coût Total', $interventions->sum('cout') . ' MAD'];
            }
            $csvData[] = [''];
        }
        
        // Utilisateurs CSV
        if ($users && $users->count() > 0) {
            $csvData[] = ['=== UTILISATEURS ==='];
            $csvData[] = ['ID', 'Nom', 'Prénom', 'Email', 'Rôle'];
            foreach ($users as $u) {
                $csvData[] = [$u->id, $u->nom, $u->prenom, $u->email, $u->role ? $u->role->nomRole : 'N/A'];
            }
        }
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($csvData as $row) {
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };
        
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rapport_personnalise_' . date('Y-m-d') . '.csv"',
        ]);
    }
}
