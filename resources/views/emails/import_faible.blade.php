<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe faible (Import)</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #fff3f3;
            padding: 30px;
        }
        .container {
            background-color: #fff;
            border: 2px solid #dc3545;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 4px 6px rgba(220, 53, 69, 0.15);
        }
        .title {
            color: #dc3545;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .line {
            margin-bottom: 10px;
            font-size: 15px;
        }
        .erreur {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
            padding: 10px;
            margin: 10px 0;
            font-size: 14px;
            border-radius: 5px;
        }
        .footer {
            margin-top: 25px;
            font-size: 13px;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="title">⚠️ Mot de passe faible détecté lors de l'import</div>

    <div class="line">Bonjour {{ $user->name }},</div>

    <div class="line">Le mot de passe pour le service <strong>{{ $service }}</strong> (utilisateur : <strong>{{ $nom_utilisateur }}</strong>) est considéré comme <strong>faible</strong>.</div>

    <div class="line">Voici les faiblesses détectées :</div>

    @foreach($erreurs as $erreur)
        <div class="erreur">- {{ $erreur }}</div>
    @endforeach

    <div class="line">Nous vous recommandons de modifier ce mot de passe dès que possible afin d'assurer une meilleure sécurité.</div>

    <div class="footer">— Système de gestion des identifiants Junior Muteba</div>
</div>
</body>
</html>