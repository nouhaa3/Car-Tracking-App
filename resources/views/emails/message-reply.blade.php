<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réponse à votre message</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #344966 0%, #546A88 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #344966;
            margin-bottom: 20px;
        }
        .original-message {
            background-color: #f8f9fa;
            border-left: 4px solid #344966;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .original-message-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .original-message-content {
            color: #495057;
            font-style: italic;
        }
        .reply-section {
            margin: 30px 0;
            padding: 20px;
            background-color: #fff;
            border: 2px solid #e9ecef;
            border-radius: 8px;
        }
        .reply-label {
            font-size: 14px;
            color: #344966;
            font-weight: 600;
            margin-bottom: 12px;
            display: block;
        }
        .reply-content {
            color: #212529;
            white-space: pre-wrap;
            line-height: 1.8;
        }
        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 13px;
        }
        .footer a {
            color: #344966;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #344966 0%, #546A88 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🚗 Car Tracking App</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Bonjour {{ $contactMessage->prenom }} {{ $contactMessage->nom }},</p>
            
            <p>Merci d'avoir pris le temps de nous contacter. Notre équipe a examiné votre message et nous sommes ravis de vous apporter une réponse.</p>
            
            <div class="original-message">
                <div class="original-message-label">Votre message :</div>
                <div class="original-message-content">{{ $contactMessage->message }}</div>
            </div>
            
            <div class="reply-section">
                <span class="reply-label">📩 Notre réponse :</span>
                <div class="reply-content">{{ $replyContent }}</div>
            </div>
            
            <div class="signature">
                <strong>{{ $adminName }}</strong><br>
                Équipe Support - Car Tracking App<br>
                <a href="mailto:support@cartracking.com">support@cartracking.com</a>
            </div>
            
            <center>
                <a href="http://127.0.0.1:8000" class="button">Visiter le site</a>
            </center>
            
            <p style="margin-top: 30px; font-size: 13px; color: #6c757d;">
                Si vous avez d'autres questions, n'hésitez pas à nous contacter à nouveau. Nous sommes là pour vous aider !
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Car Tracking App. Tous droits réservés.</p>
            <p>Cet email a été envoyé en réponse à votre demande de contact.</p>
        </div>
    </div>
</body>
</html>
