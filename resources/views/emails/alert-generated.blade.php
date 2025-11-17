<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Alerte</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #344966, #546A88);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .priority-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 20px;
        }
        .alert-info {
            background-color: #f8f9fa;
            border-left: 4px solid {{ $priorityColor }};
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .alert-info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .alert-info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #546A88;
        }
        .value {
            color: #344966;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #344966;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #546A88;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
        .footer a {
            color: #344966;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🔔</div>
            <h1>Nouvelle Alerte Générée</h1>
        </div>

        <div class="content">
            <span class="priority-badge" style="background-color: {{ $priorityColor }};">
                Priorité: {{ ucfirst($priority) }}
            </span>

            <h2 style="color: #344966; margin-top: 0;">{{ $alerteType }}</h2>

            <div class="alert-info">
                <div class="alert-info-row">
                    <span class="label">🚗 Véhicule:</span>
                    <span class="value">{{ $voitureInfo }}</span>
                </div>
                <div class="alert-info-row">
                    <span class="label">📅 Date d'échéance:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($dateEchance)->format('d/m/Y') }}</span>
                </div>
                @if($kilometrage)
                <div class="alert-info-row">
                    <span class="label">🛣️ Kilométrage:</span>
                    <span class="value">{{ number_format($kilometrage, 0, ',', ' ') }} km</span>
                </div>
                @endif
            </div>

            <p style="color: #6c757d; line-height: 1.6;">
                Une nouvelle alerte a été générée pour votre véhicule. Veuillez prendre les mesures nécessaires avant la date d'échéance.
            </p>

            <a href="{{ url('/alertes') }}" class="button">
                Voir les alertes
            </a>
        </div>

        <div class="footer">
            <p>
                Cet email a été envoyé automatiquement par <strong>CarTrackingApp</strong><br>
                <a href="{{ url('/') }}">Accéder à l'application</a> | 
                <a href="{{ url('/settings') }}">Gérer les notifications</a>
            </p>
        </div>
    </div>
</body>
</html>
