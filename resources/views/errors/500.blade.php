<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Erreur interne</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .error-container {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 50px;
            max-width: 700px;
            margin: 80px auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .error-icon {
            font-size: 60px;
            margin-bottom: 20px;
            color: #e74c3c;
        }
        .error-title {
            font-size: 36px;
            color: #c0392b;
            margin-bottom: 10px;
        }
        .error-message {
            font-size: 18px;
            color: #555;
        }
        .btn-home {
            margin-top: 25px;
            padding: 10px 25px;
            font-size: 16px;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
        }
        .btn-home:hover {
            background-color: #0056b3;
        }
        .diamond {
            font-size: 22px;
            margin-top: 20px;
            color: #9b59b6;
        }
    </style>
</head>
<body>

    <div class="error-container">
        <div class="error-icon">💥</div>
        <h1 class="error-title">500 - Erreur interne</h1>
        <p class="error-message">Une erreur inattendue s'est produite sur le serveur. Veuillez réessayer plus tard.</p>
        <a href="/login" class="btn-home">Retour à l'accueil</a>
        <div class="diamond">♦️♦️♦️</div>
    </div>

</body>
</html>