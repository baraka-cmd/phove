<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport financier — AHD-RDC</title>
    <style>
        @page {
            margin: 15mm 18mm 18mm 18mm;
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
            width: 60px;
            height: 60px;
            border: 3px solid #16558F;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16pt;
            font-weight: 800;
            color: #16558F;
        }

        .logo-circle span { color: #D32F2F; }

        .org-info h1 {
            margin: 0;
            font-size: 15pt;
            font-weight: 800;
            color: #16558F;
            letter-spacing: 1px;
        }

        .org-info h1 .accent { color: #D32F2F; }

        .org-info .subtitle {
            font-size: 7pt;
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

        .header-right strong { color: #1a1a1a; font-size: 9pt; }

        /* ----------------------------------------------------------
           TITRE RAPPORT
           ---------------------------------------------------------- */
        .rapport-title-box {
            background: #16558F;
            color: #fff;
            text-align: center;
            padding: 14px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .rapport-title-box h1 {
            margin: 0 0 4px 0;
            font-size: 14pt;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .rapport-title-box .rapport-meta {
            font-size: 9pt;
            opacity: 0.85;
        }

        /* ----------------------------------------------------------
           SECTIONS
           ---------------------------------------------------------- */
        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 10pt;
            font-weight: 700;
            color: #16558F;
            border-bottom: 2px solid #16558F;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        .section-title.green { color: #28a745; border-bottom-color: #28a745; }
        .section-title.red { color: #D32F2F; border-bottom-color: #D32F2F; }

        /* ----------------------------------------------------------
           TABLEAUX
           ---------------------------------------------------------- */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table th {
            background: #f0f4f8;
            padding: 8px 10px;
            font-size: 8.5pt;
            font-weight: 700;
            color: #555;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        .info-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e8e8e8;
            font-size: 9pt;
            vertical-align: top;
        }

        .info-table td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .info-table .total-row {
            background: #f8f9fa;
            font-weight: 700;
            font-size: 10pt;
        }

        .info-table .total-row td {
            border-top: 2px solid #16558F;
        }

        .text-success { color: #28a745; }
        .text-danger { color: #D32F2F; }

        /* ----------------------------------------------------------
           SOLDE
           ---------------------------------------------------------- */
        .solde-box {
            padding: 16px 20px;
            border-radius: 8px;
            text-align: center;
            margin: 18px 0;
        }

        .solde-box.positif {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
        }

        .solde-box.negatif {
            background: #f8d7da;
            border: 2px solid #D32F2F;
            color: #721c24;
        }

        .solde-label {
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .solde-valeur {
            font-size: 18pt;
            font-weight: 800;
        }

        /* ----------------------------------------------------------
           COMMENTAIRE
           ---------------------------------------------------------- */
        .comment-box {
            background: #f8f9fa;
            border-left: 3px solid #16558F;
            padding: 10px 14px;
            margin: 16px 0;
            font-size: 8.5pt;
            color: #555;
            line-height: 1.6;
            white-space: pre-line;
        }

        /* ----------------------------------------------------------
           SIGNATURE
           ---------------------------------------------------------- */
        .signature-section {
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
            width: 100px;
            height: 65px;
            border: 2px dashed #ccc;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7.5pt;
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

        .footer strong { color: #666; }
        .footer .slogan { font-style: italic; margin-top: 3px; color: #aaa; }

        /* ----------------------------------------------------------
           FILIGRANE
           ---------------------------------------------------------- */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 70pt;
            color: rgba(22, 85, 143, 0.02);
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
    {{-- TITRE RAPPORT --}}
    {{-- ============================================ --}}
    <div class="rapport-title-box">
        <h1>RAPPORT FINANCIER</h1>
        <div class="rapport-meta">
            {{ $rapport->typeLibelle() }} — Période : {{ $rapport->periode }}
            @if($rapport->date_validation)
                — Validé le {{ $rapport->date_validation->format('d/m/Y') }}
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- INFORMATIONS GÉNÉRALES --}}
    {{-- ============================================ --}}
    <div class="section">
        <h3 class="section-title">INFORMATIONS DU RAPPORT</h3>
        <table class="info-table">
            <tr>
                <td style="width: 35%; color: #777; font-weight: 600;">Titre</td>
                <td>{{ $rapport->titre }}</td>
            </tr>
            <tr>
                <td style="color: #777; font-weight: 600;">Type</td>
                <td>{{ $rapport->typeLibelle() }}</td>
            </tr>
            <tr>
                <td style="color: #777; font-weight: 600;">Période</td>
                <td>{{ $rapport->periode }}</td>
            </tr>
            @if($rapport->date_debut)
            <tr>
                <td style="color: #777; font-weight: 600;">Date début</td>
                <td>{{ $rapport->date_debut->format('d/m/Y') }}</td>
            </tr>
            @endif
            @if($rapport->date_fin)
            <tr>
                <td style="color: #777; font-weight: 600;">Date fin</td>
                <td>{{ $rapport->date_fin->format('d/m/Y') }}</td>
            </tr>
            @endif
            <tr>
                <td style="color: #777; font-weight: 600;">Statut</td>
                <td>{{ $rapport->statutLibelle() }}</td>
            </tr>
            <tr>
                <td style="color: #777; font-weight: 600;">Date de génération</td>
                <td>{{ now()->format('d/m/Y à H:i') }}</td>
            </tr>
        </table>
    </div>

    {{-- ============================================ --}}
    {{-- ENTRÉES --}}
    {{-- ============================================ --}}
    <div class="section">
        <h3 class="section-title green">ENTRÉES (RESSOURCES)</h3>
        <table class="info-table">
            <tr>
                <td style="color: #777;">Dons reçus</td>
                <td class="text-success">{{ number_format($rapport->dons_recus, 2, ',', ' ') }} $</td>
            </tr>
            <tr>
                <td style="color: #777;">Cotisations reçues</td>
                <td class="text-success">{{ number_format($rapport->cotisations_recues, 2, ',', ' ') }} $</td>
            </tr>
            <tr>
                <td style="color: #777;">Subventions reçues</td>
                <td class="text-success">{{ number_format($rapport->subventions_recues, 2, ',', ' ') }} $</td>
            </tr>
            <tr>
                <td style="color: #777;">Autres entrées</td>
                <td class="text-success">{{ number_format($rapport->autres_entrees, 2, ',', ' ') }} $</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL ENTRÉES</td>
                <td class="text-success">{{ number_format($rapport->total_entrees, 2, ',', ' ') }} $</td>
            </tr>
        </table>
    </div>

    {{-- ============================================ --}}
    {{-- SORTIES --}}
    {{-- ============================================ --}}
    <div class="section">
        <h3 class="section-title red">SORTIES (DÉPENSES)</h3>
        <table class="info-table">
            <tr>
                <td style="color: #777;">Dépenses projets</td>
                <td class="text-danger">{{ number_format($rapport->depenses_projets, 2, ',', ' ') }} $</td>
            </tr>
            <tr>
                <td style="color: #777;">Salaires</td>
                <td class="text-danger">{{ number_format($rapport->salaires, 2, ',', ' ') }} $</td>
            </tr>
            <tr>
                <td style="color: #777;">Logistique</td>
                <td class="text-danger">{{ number_format($rapport->logistique, 2, ',', ' ') }} $</td>
            </tr>
            <tr>
                <td style="color: #777;">Frais administratifs</td>
                <td class="text-danger">{{ number_format($rapport->administratif, 2, ',', ' ') }} $</td>
            </tr>
            <tr>
                <td style="color: #777;">Autres sorties</td>
                <td class="text-danger">{{ number_format($rapport->autres_sorties, 2, ',', ' ') }} $</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL SORTIES</td>
                <td class="text-danger">{{ number_format($rapport->total_sorties, 2, ',', ' ') }} $</td>
            </tr>
        </table>
    </div>

    {{-- ============================================ --}}
    {{-- SOLDE --}}
    {{-- ============================================ --}}
    <div class="solde-box {{ $rapport->solde >= 0 ? 'positif' : 'negatif' }}">
        <div class="solde-label">{{ $rapport->solde >= 0 ? 'SOLDE EXCÉDENTAIRE' : 'SOLDE DÉFICITAIRE' }}</div>
        <div class="solde-valeur">{{ number_format($rapport->solde, 2, ',', ' ') }} $</div>
    </div>

    {{-- ============================================ --}}
    {{-- COMMENTAIRE --}}
    {{-- ============================================ --}}
    @if($rapport->commentaire)
    <div class="comment-box">
        <strong>Commentaire :</strong><br>
        {{ $rapport->commentaire }}
    </div>
    @endif

    {{-- ============================================ --}}
    {{-- SIGNATURES --}}
    {{-- ============================================ --}}
    <div class="signature-section">
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
            <div class="signature-name">Le Chargé des Finances</div>
            <div class="signature-role">Administration & Finances</div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- PIED DE PAGE --}}
    {{-- ============================================ --}}
    <div class="footer">
        <strong>AHD-RDC</strong> — Action Humanitaire pour le Développement en République Démocratique du Congo<br>
        Association Sans But Lucratif de droit congolais — Créée le 7 janvier 2017 à Minova, Sud-Kivu<br>
        <strong>Contact :</strong> +243 994 656 250 | contact@ahd-rdc.org | Minova, Territoire de Kalehe, Sud-Kivu, RD Congo<br>
        <span class="slogan">« Être ensemble pour le bien de la communauté »</span><br><br>
        Document généré le {{ now()->format('d/m/Y à H:i') }} — Ce rapport est un document officiel d'AHD-RDC.
    </div>

</body>
</html>