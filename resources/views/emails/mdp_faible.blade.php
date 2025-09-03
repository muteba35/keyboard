<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe faible</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #fff9e6; padding: 30px;">
    <div style="max-width: 600px; background: #fffbe6; padding: 20px; border-radius: 10px; border: 1px solid #f1c40f;">
        <h2 style="color: #f39c12;">⚠️ Mot de passe jugé faible</h2>
        <p>Bonjour {{ $user->name }},</p>
        <p>Suite à votre analyse, votre mot de passe présente les failles suivantes :</p>
        <ul>
            @foreach($messages as $msg)
                <li>❌ {{ $msg }}</li>
            @endforeach
        </ul>
        <p>👉 Nous vous recommandons de modifier votre mot de passe pour renforcer votre sécurité.</p>
        <p>Merci,<br><strong>L’équipe de sécurité</strong></p>
    </div>
</body>
</html>