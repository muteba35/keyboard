<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mot de passe faible détecté</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: auto; padding: 20px; background: #fff3f3; border: 1px solid #f5c6cb; }
        .warning { color: #721c24; font-weight: bold; margin-top: 10px; }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        ul { padding-left: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bonjour {{ $user->name }},</h2>

        <p>Nous avons détecté que le mot de passe utilisé pour le service <strong>{{ $service }}</strong> avec l’identifiant <strong>{{ $nom_utilisateur }}</strong> présente des <span class="warning">faiblesses</span>.</p>

        <p>Voici les points à corriger :</p>
        <ul>
            @foreach ($erreurs as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>

        <p>Nous vous recommandons de <strong>modifier le mot de passe directement sur le site de {{ $service }}</strong> (par exemple via les paramètres de sécurité de votre compte {{ $service }}).</p>

        <p>Vous pouvez également effectuer cette mise à jour depuis votre espace personnel dans notre application OAPS.</p>

        <a href="{{ route('login') }}" class="btn">Accéder à mon espace</a>

        <p style="margin-top: 30px;">— L’équipe OAPS</p>
    </div>
</body>
</html>