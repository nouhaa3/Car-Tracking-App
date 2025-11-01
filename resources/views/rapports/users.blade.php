<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des Utilisateurs</title>
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
        <h1>Rapport des Utilisateurs</h1>
        <div class="date">Généré le {{ date('d/m/Y à H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Créé le</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->nom }}</td>
                <td>{{ $user->prenom }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role ? $user->role->nomRole : 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Car Tracking App - Rapport généré automatiquement</p>
    </div>
</body>
</html>
