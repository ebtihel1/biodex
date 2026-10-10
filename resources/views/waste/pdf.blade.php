<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déchets</title>
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
        .status-recyclable { color: #137d52; font-weight: bold; }
        .status-reusable   { color: #0d6efd; font-weight: bold; }
        .footer { margin-top: 12px; font-size: 8px; color: #68817c; text-align: right; }
    </style>
</head>
<body>
    <h1>Déchets</h1>
    <div class="meta">
        {{ $wastes->count() }} déchet(s) &mdash; généré le {{ $generatedAt->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">ID</th>
                <th style="width: 14%;">Type</th>
                <th style="width: 8%;">Poids (kg)</th>
                <th style="width: 10%;">Statut</th>
                <th style="width: 16%;">Description</th>
                <th style="width: 14%;">Catégorie</th>
                <th style="width: 14%;">Utilisateur</th>
                <th style="width: 20%;">Point de collecte</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($wastes as $waste)
                <tr>
                    <td>{{ $waste->id }}</td>
                    <td>{{ $waste->type }}</td>
                    <td>{{ number_format((float) $waste->weight, 2) }}</td>
                    <td class="{{ $waste->status === 'recyclable' ? 'status-recyclable' : 'status-reusable' }}">
                        {{ $waste->status === 'recyclable' ? 'Recyclable' : 'Réutilisable' }}
                    </td>
                    <td>{{ $waste->description }}</td>
                    <td>{{ optional($waste->category)->name ?? '—' }}</td>
                    <td>{{ optional($waste->user)->name ?? '—' }}</td>
                    <td>{{ optional($waste->collectionPoint)->name ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 12px;">
                        Aucun déchet.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Biodex &mdash; Back Office</div>
</body>
</html>