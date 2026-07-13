<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de votre don — AHD-RDC</title>
    <link rel="stylesheet" href="{{ asset('assets/css/emails/don-confirmation.css') }}">
</head>
<body>

    {{-- Conteneur principal --}}
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="email-bg">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" class="email-wrapper">

                    {{-- ============================================ --}}
                    {{-- HEADER --}}
                    {{-- ============================================ --}}
                    <tr>
                        <td class="email-header">
                            <div class="email-logo">
                                AHD<span class="email-logo-accent">-</span>RDC
                            </div>
                            <div class="email-header-subtitle">
                                Action Humanitaire pour le Développement en RD Congo
                            </div>
                        </td>
                    </tr>

                    {{-- ============================================ --}}
                    {{-- CORPS DU MESSAGE --}}
                    {{-- ============================================ --}}
                    <tr>
                        <td class="email-body">

                            {{-- Titre --}}
                            <h1 class="email-title">Merci pour votre générosité ! 🙏</h1>
                            <p class="email-text">
                                Cher(e) <strong>{{ $don->donateur_nom }}</strong>,<br><br>
                                Nous avons bien reçu votre don. Votre soutien est essentiel pour nous permettre 
                                de continuer notre mission humanitaire auprès des communautés vulnérables 
                                de la République Démocratique du Congo.
                            </p>

                            {{-- Détails du don --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="don-details">
                                <tr>
                                    <td class="don-detail-td">
                                        <span class="don-detail-label">Numéro de don</span>
                                    </td>
                                    <td class="don-detail-td don-detail-right">
                                        <span class="don-detail-value don-detail-highlight">{{ $don->numero_recu }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="don-detail-td">
                                        <span class="don-detail-label">Montant</span>
                                    </td>
                                    <td class="don-detail-td don-detail-right">
                                        <span class="don-detail-value don-detail-highlight">{{ number_format($don->montant, 2) }} {{ $don->devise }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="don-detail-td">
                                        <span class="don-detail-label">Type de don</span>
                                    </td>
                                    <td class="don-detail-td don-detail-right">
                                        <span class="don-detail-value">{{ $don->type_don === 'ponctuel' ? 'Don ponctuel' : 'Don récurrent mensuel' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="don-detail-td">
                                        <span class="don-detail-label">Mode de paiement</span>
                                    </td>
                                    <td class="don-detail-td don-detail-right">
                                        <span class="don-detail-value">{{ $don->modePaiementLibelle() }}</span>
                                    </td>
                                </tr>
                                @if($don->projet)
                                <tr>
                                    <td class="don-detail-td">
                                        <span class="don-detail-label">Projet soutenu</span>
                                    </td>
                                    <td class="don-detail-td don-detail-right">
                                        <span class="don-detail-value">{{ Str::limit($don->projet->titre, 40) }}</span>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="don-detail-td don-detail-last">
                                        <span class="don-detail-label">Date</span>
                                    </td>
                                    <td class="don-detail-td don-detail-last don-detail-right">
                                        <span class="don-detail-value">{{ $don->date_don->format('d/m/Y') }}</span>
                                    </td>
                                </tr>
                            </table>

                            {{-- Message du donateur --}}
                            @if($don->message_donateur)
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="don-message">
                                <tr>
                                    <td class="don-message-td">
                                        « {{ $don->message_donateur }} »
                                    </td>
                                </tr>
                            </table>
                            @endif

                            {{-- Reçu fiscal --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="don-recu">
                                <tr>
                                    <td class="don-recu-td">
                                        <strong class="don-recu-title">📄 Reçu fiscal</strong>
                                        Votre reçu fiscal vous sera envoyé dans les plus brefs délais. 
                                        Conservez-le pour vos déclarations fiscales. 
                                        Numéro de référence : <strong>{{ $don->numero_recu }}</strong>
                                    </td>
                                </tr>
                            </table>

                            {{-- Bouton --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="don-btn-table">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/') }}" class="don-btn">
                                            Retourner sur le site
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ============================================ --}}
                    {{-- FOOTER --}}
                    {{-- ============================================ --}}
                    <tr>
                        <td class="email-footer">
                            <p class="email-footer-text">
                                <strong>AHD-RDC</strong> — Action Humanitaire pour le Développement en RD Congo
                            </p>
                            <p class="email-footer-text">
                                Minova, Territoire de Kalehe, Sud-Kivu, RD Congo
                            </p>
                            <p class="email-footer-text">
                                contact@ahd-rdc.org | +243 994 656 250
                            </p>
                            <p class="email-slogan">
                                « Être ensemble pour le bien de la communauté »
                            </p>
                            <p class="email-disclaimer">
                                Cet email a été envoyé automatiquement. Si vous n'êtes pas à l'origine de ce don, 
                                veuillez nous contacter immédiatement.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>