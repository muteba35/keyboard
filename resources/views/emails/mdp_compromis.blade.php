<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe compromis</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #fff0f0; padding: 30px;">
    <div style="max-width: 600px; background: #ffecec; padding: 20px; border-radius: 10px; border: 1px solid #e74c3c;">
        <h2 style="color: #e74c3c;">🚨 Mot de passe compromis</h2>
        <p>Bonjour {{ $user->name }},</p>
        <p>⚠️ Le mot de passe que vous avez analysé apparaît dans une base de données de fuites publiques.</p>
        <p>🔐 Il est donc considéré comme <strong>compromis</strong>.</p>
        <p>
            Veuillez <strong>le changer immédiatement</strong> sur tous les services où il est utilisé (email, réseaux sociaux, comptes professionnels, etc.).
        </p>
        <p>Pour votre sécurité, utilisez un mot de passe unique et complexe pour chaque service.</p>
        <p>Merci,<br><strong>L’équipe de sécurité</strong></p>
    </div>
</body>
</html>