<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mot de passe faible après modification</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff3f3; padding: 20px; color: #721c24; }
        .container { background-color: #ffffff; padding: 20px; border-left: 6px solid #dc3545; border-radius: 8px; }
        .header { font-size: 20px; font-weight: bold; margin-bottom: 15px; color: #dc3545; }
        .content { font-size: 16px; line-height: 1.6; }
        .footer { margin-top: 20px; font-size: 13px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">⚠️ Mot de passe faible détecté</div>

        <div class="content">
            Bonjour {{ $user->name }},<br><br>

            Vous avez modifié votre identifiant pour le service <strong>{{ $service }}</strong>
            avec le nom d’utilisateur <strong>{{ $nom_utilisateur }}</strong>.<br>

            Cependant, le mot de passe choisi présente des faiblesses :
            <ul>
                @foreach ($erreurs as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>

            Nous vous recommandons vivement de mettre à jour ce mot de passe dans le service concerné afin d'assurer la sécurité de vos données.
        </div>

        <div class="footer">
            — Application Junior Muteba
        </div>
    </div>
</body>