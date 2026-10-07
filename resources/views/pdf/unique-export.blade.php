<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Export PDF' }}</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100% !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background-color: #f9f9f9;
        }

        .pdf-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            padding: 0;
        }

        .header {
            background-color: #1a54b8;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header img {
            width: 100px;
            margin-bottom: 10px;
        }

        .content {
            padding: 20px;
        }

        .badge {
            background-color: #d0ddf7;
            color: #1a54b8;
            border: 1px solid #1a54b8;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-block;
            margin: 10px 0;
        }

        .info-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .info-table th,
        .info-table td {
            padding: 10px;
            border-bottom: 1px solid #eaeaea;
            text-align: left;
        }

        .info-table th {
            background-color: #f1f1f1;
            color: #333;
            font-weight: bold;
        }

        .section-card {
            border: 1px solid #d0ddf7;
            border-left: 4px solid #1a54b8;
            border-radius: 4px;
            background-color: #f8faff;
            margin: 15px 0;
            padding: 15px;
        }

        .section-title {
            color: #1a54b8;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .detail-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .detail-list li {
            padding: 5px 0;
            border-bottom: 1px solid #eaeaea;
        }

        .detail-list li:last-child {
            border-bottom: none;
        }

        .detail-list strong {
            color: #1a54b8;
            min-width: 120px;
            display: inline-block;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777;
            background-color: #f5f5f5;
        }

        .footer a {
            color: #1a54b8;
            text-decoration: none;
        }

        .causes-consequences {
            margin-top: 20px;
        }

        .causes-consequences h4 {
            color: #1a54b8;
            margin-bottom: 10px;
        }

        .causes-consequences ul {
            list-style: none;
            padding: 0;
        }

        .causes-consequences li {
            padding: 5px 0;
            border-bottom: 1px solid #eaeaea;
        }

        .causes-consequences li:last-child {
            border-bottom: none;
        }

        @media print {
            body, html {
                background-color: white;
            }
            
            .pdf-container {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="pdf-container">
        <!-- Header -->
        <div class="header">
            <img src="https://churabat.ma/wp-content/uploads/2024/08/Couleur-4.svg" alt="Logo CHU">
            <h2>Centre Hospitalier Universitaire Ibn Sina</h2>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="badge">{{ $entityType ?? 'Détails' }}</div>

            <!-- Main Entity Section -->
            <div class="section-card">
                <div class="section-title">
                    <i class="bi bi-info-circle"></i> {{ $entityType ?? 'Détails' }}
                </div>
                <ul class="detail-list">
                    @if(isset($entity->nom))
                        <li><strong>Nom:</strong> {{ $entity->nom }}</li>
                    @endif
                    @if(isset($entity->description))
                        <li><strong>Description:</strong> {{ $entity->description }}</li>
                    @endif
                    @if(isset($entity->date_heure))
                        <li><strong>Date et heure:</strong> {{ \Carbon\Carbon::parse($entity->date_heure)->format('d/m/Y H:i') }}</li>
                    @endif
                    @if(isset($entity->lieu))
                        <li><strong>Lieu:</strong> {{ $entity->lieu }}</li>
                    @endif
                    @if(isset($entity->service) && $entity->service)
                        <li><strong>Service:</strong> {{ $entity->service->nom }}</li>
                    @endif
                    @if(isset($entity->entreprise) && $entity->entreprise)
                        <li><strong>Entreprise:</strong> {{ $entity->entreprise->abbreviation ?? $entity->entreprise->nom ?? $entity->entreprise->description ?? '—' }}</li>
                    @endif
                    @if(isset($entity->typologie) && $entity->typologie)
                        <li><strong>Typologie:</strong> {{ $entity->typologie->nom }}</li>
                    @endif
                    @if(isset($entity->gravite) && $entity->gravite)
                        <li><strong>Gravité:</strong> {{ $entity->gravite->nom }}</li>
                    @endif
                    @if(isset($entity->user) && $entity->user)
                        <li><strong>Utilisateur:</strong> {{ $entity->user->nom }} {{ $entity->user->prenom }}</li>
                    @endif
                    @if(isset($entity->referant) && $entity->referant)
                        <li><strong>Référant:</strong> {{ $entity->referant->nom }} {{ $entity->referant->prenom }}</li>
                    @endif
                    @if(isset($entity->coordinateur) && $entity->coordinateur)
                        <li><strong>Coordinateur:</strong> {{ $entity->coordinateur->nom }} {{ $entity->coordinateur->prenom }}</li>
                    @endif
                    @if(isset($entity->declarant_fonction))
                        <li><strong>Fonction du déclarant:</strong> {{ $entity->declarant_fonction }}</li>
                    @endif
                    @if(isset($entity->personne_concernee) && $entity->personne_concernee)
                        <li><strong>Personne concernée:</strong> {{ $entity->personne_concernee->nom }}</li>
                    @endif
                    @if(isset($entity->created_at))
                        <li><strong>Date de création:</strong> {{ \Carbon\Carbon::parse($entity->created_at)->format('d/m/Y H:i') }}</li>
                    @endif
                </ul>
            </div>

            <!-- Causes Section -->
            @if(isset($causes) && count($causes) > 0)
            <div class="causes-consequences">
                <h4 style="color: #1a54b8;">Causes</h4>
                <ul class="detail-list">
                    @foreach($causes as $cause)
                        <li>{{ $cause->nom }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Consequences Section -->
            @if(isset($consequences) && count($consequences) > 0)
            <div class="causes-consequences">
                <h4 style="color: #1a54b8;">Conséquences</h4>
                <ul class="detail-list">
                    @foreach($consequences as $consequence)
                        <li>{{ $consequence->nom }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <p style="margin-top: 20px; padding: 15px; background-color: #f8faff; border-radius: 4px; border-left: 4px solid #1a54b8;">
                <strong>Document généré le {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} CHU Ibn Sina - Rabat. Tous droits réservés.</p>
            <p><a href="https://www.chu-rabat.ma">www.chu-rabat.ma</a></p>
        </div>
    </div>
</body>
</html> 