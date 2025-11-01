<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Financier</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #344966;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
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
        .summary {
            background-color: #E8F0F7;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .summary h2 {
            color: #344966;
            font-size: 16px;
            margin: 0 0 10px 0;
        }
        .summary .stat {
            font-size: 14px;
            margin: 5px 0;
        }
        .summary .stat strong {
            color: #344966;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead {
            background-color: #344966;
            color: white;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #E8F0F7;
        }
        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        table tbody tr:hover {
            background-color: #E8F0F7;
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
        <h1>Rapport Financier</h1>
        <div class="date">Généré le {{ date('d/m/Y à H:i') }}</div>
    </div>

    <div class="summary">
        <h2>Résumé Financier</h2>
        <div class="stat"><strong>Total des coûts:</strong> {{ number_format($total, 2) }} DH</div>
        <div class="stat"><strong>Nombre d'interventions:</strong> {{ $count }}</div>
        <div class="stat"><strong>Coût moyen:</strong> {{ number_format($moyenne, 2) }} DH</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
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
                <td>{{ $intervention->id }}</td>
                <td>{{ $intervention->voiture ? $intervention->voiture->marque . ' ' . $intervention->voiture->modele : 'N/A' }}</td>
                <td>{{ $intervention->type }}</td>
                <td>{{ \Carbon\Carbon::parse($intervention->date_intervention)->format('d/m/Y') }}</td>
                <td>{{ $intervention->statut }}</td>
                <td>{{ number_format($intervention->cout, 2) }} DH</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Car Tracking App - Rapport généré automatiquement</p>
    </div>
</body>
</html>
