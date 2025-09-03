<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Code de vérification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background: white; border-radius: 8px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #2d3748;">Bonjour {{ $user->name }},</h2>
        <p style="font-size: 16px; color: #4a5568;">Nous avons reçu une demande de connexion à votre compte.</p>

        <p style="font-size: 18px; font-weight: bold; color: #1a202c;">Voici votre code de vérification :</p>

        <div style="text-align: center; margin: 20px 0;">
            <span style="font-size: 32px; font-weight: bold; background-color: #edf2f7; padding: 10px 20px; border-radius: 8px; color: #2d3748;">
                {{ $otp }}
            </span>
        </div>

        <p style="font-size: 14px; color: #718096;">Ce code est valide pour 5 minutes.</p>

        <p style="font-size: 14px; color: #e53e3e;">
            ⚠️ Si vous n'êtes pas à l'origine de cette demande, veuillez sécuriser immédiatement votre compte.
        </p>

        <p style="font-size: 14px; color: #4a5568;">Merci de votre confiance.<br>L'équipe de sécurité</p>
    </div>
</body>
</html>