<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #dc3545;
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin-bottom: 30px;
        }
        .content p {
            margin: 15px 0;
        }
        .user-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .user-info p {
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="icon">⚠️</div>
            <h1>Demande d'inscription refusée</h1>
        </div>
        
        <div class="content">
            <p>Bonjour {{ $user->prenom }} {{ $user->nom }},</p>
            
            <p>Nous vous informons que votre demande d'inscription sur notre plateforme de gestion de parc automobile n'a pas été approuvée.</p>
            
            <div class="user-info">
                <p><strong>Informations de votre compte :</strong></p>
                <p>Nom : {{ $user->nom }}</p>
                <p>Prénom : {{ $user->prenom }}</p>
                <p>Email : {{ $user->email }}</p>
            </div>
            
            <p>Votre accès au système est actuellement restreint.</p>
            
            <p>Si vous pensez qu'il s'agit d'une erreur ou si vous avez des questions concernant cette décision, veuillez contacter l'administrateur du système.</p>
        </div>
        
        <div class="footer">
            <p>Système de gestion de parc automobile</p>
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
