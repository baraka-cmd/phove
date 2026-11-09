<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Maintenance — PHOVE</title>
    <style>
        :root { color-scheme: light; font-family: Arial, sans-serif; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 24px; background: #f1f5f9; color: #0f172a; }
        main { width: min(100%, 620px); padding: 48px 36px; border-radius: 18px; background: #fff; text-align: center; box-shadow: 0 18px 50px rgba(15, 23, 42, .08); }
        .mark { display: inline-grid; width: 64px; height: 64px; place-items: center; margin-bottom: 22px; border-radius: 50%; background: #ccfbf1; color: #0f766e; font-size: 28px; }
        h1 { margin: 0 0 14px; font-size: clamp(25px, 5vw, 34px); }
        p { margin: 0; color: #475569; line-height: 1.75; }
        small { display: block; margin-top: 28px; color: #64748b; }
    </style>
</head>
<body>
    <main>
        <div class="mark" aria-hidden="true">⚙</div>
        <h1>Site en maintenance</h1>
        <p>{{ $message }}</p>
        <small>PHOVE — Programme Humanitaire aux Orphelins, Veuves et Personnes Démunies</small>
    </main>
</body>
</html>
