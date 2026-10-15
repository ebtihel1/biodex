<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Campagnes</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 10px; color: #1b3a36; }
        h1 { font-size: 18px; color: #137d52; margin: 0 0 4px 0; }
        .meta { font-size: 9px; color: #68817c; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background-color: #137d52; color: #ffffff; text-align: left;
            padding: 6px 5px; font-size: 9px; border: 1px solid #0f6242;
        }
        tbody td { padding: 5px; border: 1px solid #dfe9e5; vertical-align: top; }
        tbody tr:nth-child(even) { background-color: #f2faf6; }
        .footer { margin-top: 12px; font-size: 8px; color: #68817c; text-align: right; }
    </style>
</head>
<body>
    <h1>Campagnes</h1>
    <div class="meta">
        {{ $campaigns->count() }} campagne(s) &mdash; généré le {{ $generatedAt->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">ID</th>
                <th style="width: 15%;">Titre</th>
                <th style="width: 10%;">Statut</th>
                <th style="width: 8%;">Ville</th>
                <th style="width: 10%;">Région</th>
                <th style="width: 9%;">Début</th>
                <th style="width: 9%;">Fin</th>
                <th style="width: 9%;">Limite inscription</th>
                <th style="width: 6%;">Participants</th>
                <th style="width: 10%;">Créée par</th>
                <th style="width: 10%;">Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($campaigns as $campaign)
                <tr>
                    <td>{{ $campaign->id }}</td>
                    <td>{{ $campaign->title }}</td>
                    <td>{{ ucfirst($campaign->status) }}</td>
                    <td>{{ $campaign->city ?? '—' }}</td>
                    <td>{{ $campaign->region ?? '—' }}</td>
                    <td>{{ $campaign->start_date?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $campaign->end_date?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $campaign->deadline_registration?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $campaign->participants_count ?? 0 }}</td>
                    <td>{{ optional($campaign->user)->name ?? '—' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($campaign->description, 60) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 12px;">Aucune campagne.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Biodex &mdash; Back Office</div>
</body>
</html>
