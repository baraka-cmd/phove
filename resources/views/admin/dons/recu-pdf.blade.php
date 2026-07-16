<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu fiscal — AHD-RDC</title>
    <style>
        @page {
            margin: 12mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #1a1a1a;
            font-size: 10pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* ----------------------------------------------------------
           EN-TÊTE
           ---------------------------------------------------------- */
        .header {
            border-bottom: 3px solid #16558F;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-circle {
            width: 65px;
            height: 65px;
            border: 3px solid #16558F;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18pt;
            font-weight: 800;
            color: #16558F;
        }

        .logo-circle span {
            color: #D32F2F;
        }

        .org-info h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: 800;
            color: #16558F;
            letter-spacing: 1px;
        }

        .org-info h1 .accent {
            color: #D32F2F;
        }

        .org-info .subtitle {
            font-size: 7.5pt;
            color: #666;
            letter-spacing: 0.3px;
            margin-top: 2px;
        }

        .header-right {
            text-align: right;
            font-size: 8pt;
            color: #555;
            line-height: 1.5;
        }

        .header-right strong {
            color: #1a1a1a;
            font-size: 9pt;
        }

        /* ----------------------------------------------------------
           BLOC REÇU
           ---------------------------------------------------------- */
        .recu-box {
            background: #f0f4f8;
            border: 2px solid #16558F;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .recu-box .recu-title {
            font-size: 16pt;
            font-weight: 800;
            color: #16558F;
            letter-spacing: 2px;
            margin: 0 0 4px 0;
        }

        .recu-box .recu-number {
            font-size: 11pt;
            font-weight: 600;
            color: #1a1a1a;
            background: #fff;
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            border: 1px solid #16558F;
        }

        .recu-box .recu-date {
            font-size: 8pt;
            color: #777;
            margin-top: 6px;
        }

        /* ----------------------------------------------------------
           SECTIONS
           ---------------------------------------------------------- */
        .section {
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 10pt;
            font-weight: 700;
            color: #16558F;
            border-bottom: 2px solid #16558F;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .info-grid td {
            padding: 6px 0;
            border-bottom: 1px solid #e8e8e8;
            font-size: 9.5pt;
            vertical-align: top;
        }

        .info-grid td:first-child {
            color: #777;
            font-weight: 600;
            width: 38%;
            padding-right: 10px;
        }

        .info-grid td:last-child {
            color: #1a1a1a;
            font-weight: 600;
            text-align: right;
        }

        .montant-highlight {
            font-size: 13pt;
            font-weight: 800;
            color: #28a745;
        }

        /* ----------------------------------------------------------
           MENTIONS
           ---------------------------------------------------------- */
        .mention-box {
            background: #f8f9fa;
            border-left: 3px solid #16558F;
            padding: 10px 14px;
            margin: 16px 0;
            font-size: 8pt;
            color: #555;
            line-height: 1.5;
        }

        .mention-box strong {
            color: #1a1a1a;
        }

        /* ----------------------------------------------------------
           SIGNATURES
           ---------------------------------------------------------- */
        .signatures {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .signature-block {
            width: 40%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #999;
            margin: 35px 0 6px 0;
        }

        .signature-name {
            font-size: 8.5pt;
            font-weight: 700;
            color: #1a1a1a;
        }

        .signature-role {
            font-size: 7.5pt;
            color: #777;
        }

        .cachet {
            width: 110px;
            height: 70px;
            border: 2px dashed #ccc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #aaa;
            text-align: center;
        }

        /* ----------------------------------------------------------
           PIED DE PAGE
           ---------------------------------------------------------- */
        .footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 7pt;
            color: #999;
            line-height: 1.6;
        }

        .footer strong {
            color: #666;
        }

        .footer .slogan {
            font-style: italic;
            margin-top: 3px;
            color: #aaa;
        }

        /* ----------------------------------------------------------
           FILIGRANE
           ---------------------------------------------------------- */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80pt;
            color: rgba(22, 85, 143, 0.025);
            font-weight: 800;
            pointer-events: none;
            z-index: -1;
            white-space: nowrap;
        }
    </style>
</head>
<body>

    {{-- Filigrane --}}
    <div class="watermark">AHD-RDC</div>

    {{-- ============================================ --}}
    {{-- EN-TÊTE --}}
    {{-- ============================================ --}}
    <div class="header">
        <div class="header-left">
            <div class="logo-circle">AHD<span>-</span>RDC</div>
            <div class="org-info">
                <h1>AHD<span class="accent">-</span>RDC</h1>
                <div class="subtitle">ACTION HUMANITAIRE POUR LE DÉVELOPPEMENT EN RD CONGO</div>
                <div class="subtitle">Association Sans But Lucratif — Agréée le 7 janvier 2017</div>
            </div>
        </div>
        <div class="header-right">
            <strong>Siège social</strong><br>
            Minova, Territoire de Kalehe<br>
            Province du Sud-Kivu, RD Congo<br><br>
            <strong>Tél :</strong> +243 994 656 250<br>
            <strong>Email :</strong> contact@ahd-rdc.org
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- BLOC REÇU FISCAL --}}
    {{-- ============================================ --}}
    <div class="recu-box">
        <div class="recu-title">REÇU FISCAL</div>
        <div class="recu-number">N° {{ $don->numero_recu }}</div>
        <div class="recu-date">Émis le {{ $don->date_recu ? $don->date_recu->format('d/m/Y') : now()->format('d/m/Y') }} à Minova, Sud-Kivu</div>
    </div>

    {{-- ============================================ --}}
    {{-- BÉNÉFICIAIRE DU REÇU (DONATEUR) --}}
    {{-- ============================================ --}}
    <div class="section">
        <h3 class="section-title">BÉNÉFICIAIRE DU REÇU</h3>
        <table class="info-grid">
            <tr>
                <td>Nom complet</td>
                <td>{{ $don->donateur_nom }}</td>
            </tr>
            @if($don->donateur_email)
            <tr>
                <td>Adresse email</td>
                <td>{{ $don->donateur_email }}</td>
            </tr>
            @endif
            @if($don->donateur_telephone)
            <tr>
                <td>Téléphone</td>
                <td>{{ $don->donateur_telephone }}</td>
            </tr>
            @endif
            @if($don->donateur_adresse)
            <tr>
                <td>Adresse postale</td>
                <td>{{ $don->donateur_adresse }}</td>
            </tr>
            @endif
            <tr>
                <td>Type de donateur</td>
                <td>
                    @php
                        echo match($don->type_donateur) {
                            'particulier' => 'Personne physique',
                            'organisation' => 'Organisation / Entreprise',
                            'etat' => 'Institution étatique',
                            'anonyme' => 'Donateur anonyme',
                            default => $don->type_donateur
                        };
                    @endphp
                </td>
            </tr>
        </table>
    </div>

    {{-- ============================================ --}}
    {{-- DÉTAILS DU DON --}}
    {{-- ============================================ --}}
    <div class="section">
        <h3 class="section-title">DÉTAILS DU DON</h3>
        <table class="info-grid">
            <tr>
                <td>Montant du don</td>
                <td class="montant-highlight">{{ number_format($don->montant, 2) }} {{ $don->devise }}</td>
            </tr>
            <tr>
                <td>Type de don</td>
                <td>{{ $don->type_don === 'ponctuel' ? 'Don ponctuel (unique)' : 'Don récurrent (mensuel)' }}</td>
            </tr>
            <tr>
                <td>Mode de paiement</td>
                <td>{{ $don->modePaiementLibelle() }}</td>
            </tr>
            @if($don->reference_transaction)
            <tr>
                <td>Référence transaction</td>
                <td>{{ $don->reference_transaction }}</td>
            </tr>
            @endif
            @if($don->projet)
            <tr>
                <td>Projet soutenu</td>
                <td>{{ $don->projet->titre }}</td>
            </tr>
            @else
            <tr>
                <td>Affectation</td>
                <td>Fonds général — Là où les besoins sont les plus urgents</td>
            </tr>
            @endif
            <tr>
                <td>Date du don</td>
                <td>{{ $don->date_don ? $don->date_don->format('d/m/Y') : '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- ============================================ --}}
    {{-- MENTIONS FISCALES --}}
    {{-- ============================================ --}}
    <div class="mention-box">
        <strong>📌 Mention fiscale :</strong> Le présent reçu est délivré conformément à la loi n°004-2001 du 20 juillet 2001 
        portant dispositions générales applicables aux associations sans but lucratif en République Démocratique du Congo. 
        Ce don ouvre droit à une déduction fiscale dans les conditions prévues par la législation en vigueur. 
        <strong>Conservez ce reçu pour vos déclarations fiscales.</strong>
    </div>

    {{-- ============================================ --}}
    {{-- SIGNATURES --}}
    {{-- ============================================ --}}
    <div class="signatures">
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-name">Dr AKSANTI DUNIA Steeng</div>
            <div class="signature-role">Coordonnateur National — AHD-RDC</div>
        </div>
        <div class="cachet">
            Cachet de<br>l'organisation<br>AHD-RDC
        </div>
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-name">{{ $don->donateur_nom }}</div>
            <div class="signature-role">Donateur</div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- PIED DE PAGE --}}
    {{-- ============================================ --}}
    <div class="footer">
        <strong>AHD-RDC</strong> — Action Humanitaire pour le Développement en République Démocratique du Congo<br>
        Association Sans But Lucratif de droit congolais — Créée le 7 janvier 2017 à Minova, Sud-Kivu<br>
        <strong>Contact :</strong> +243 994 656 250 | contact@ahd-rdc.org | Minova, Territoire de Kalehe, Sud-Kivu, RD Congo<br>
        <span class="slogan">« Être ensemble pour le bien de la communauté »</span>
    </div>

</body>
</html>