<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Alerte de sécurité - KeyManage</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f0f4f8; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        
        <!-- En-tête -->
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 30px;">
            <img src="https://cdn-icons-png.flaticon.com/512/3064/3064197.png" width="32" height="32" alt="Icône sécurité" style="vertical-align: middle;">
            <h1 style="margin: 0; font-size: 22px; color: #2b6cb0;">Alerte de Sécurité</h1>
        </div>

        <!-- Corps du message -->
        <p style="font-size: 16px; color: #2d3748;">Bonjour <strong>{{ $user->name }}</strong>,</p>

        <p style="font-size: 16px; color: #4a5568;">
            Notre système de sécurité a détecté au niveau de votre gestionnaire KeyManage<strong>3 tentatives échouées</strong> de connexion à votre compte.
        </p>

        <div style="background-color: #fef2f2; color: #c53030; padding: 15px; border-left: 5px solid #e53e3e; margin: 20px 0; border-radius: 5px;">
            ⚠️ <strong>Important :</strong> Si ce n’est pas vous, connectez-vous immédiatement et changez votre mot de passe pour éviter tout accès non autorisé.
        </div>

        <!-- BOUTON -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="http://localhost:8000/identifiants" style="background-color: #2b6cb0; color: white; text-decoration: none; padding: 12px 20px; border-radius: 5px; font-weight: bold; display: inline-block;">
                🔐 Changer mon mot de passe
            </a>
        </div>

        <p style="font-size: 15px; color: #4a5568;">
            Si vous êtes à l’origine de ces tentatives, vous pouvez ignorer cet avertissement.
        </p>

        <!-- Signature -->
        <p style="font-size: 14px; color: #718096; margin-top: 30px;">
            Merci pour votre vigilance.<br>
            — L’équipe de sécurité <strong>KeyManage</strong>
        </p>

        <!-- Pied de page -->
        <div style="margin-top: 40px; text-align: center; font-size: 13px; color: #a0aec0;">
            <hr style="border: none; border-top: 1px solid #e2e8f0;">
            Vous recevez ce message parce que votre compte est protégé par le système de sécurité de <strong>KeyManage</strong>.
        </div>
    </div>
</body>
</html>