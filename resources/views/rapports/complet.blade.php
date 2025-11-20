<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Complet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #344966;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #344966;
        }
        .header h1 {
            color: #344966;
            margin: 0;
            font-size: 24px;
        }
        .header .date {
            color: #748BAA;
            font-size: 11px;
            margin-top: 5px;
        }
        .section {
            margin: 30px 0;
            page-break-inside: avoid;
        }
        .section h2 {
            color: #344966;
            font-size: 16px;
            border-bottom: 2px solid #748BAA;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table thead {
            background-color: #344966;
            color: white;
        }
        table th, table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #E8F0F7;
            font-size: 10px;
        }
        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .summary {
            background-color: #E8F0F7;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .summary .stat {
            font-size: 12px;
            margin: 5px 0;
        }
        .summary .stat strong {
            color: #344966;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #748BAA;
            padding: 10px 0;
            border-top: 1px solid #E8F0F7;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport Complet</h1>
        <div class="date">Généré le {{ date('d/m/Y à H:i') }}</div>
    </div>

    <!-- Section Véhicules -->
    <div class="section">
        <h2>Véhicules ({{ $voitures->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Marque/Modèle</th>
                    <th>Immatriculation</th>
                    <th>Année</th>
                    <th>Km</th>
                    <th>Statut</th>
                    <th>État</th>
                </tr>
            </thead>
            <tbody>
                @foreach($voitures as $voiture)
                <tr>
                    <td>{{ $voiture->marque }} {{ $voiture->modele }}</td>
                    <td>{{ $voiture->immatriculation }}</td>
                    <td>{{ $voiture->annee }}</td>
                    <td>{{ number_format($voiture->kilometrage) }}</td>
                    <td>{{ $voiture->statut }}</td>
                    <td>{{ $voiture->etat }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Section Interventions -->
    <div class="section">
        <h2>Interventions ({{ $interventions->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Véhicule</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Coût</th>
                </tr>
            </thead>
            <tbody>
                @foreach($interventions as $intervention)
                <tr>
                    <td>{{ $intervention->voiture ? $intervention->voiture->marque . ' ' . $intervention->voiture->modele : 'N/A' }}</td>
                    <td>{{ $intervention->type }}</td>
                    <td>{{ \Carbon\Carbon::parse($intervention->date_intervention)->format('d/m/Y') }}</td>
                    <td>{{ $intervention->statut }}</td>
                    <td>{{ number_format($intervention->cout, 2) }} DH</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Section Résumé Financier -->
    <div class="section">
        <h2>Résumé Financier</h2>
        <div class="summary">
            <div class="stat"><strong>Total des coûts:</strong> {{ number_format($interventions->sum('cout'), 2) }} DT</div>
            <div class="stat"><strong>Nombre d'interventions:</strong> {{ $interventions->count() }}</div>
            <div class="stat"><strong>Coût moyen:</strong> {{ $interventions->count() > 0 ? number_format($interventions->avg('cout'), 2) : 0 }} DT</div>
        </div>
    </div>

    <!-- Section Statistiques Visuelles -->
    <div class="section">
        <h2>Statistiques Visuelles</h2>
        
        <!-- Graphique Véhicules par Statut -->
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 13px; color: #546A88; margin-bottom: 10px;">Répartition des Véhicules par Statut</h3>
            <div class="chart-container">
                @php
                    $vehiculesParStatut = $voitures->groupBy('statut');
                    $totalVoitures = $voitures->count();
                @endphp
                <table style="width: 100%; border: none;">
                    <tr>
                        @foreach($vehiculesParStatut as $statut => $voituresGroupe)
                            @php
                                $count = $voituresGroupe->count();
                                $percentage = ($totalVoitures > 0) ? round(($count / $totalVoitures) * 100, 1) : 0;
                                $colors = [
                                    'En boutique' => '#4CAF50',
                                    'En location' => '#2196F3',
                                    'En maintenance' => '#FF9800'
                                ];
                                $color = $colors[$statut] ?? '#9E9E9E';
                            @endphp
                            <td style="text-align: center; padding: 10px; border: none;">
                                <div style="background-color: {{ $color }}; color: white; padding: 15px; border-radius: 8px; margin-bottom: 5px;">
                                    <div style="font-size: 24px; font-weight: bold;">{{ $count }}</div>
                                    <div style="font-size: 11px; opacity: 0.9;">{{ $percentage }}%</div>
                                </div>
                                <div style="font-size: 10px; color: #344966;">{{ $statut }}</div>
                            </td>
                        @endforeach
                    </tr>
                </table>
            </div>
        </div>

        <!-- Graphique Interventions par Type -->
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 13px; color: #546A88; margin-bottom: 10px;">Interventions par Type</h3>
            @php
                $interventionsParType = $interventions->groupBy('type');
                $maxCount = $interventionsParType->map->count()->max();
            @endphp
            <div style="padding: 15px; background-color: #f8f9fa; border-radius: 8px;">
                @foreach($interventionsParType as $type => $interventionsGroupe)
                    @php
                        $count = $interventionsGroupe->count();
                        $width = ($maxCount > 0) ? ($count / $maxCount) * 100 : 0;
                    @endphp
                    <div style="margin-bottom: 12px;">
                        <div style="font-size: 10px; color: #344966; margin-bottom: 3px; font-weight: 500;">{{ $type }} ({{ $count }})</div>
                        <div style="background-color: #E8F0F7; height: 20px; border-radius: 10px; overflow: hidden;">
                            <div style="background: linear-gradient(90deg, #546A88 0%, #748BAA 100%); height: 100%; width: {{ $width }}%; border-radius: 10px; display: flex; align-items: center; justify-content: flex-end; padding-right: 8px; color: white; font-size: 9px; font-weight: bold;">
                                @if($width > 15){{ $count }}@endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Graphique Coûts par Mois -->
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 13px; color: #546A88; margin-bottom: 10px;">Évolution des Coûts (6 derniers mois)</h3>
            @php
                $interventionsByMonth = [];
                for ($i = 5; $i >= 0; $i--) {
                    $month = now()->subMonths($i)->format('Y-m');
                    $monthLabel = now()->subMonths($i)->locale('fr')->isoFormat('MMM YYYY');
                    $interventionsByMonth[$monthLabel] = $interventions->filter(function($intervention) use ($month) {
                        return \Carbon\Carbon::parse($intervention->date_intervention)->format('Y-m') === $month;
                    })->sum('cout');
                }
                $maxCost = max(array_values($interventionsByMonth)) ?: 1;
            @endphp
            <div style="padding: 15px; background-color: #f8f9fa; border-radius: 8px;">
                <table style="width: 100%; border: none;">
                    <tr style="vertical-align: bottom; height: 150px;">
                        @foreach($interventionsByMonth as $month => $cost)
                            @php
                                $height = ($cost / $maxCost) * 100;
                            @endphp
                            <td style="text-align: center; padding: 0 5px; border: none; vertical-align: bottom;">
                                <div style="display: inline-block; width: 100%; max-width: 60px;">
                                    <div style="font-size: 9px; color: #344966; margin-bottom: 3px; font-weight: 500;">{{ number_format($cost, 0) }} DT</div>
                                    <div style="background: linear-gradient(180deg, #546A88 0%, #748BAA 100%); height: {{ $height }}px; max-height: 120px; min-height: 5px; border-radius: 5px 5px 0 0; margin: 0 auto;"></div>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($interventionsByMonth as $month => $cost)
                            <td style="text-align: center; padding: 5px 2px 0 2px; border: none;">
                                <div style="font-size: 8px; color: #748BAA; transform: rotate(-45deg); white-space: nowrap; margin-top: 15px;">{{ $month }}</div>
                            </td>
                        @endforeach
                    </tr>
                </table>
            </div>
        </div>

        <!-- Statistiques Globales -->
        <div style="margin-top: 20px;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 33%; padding: 10px; border: none;">
                        <div style="background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%); padding: 15px; border-radius: 8px; text-align: center;">
                            <div style="color: #1976D2; font-size: 28px; font-weight: bold;">{{ $voitures->count() }}</div>
                            <div style="color: #546A88; font-size: 10px; margin-top: 5px;">Total Véhicules</div>
                        </div>
                    </td>
                    <td style="width: 33%; padding: 10px; border: none;">
                        <div style="background: linear-gradient(135deg, #F3E5F5 0%, #E1BEE7 100%); padding: 15px; border-radius: 8px; text-align: center;">
                            <div style="color: #7B1FA2; font-size: 28px; font-weight: bold;">{{ $interventions->count() }}</div>
                            <div style="color: #546A88; font-size: 10px; margin-top: 5px;">Total Interventions</div>
                        </div>
                    </td>
                    <td style="width: 33%; padding: 10px; border: none;">
                        <div style="background: linear-gradient(135deg, #FFF9C4 0%, #FFF59D 100%); padding: 15px; border-radius: 8px; text-align: center;">
                            <div style="color: #F57F17; font-size: 20px; font-weight: bold;">{{ number_format($interventions->sum('cout'), 0) }} DT</div>
                            <div style="color: #546A88; font-size: 10px; margin-top: 5px;">Coût Total</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Section Utilisateurs -->
    <div class="section">
        <h2>Utilisateurs ({{ $users->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->nom }}</td>
                    <td>{{ $user->prenom }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role ? $user->role->nomRole : 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Car Tracking App - Rapport généré automatiquement</p>
    </div>
</body>
</html>
