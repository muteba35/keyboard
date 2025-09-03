<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe sécurisé (Import)</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            padding: 30px;
        }
        .container {
            background-color: #ffffff;
            border: 2px solid #28a745;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.15);
        }
        .title {
            color: #28a745;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .line {
            margin-bottom: 10px;
            font-size: 15px;
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
    <div class="title">✅ Mot de passe sécurisé détecté lors de l'import</div>

    <div class="line">Bonjour {{ $user->name }},</div>

    <div class="line">Le mot de passe pour le service <strong>{{ $service }}</strong> (utilisateur : <strong>{{ $identifiant }}</strong>) importé a été évalué comme <strong>sécurisé</strong>.</div>

    <div class="line">Aucune faiblesse majeure n’a été détectée. Il répond bien aux exigences de sécurité définies.</div>

    <div class="footer">— Système de gestion des identifiants Junior Muteba</div>
</div>
</body>
</html>