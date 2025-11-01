<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Personnalisé</title>
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
        <h1>Rapport Personnalisé</h1>
        <div class="date">Période: {{ $periodLabel }} | Généré le {{ date('d/m/Y à H:i') }}</div>
    </div>

    @if(isset($voitures) && $voitures->count() > 0)
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
    @endif

    @if(isset($interventions) && $interventions->count() > 0)
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

        @if(isset($includeFinancier) && $includeFinancier)
        <div class="summary">
            <div class="stat"><strong>Total des coûts:</strong> {{ number_format($interventions->sum('cout'), 2) }} DH</div>
            <div class="stat"><strong>Coût moyen:</strong> {{ number_format($interventions->avg('cout'), 2) }} DH</div>
        </div>
        @endif
    </div>
    @endif

    @if(isset($users) && $users->count() > 0)
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
    @endif

    <div class="footer">
        <p>Car Tracking App - Rapport généré automatiquement</p>
    </div>
</body>
</html>
