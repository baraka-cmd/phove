<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AHD-RDC</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f0f4f1; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { background: white; padding: 60px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); text-align: center; max-width: 600px; }
        .logo { font-size: 3rem; font-weight: 800; color: #1a5632; margin-bottom: 10px; }
        .logo span { color: #e8a817; }
        h1 { font-size: 1.5rem; color: #333; margin-bottom: 10px; }
        p { color: #777; margin-bottom: 30px; }
        .info { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px; text-align: left; }
        .info strong { color: #1a5632; }
        .btn { display: inline-block; padding: 12px 30px; background: #dc3545; color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: 0.3s; }
        .btn:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">AHD<span>-</span>RDC</div>
        <h1>🎉 Bienvenue, {{ Auth::user()->nom }} !</h1>
        <p>Vous êtes connecté au dashboard d'administration.</p>
        <div class="info">
            <p><strong>Email :</strong> {{ Auth::user()->email }}</p>
            <p><strong>Rôle :</strong> {{ Auth::user()->role }}</p>
            <p><strong>Dernière connexion :</strong> {{ Auth::user()->derniere_connexion->format('d/m/Y à H:i') }}</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn">Se déconnecter</button>
        </form>
    </div>
</body>
</html>