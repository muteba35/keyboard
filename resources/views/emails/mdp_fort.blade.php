<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe robuste</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 30px;">
    <div style="max-width: 600px; background: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0 0 12px rgba(0,0,0,0.1);">
        <h2 style="color: #2ecc71;">🔒 Mot de passe robuste détecté</h2>
        <p>Bonjour {{ $user->name }},</p>
        <p>Votre mot de passe respecte toutes les bonnes pratiques de sécurité actuelles :</p>
        <ul>
            <li>✔️ Longueur suffisante</li>
            <li>✔️ Majuscules et minuscules présentes</li>
            <li>✔️ Chiffres et caractères spéciaux inclus</li>
            <li>✔️ Aucun compromis détecté</li>
        </ul>
        <p>💪 Vous êtes bien protégé.</p>
        <p>Merci,<br><strong>L’équipe de sécurité</strong></p>
    </div>
</body>
</html>