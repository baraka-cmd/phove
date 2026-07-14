<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Page introuvable | AHD-RDC</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    
    <style>
        :root {
            --color-primary: #16558F;
            --color-primary-dark: #0f3f6b;
            --color-accent: #D32F2F;
            --font-primary: 'Poppins', sans-serif;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: var(--font-primary);
            background: linear-gradient(135deg, #f0f2f5 0%, #e8ecf1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            text-align: center;
            max-width: 550px;
        }
        
        .error-code {
            font-size: clamp(6rem, 15vw, 10rem);
            font-weight: 900;
            color: var(--color-primary);
            line-height: 1;
            letter-spacing: -3px;
            margin-bottom: 10px;
            position: relative;
        }
        
        .error-code span {
            color: var(--color-accent);
        }
        
        .error-icon {
            font-size: 3rem;
            color: var(--color-accent);
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 12px;
        }
        
        .error-text {
            font-size: 0.95rem;
            color: #666;
            line-height: 1.7;
            margin-bottom: 30px;
        }
        
        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            font-family: var(--font-primary);
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .btn-primary {
            background: var(--color-primary);
            color: #fff;
            border-color: var(--color-primary);
        }
        
        .btn-primary:hover {
            background: var(--color-primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(22, 85, 143, 0.3);
        }
        
        .btn-outline {
            background: #fff;
            color: var(--color-primary);
            border-color: var(--color-primary);
        }
        
        .btn-outline:hover {
            background: var(--color-primary);
            color: #fff;
            transform: translateY(-2px);
        }
        
        .error-logo {
            font-size: 20px;
            font-weight: 800;
            color: var(--color-primary);
            margin-top: 40px;
            letter-spacing: 1px;
        }
        
        .error-logo span {
            color: var(--color-accent);
        }
        
        .error-slogan {
            font-size: 12px;
            color: #999;
            font-style: italic;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-map-signs"></i>
        </div>
        <div class="error-code">4<span>0</span>4</div>
        <h1 class="error-title">Page introuvable</h1>
        <p class="error-text">
            La page que vous recherchez n'existe pas ou a été déplacée. 
            Vérifiez l'URL ou retournez à l'accueil pour continuer votre navigation.
        </p>
        <div class="error-actions">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="fas fa-home"></i> Retour à l'accueil
            </a>
            <a href="{{ url('/contact') }}" class="btn btn-outline">
                <i class="fas fa-envelope"></i> Nous contacter
            </a>
        </div>
        <div class="error-logo">AHD<span>-</span>RDC</div>
        <p class="error-slogan">« Être ensemble pour le bien de la communauté »</p>
    </div>
</body>
</html>