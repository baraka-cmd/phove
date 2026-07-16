<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu fiscal — AHD-RDC</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; padding: 30px; display: flex; justify-content: center; }
        .recu-container { max-width: 750px; width: 100%; }
        .recu-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); overflow: hidden; }
        .recu-header { background: linear-gradient(135deg, #16558F 0%, #0f3f6b 100%); color: #fff; padding: 30px 35px; text-align: center; }
        .recu-header h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .recu-header .recu-logo { font-size: 28px; font-weight: 800; letter-spacing: 1px; }
        .recu-header .recu-logo span { color: #D32F2F; }
        .recu-header p { font-size: 12px; opacity: 0.75; margin-top: 6px; }
        .recu-title { text-align: center; padding: 18px; background: #fafbfc; border-bottom: 1px solid #eef0f4; font-size: 16px; font-weight: 700; color: #1a1a1a; }
        .recu-body { padding: 30px 35px; }
        .recu-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .recu-row:last-child { border-bottom: none; }
        .recu-label { color: #888; font-weight: 500; }
        .recu-value { color: #1a1a1a; font-weight: 600; text-align: right; }
        .recu-highlight { font-size: 18px; font-weight: 800; color: #28a745; }
        .recu-footer { padding: 18px 35px; background: #fafbfc; border-top: 1px solid #eef0f4; text-align: center; font-size: 11px; color: #999; line-height: 1.6; }
        .recu-actions { display: flex; gap: 10px; justify-content: center; margin-top: 20px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; border: 2px solid transparent; transition: all 0.3s; }
        .btn-primary { background: #16558F; color: #fff; border-color: #16558F; }
        .btn-primary:hover { background: #0f3f6b; }
        .btn-outline { background: #fff; color: #16558F; border-color: #16558F; }
        .btn-outline:hover { background: #16558F; color: #fff; }
        @media print { body { background: #fff; padding: 0; } .recu-actions { display: none; } .recu-card { box-shadow: none; border: none; } }
    </style>
</head>
<body>
    <div class="recu-container">
        <div class="recu-card">
            <div class="recu-header">
                <div class="recu-logo">AHD<span>-</span>RDC</div>
                <h1>Reçu Fiscal</h1>
                <p>Action Humanitaire pour le Développement en RD Congo</p>
            </div>
            <div class="recu-title">N° {{ $don->numero_recu }}</div>
            <div class="recu-body">
                <div class="recu-row">
                    <span class="recu-label">Donateur</span>
                    <span class="recu-value">{{ $don->donateur_nom }}</span>
                </div>
                @if($don->donateur_email)
                <div class="recu-row">
                    <span class="recu-label">Email</span>
                    <span class="recu-value">{{ $don->donateur_email }}</span>
                </div>
                @endif
                <div class="recu-row">
                    <span class="recu-label">Montant du don</span>
                    <span class="recu-value recu-highlight">{{ number_format($don->montant, 2) }} {{ $don->devise }}</span>
                </div>
                <div class="recu-row">
                    <span class="recu-label">Type de don</span>
                    <span class="recu-value">{{ $don->type_don === 'ponctuel' ? 'Don ponctuel' : 'Don récurrent' }}</span>
                </div>
                <div class="recu-row">
                    <span class="recu-label">Mode de paiement</span>
                    <span class="recu-value">{{ $don->modePaiementLibelle() }}</span>
                </div>
                @if($don->projet)
                <div class="recu-row">
                    <span class="recu-label">Projet soutenu</span>
                    <span class="recu-value">{{ $don->projet->titre }}</span>
                </div>
                @endif
                <div class="recu-row">
                    <span class="recu-label">Date du don</span>
                    <span class="recu-value">{{ $don->date_don ? $don->date_don->format('d/m/Y') : '—' }}</span>
                </div>
                <div class="recu-row">
                    <span class="recu-label">Date d'émission</span>
                    <span class="recu-value">{{ $don->date_recu ? $don->date_recu->format('d/m/Y') : now()->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="recu-footer">
                <strong>AHD-RDC</strong> — Minova, Territoire de Kalehe, Sud-Kivu, RD Congo<br>
                contact@ahd-rdc.org | +243 994 656 250<br>
                « Être ensemble pour le bien de la communauté »<br>
                <span style="margin-top: 6px; display: block;">Ce reçu fiscal est délivré conformément à la réglementation en vigueur.</span>
            </div>
        </div>
        <div class="recu-actions">
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Imprimer</button>
            <a href="{{ route('admin.dons.show', $don) }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    <script src="{{ asset('assets/fontawesome/js/all.min.js') }}"></script>
</body>
</html>