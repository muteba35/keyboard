<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mot de passe sécurisé</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: auto; padding: 20px; background: #f9f9f9; border: 1px solid #ccc; }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .highlight {
            background-color: #e0f7fa;
            padding: 10px;
            border-left: 4px solid #00796b;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bonjour {{ $user->name }},</h2>

        <p>Vous avez récemment ajouté un identifiant pour le service <strong>{{ $service }}</strong> avec l'identifiant : <strong>{{ $identifiant }}</strong>.</p>

        <div class="highlight">
            Bonne nouvelle ! Le mot de passe utilisé est jugé <strong>sécurisé</strong>.
        </div>

        <p>Nous vous recommandons néanmoins de le changer d'ici 6 mois pour garantir une sécurité continue.</p>

        <p>Merci d’utiliser notre système de gestion sécurisée.</p>

        <a href="{{ route('login') }}" class="btn">Accéder à votre espace</a>

        <p style="margin-top: 30px;">— L’équipe OAPS</p>
    </div>
</body>
</html>