<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attribution - Hôpital Ibn Sina CHU Rabat</title>
    <!--[if mso]>
    <style type="text/css">
        table {border-collapse:collapse;border-spacing:0;margin:0;}
        div, td {padding:0;}
        div {margin:0 !important;}
    </style>
    <noscript>
    <xml>
        <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Styles de base pour tous les clients */
        body,
        html {
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        body {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
        }

        table {
            border-spacing: 0;
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        table td {
            border-collapse: collapse;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            display: block;
        }

        /* Conteneur principal */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
        }

        /* En-tête */
        .header {
            background: #1a54b8;
            padding: 20px;
            color: #ffffff;
            text-align: center;
        }

        /* Logo */
        .logo {
            width: 120px;
            height: auto;
            margin: 0 auto 10px;
        }

        /* Corps du message */
        .content {
            padding: 20px;
            background-color: #ffffff;
        }

        /* Alerte */
        .alert-box {
            background-color: #d0ddf7;
            border-left: 5px solid #1a54b8;
            padding: 15px;
            margin: 15px 0;
        }

        /* Tableau d'informations */
        .info-table {
            width: 100%;
            margin: 20px 0;
            border: 1px solid #e0e0e0;
        }

        .info-table th {
            background-color: #1a54b8;
            color: #ffffff;
            text-align: left;
            padding: 10px;
            font-weight: normal;
        }

        .info-table td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 5px 12px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .badge-primary {
            background-color: #d0ddf7;
            color: #1a54b8;
            border: 1px solid #1a54b8;
        }

        /* Pied de page */
        .footer {
            padding: 20px;
            background-color: #f5f5f5;
            color: #666666;
            text-align: center;
            font-size: 12px;
        }

        .footer a {
            color: #1a54b8;
            text-decoration: none;
        }

        /* Bouton */
        .btn {
            display: inline-block;
            background-color: #1a54b8;
            color: #ffffff !important;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
        }

        /* Texte important */
        .important {
            color: #1a54b8;
            font-weight: bold;
        }

        /* Compte à rebours */
        .countdown {
            font-size: 24px;
            font-weight: bold;
            color: #1a54b8;
            text-align: center;
            margin: 15px 0;
        }

        /* Alignement sur mobile */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }

            .content,
            .header,
            .footer {
                padding: 15px !important;
            }

            .info-table th,
            .info-table td {
                padding: 8px !important;
            }

            .btn {
                display: block !important;
                width: 100% !important;
            }
        }
    </style>
</head>

<body>
    <center>
        <div class="email-container">
            <!-- En-tête -->
            <table cellpadding="0" cellspacing="0" width="100%" role="presentation">
                <tr>
                    <td class="header">
                        <img src="https://churabat.ma/wp-content/uploads/2024/08/Couleur-4.svg" width="120"
                            height="auto" alt="Logo Hôpital Ibn Sina CHU Rabat" class="logo">
                        <h2 style="margin: 5px 0; font-size: 20px;">Centre Hospitalier Universitaire Ibn Sina</h2>
                    </td>
                </tr>
            </table>

            <!-- Contenu -->
            <table cellpadding="0" cellspacing="0" width="100%" role="presentation">
                <tr>
                    <td class="content">
                        <p>Bonjour M. Belahcen Ilyasse,</p>

                        <div class="badge badge-primary">Notification : Attribution médicale à effectuer</div>

                        <div class="alert-box">
                            @if($patient)
                            <p>Nous vous informons qu'une attribution médicale a été effectuée pour le
                                patient <span class="important">{{ $patient->nom }}
                                    {{ $patient->prenom }}</span>.
                            </p>
                            @else
                            <p>Nous vous informons qu'une attribution médicale a été enregistrée.</p>
                            @endif
                        </div>

                        <h3 style="color: #1a54b8; margin-top: 20px;">Détails de l'attribution :</h3>
                        <table cellpadding="0" cellspacing="0" class="info-table" width="100%">
                            <tr>
                                <th width="40%">ID</th>
                                <td width="60%">{{ $attribution->id }}</td>
                            </tr>
                            @if($patient)
                            <tr>
                                <th>Patient</th>
                                <td>{{ $patient->nom }} {{ $patient->prenom }}</td>
                            </tr>
                            <tr>
                                <th>CIN</th>
                                <td>{{ $patient->cin }}</td>
                            </tr>
                            <tr>
                                <th>Groupe sanguin</th>
                                <td>{{ $patient->groupesang }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Utilisateur</th>
                                <td>{{ $user->nom }} {{ $user->prenom }}</td>
                            </tr>
                        </table>

                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation">
                            <tr>
                                <td align="center">
                                    <a href="#" class="btn">Accéder au système</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <!-- Pied de page -->
            <table cellpadding="0" cellspacing="0" width="100%" role="presentation">
                <tr>
                    <td class="footer">
                        <p>Ceci est un message automatique. Merci de ne pas y répondre.</p>
                        <p>&copy; {{ date('Y') }} Centre Hospitalier Universitaire Ibn Sina - Rabat. Tous droits
                            réservés.</p>
                        <p><a href="https://www.chu-rabat.ma">Visitez notre site web</a></p>
                    </td>
                </tr>
            </table>
        </div>
    </center>
</body>

</html>
