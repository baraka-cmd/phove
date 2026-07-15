<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suite de votre demande — AHD-RDC</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: #f4f6f9; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);">

                    {{-- HEADER --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #e67e22 0%, #d35400 100%); padding: 35px 30px; text-align: center;">
                            <div style="font-size: 28px; font-weight: 800; color: #ffffff; letter-spacing: 1px; margin-bottom: 8px;">
                                📋 Suite de votre demande
                            </div>
                            <div style="font-size: 13px; color: rgba(255, 255, 255, 0.8); letter-spacing: 0.5px;">
                                AHD-RDC — Action Humanitaire pour le Développement en RD Congo
                            </div>
                        </td>
                    </tr>

                    {{-- CORPS --}}
                    <tr>
                        <td style="padding: 30px;">

                            <h1 style="font-size: 22px; font-weight: 700; color: #1a1a1a; margin: 0 0 8px 0;">
                                Suite à votre demande d'adhésion
                            </h1>
                            <p style="font-size: 14px; color: #666; margin: 0 0 25px 0; line-height: 1.6;">
                                Cher(e) <strong>{{ $membre->nomComplet() }}</strong>,<br><br>
                                Nous avons bien étudié votre demande d'adhésion à <strong>AHD-RDC</strong>. 
                                Après examen, le Conseil d'Administration n'a malheureusement pas pu 
                                donner une suite favorable à votre demande.
                            </p>

                            {{-- Motif --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: #fef5e7; border-left: 4px solid #e67e22; border-radius: 6px; padding: 14px 18px; margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 13px; color: #856404; line-height: 1.6;">
                                        <strong style="display: block; font-size: 15px; margin-bottom: 4px;">Motif du refus</strong>
                                        {{ $motif }}
                                    </td>
                                </tr>
                            </table>

                            {{-- Détails --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 8px 0; font-size: 14px;">
                                        <span style="color: #777; font-weight: 500;">Demande déposée le</span>
                                    </td>
                                    <td style="padding: 8px 0; font-size: 14px; text-align: right;">
                                        <span style="color: #1a1a1a; font-weight: 600;">{{ $membre->created_at->format('d/m/Y') }}</span>
                                    </td>
                                </tr>
                            </table>

                            {{-- Message d'encouragement --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background: #e8f0f8; border-left: 4px solid #16558F; border-radius: 6px; padding: 14px 18px; margin-bottom: 25px;">
                                <tr>
                                    <td style="font-size: 13px; color: #0f3f6b; line-height: 1.6;">
                                        <strong style="display: block; font-size: 15px; margin-bottom: 4px;">🙏 Nous vous remercions</strong>
                                        Nous vous remercions pour l'intérêt que vous portez à notre organisation. 
                                        Cette décision ne remet pas en cause votre engagement. 
                                        Vous pouvez continuer à soutenir AHD-RDC en tant que sympathisant 
                                        ou en participant à nos activités sur le terrain.
                                    </td>
                                </tr>
                            </table>

                            {{-- Bouton --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align: center; margin-bottom: 20px;">
                                <tr>
                                    <td>
                                        <a href="{{ url('/') }}" style="display: inline-block; background: #16558F; color: #fff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">
                                            Visiter le site AHD-RDC
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td style="text-align: center; padding: 20px 30px; border-top: 1px solid #e9ecef;">
                            <p style="font-size: 12px; color: #999; margin: 0 0 4px 0;">
                                <strong>AHD-RDC</strong> — Action Humanitaire pour le Développement en RD Congo
                            </p>
                            <p style="font-size: 12px; color: #999; margin: 0 0 4px 0;">
                                Minova, Territoire de Kalehe, Sud-Kivu, RD Congo
                            </p>
                            <p style="font-size: 12px; color: #999; margin: 0 0 4px 0;">
                                contact@ahd-rdc.org | +243 994 656 250
                            </p>
                            <p style="font-size: 12px; color: #aaa; font-style: italic; margin: 8px 0 0 0;">
                                « Être ensemble pour le bien de la communauté »
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>