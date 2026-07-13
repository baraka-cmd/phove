<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message de contact — AHD-RDC</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: #f4f6f9; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);">

                    {{-- HEADER --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #D32F2F 0%, #b71c1c 100%); padding: 30px; text-align: center;">
                            <div style="font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: 1px; margin-bottom: 6px;">
                                📩 Nouveau message
                            </div>
                            <div style="font-size: 13px; color: rgba(255, 255, 255, 0.75); letter-spacing: 0.5px;">
                                AHD-RDC — Action Humanitaire pour le Développement en RD Congo
                            </div>
                        </td>
                    </tr>

                    {{-- CORPS --}}
                    <tr>
                        <td style="padding: 30px;">

                            <h1 style="font-size: 20px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0;">
                                {{ $contact->sujet }}
                            </h1>
                            <p style="font-size: 13px; color: #999; margin: 0 0 25px 0;">
                                Reçu le {{ $contact->created_at->format('d/m/Y à H:i') }}
                            </p>

                            {{-- Informations expéditeur --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; font-size: 14px;">
                                        <span style="color: #777; font-weight: 500;">Expéditeur</span>
                                    </td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; font-size: 14px; text-align: right;">
                                        <span style="color: #1a1a1a; font-weight: 600;">{{ $contact->nom }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; font-size: 14px;">
                                        <span style="color: #777; font-weight: 500;">Email</span>
                                    </td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; font-size: 14px; text-align: right;">
                                        <span style="color: #1a1a1a; font-weight: 600;">{{ $contact->email }}</span>
                                    </td>
                                </tr>
                                @if($contact->telephone)
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; font-size: 14px;">
                                        <span style="color: #777; font-weight: 500;">Téléphone</span>
                                    </td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; font-size: 14px; text-align: right;">
                                        <span style="color: #1a1a1a; font-weight: 600;">{{ $contact->telephone }}</span>
                                    </td>
                                </tr>
                                @endif
                                @if($contact->organisation)
                                <tr>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; font-size: 14px;">
                                        <span style="color: #777; font-weight: 500;">Organisation</span>
                                    </td>
                                    <td style="padding: 8px 0; border-bottom: 1px solid #e9ecef; font-size: 14px; text-align: right;">
                                        <span style="color: #1a1a1a; font-weight: 600;">{{ $contact->organisation }}</span>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 8px 0; font-size: 14px;">
                                        <span style="color: #777; font-weight: 500;">Motif</span>
                                    </td>
                                    <td style="padding: 8px 0; font-size: 14px; text-align: right;">
                                        <span style="color: #1a1a1a; font-weight: 600;">{{ $contact->motifLibelle() }}</span>
                                    </td>
                                </tr>
                            </table>

                            {{-- Message --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 14px; color: #1a1a1a; line-height: 1.8;">
                                        {!! nl2br(e($contact->message)) !!}
                                    </td>
                                </tr>
                            </table>

                            {{-- Actions --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align: center;">
                                <tr>
                                    <td>
                                        <a href="mailto:{{ $contact->email }}?subject=RE: {{ urlencode($contact->sujet) }}" style="display: inline-block; background: #16558F; color: #fff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; margin-right: 10px;">
                                            ✉️ Répondre
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td style="text-align: center; padding: 20px 30px; border-top: 1px solid #e9ecef;">
                            <p style="font-size: 11px; color: #bbb; margin: 0;">
                                Cet email a été envoyé automatiquement depuis le formulaire de contact d'AHD-RDC.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>