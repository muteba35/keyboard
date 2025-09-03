<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Identifiants — {{ $user->name }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            background: #f7f9fc;
            color: #333;
            font-size: 14px;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #4a00e0;
            margin-bottom: 30px;
        }
        .identifiant {
            background: white;
            border-left: 5px solid #4a00e0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            padding: 15px 20px;
        }
        .ligne {
            margin: 6px 0;
        }
        .label {
            font-weight: bold;
            color: #4a00e0;
            margin-right: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 11px;
            color: #999;
        }
    </style>
</head>
<body>

<h1>🔐 Identifiants de {{ $user->name }}</h1>

@foreach($identifiants as $identifiant)
    <div class="identifiant">
        <div class="ligne"><span class="label">Service :</span> {{ $identifiant->service }}</div>
        <div class="ligne"><span class="label">Nom utilisateur :</span> {{ $identifiant->nom_utilisateur }}</div>
        <div class="ligne"><span class="label"> Email :</span> {{ $identifiant->email }}</div>
        <div class="ligne"><span class="label">Mot de passe :</span> {{ $identifiant->mot_de_passe_clair ?? 'Non disponible' }}</div>
        <div class="ligne"><span class="label">URL du service :</span> {{ $identifiant->url_service }}</div>
        <div class="ligne"><span class="label">Dossier :</span> {{ $identifiant->dossier->nom ?? 'Aucun' }}</div>
        <div class="ligne"><span class="label">Description :</span> {{ $identifiant->description ?? '—' }}</div>
        <div class="ligne"><span class="label">Ajouté le :</span> {{ $identifiant->created_at->format('d/m/Y à H:i') }}</div>
    </div>
@endforeach

<div class="footer">
    Document généré par l’application Junior Muteba — {{ now()->format('d/m/Y à H:i') }}
</div>

</body>
</html>