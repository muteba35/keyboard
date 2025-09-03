<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Analyse de votre mot de passe</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 30px;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #4a00e0;
        }

        ul {
            padding-left: 20px;
        }

        li {
            margin-bottom: 8px;
        }

        .warning {
            color: #e74c3c;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bonjour {{ $user->name }},</h2>

        <p>Suite à votre demande d’analyse, voici les résultats concernant la robustesse de votre mot de passe :</p>

        <p class="warning">❗ Failles détectées :</p>
        <ul>
            @foreach($messages as $msg)
                <li>🔸 {{ $msg }}</li>
            @endforeach
        </ul>

        <p>Nous vous recommandons vivement de modifier ce mot de passe dans les plus brefs délais, notamment sur les services sensibles (emails, réseaux sociaux, banques, etc.) où ce mot de passe est utilisé.</p>

        <p>Pour renforcer votre sécurité :</p>
        <ul>
            <li>✅ Utilisez un mot de passe d'au moins 12 caractères</li>
            <li>✅ Mélangez majuscules, minuscules, chiffres et symboles</li>
            <li>✅ Évitez d’utiliser des mots de passe déjà compromis</li>
            <li>✅ Changez régulièrement vos mots de passe critiques</li>
        </ul>

        <p class="footer">
            Ce message vous est envoyé automatiquement par notre système d’analyse de robustesse.<br>
            Restez vigilant et sécurisez vos accès.
        </p>

        <p class="footer">
            🔐 <strong>L’équipe de sécurité</strong>
        </p>
    </div>
</body>
</html>