<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte Approuvé</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .email-header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.95;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body h2 {
            color: #10b981;
            font-size: 22px;
            margin: 0 0 20px;
        }
        .email-body p {
            margin: 0 0 15px;
            font-size: 16px;
            color: #555;
        }
        .credentials-box {
            background-color: #f0fdf4;
            border: 2px solid #10b981;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .credentials-box p {
            margin: 8px 0;
            font-size: 15px;
        }
        .credentials-box strong {
            color: #059669;
            font-weight: 600;
        }
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            transition: transform 0.2s;
        }
        .login-button:hover {
            transform: translateY(-2px);
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .email-footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #6b7280;
        }
        .success-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="success-icon">✅</div>
            <h1>Compte Approuvé !</h1>
            <p>Bienvenue dans Car Tracking App</p>
        </div>
        
        <div class="email-body">
            <h2>Bonjour {{ $userName }},</h2>
            
            <p>Excellente nouvelle ! Votre demande d'inscription a été <strong>approuvée</strong> par notre administrateur.</p>
            
            <p>Vous pouvez désormais accéder à votre compte et profiter de toutes les fonctionnalités de Car Tracking App.</p>
            
            <div class="credentials-box">
                <p><strong>📧 Adresse email :</strong> {{ $email }}</p>
                <p><strong>🔐 Mot de passe :</strong> Le mot de passe que vous avez choisi lors de l'inscription</p>
            </div>
            
            <center>
                <a href="http://127.0.0.1:8000/login" class="login-button">
                    Se connecter maintenant
                </a>
            </center>
            
            <p style="margin-top: 30px;">Si vous avez des questions ou besoin d'assistance, n'hésitez pas à contacter notre équipe de support.</p>
            
            <p>Cordialement,<br>
            <strong>L'équipe Car Tracking App</strong></p>
        </div>
        
        <div class="email-footer">
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
            <p>&copy; 2025 Car Tracking App. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
