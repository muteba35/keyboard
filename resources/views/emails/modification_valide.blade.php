<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mot de passe sécurisé modifié</title>
     <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f4f8;
            padding: 30px;
            color: #333;
        }
        .container {
            background-color: #fff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }
        .header {
            font-size: 22px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin-bottom: 25px;
        }
        .content {
            font-size: 16px;
            line-height: 1.8;
        }
        .content strong {
            color: #007bff;
        }
        .secure-message {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            background-color: #e8f5e9;
            padding: 10px 15px;
            border-radius: 6px;
            text-align: center;
            margin: 25px 0;
        }
        .footer {
            font-size: 13px;
            color: #999;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">✅ Modification sécurisée effectuée</div>

        <div class="content">
            Bonjour {{ $user->name }},<br><br>

            Vous avez récemment modifié votre identifiant pour le service <strong>{{ $service }}</strong>
            avec le nom d’utilisateur <strong>{{ $identifiant }}</strong>.<br>

            Le nouveau mot de passe que vous avez choisi est <strong>sécurisé</strong> et ne présente aucune faiblesse connue.<br><br>

            Aucun changement supplémentaire n’est requis pour le moment. Continuez à adopter de bonnes pratiques de sécurité !
        </div>

        <div class="footer">
            — Application Junior Muteba
        </div>
    </div>
</body>
</html>