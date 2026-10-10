<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dons</title>
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
    <h1>Dons</h1>
    <div class="meta">
        {{ $donations->count() }} don(s) &mdash; généré le {{ $generatedAt->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">ID</th>
                <th style="width: 14%;">Utilisateur</th>
                <th style="width: 14%;">Catégorie</th>
                <th style="width: 16%;">Article</th>
                <th style="width: 8%;">État</th>
                <th style="width: 10%;">Statut</th>
                <th style="width: 20%;">Description</th>
                <th style="width: 14%;">Créé le</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($donations as $donation)
                <tr>
                    <td>{{ $donation->id }}</td>
                    <td>{{ optional($donation->user)->name ?? 'Guest' }}</td>
                    <td>{{ optional(optional($donation->waste)->category)->name ?? '—' }}</td>
                    <td>{{ $donation->item_name }}</td>
                    <td>{{ ucfirst($donation->condition) }}</td>
                    <td>{{ is_object($donation->status) ? $donation->status->value : $donation->status }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($donation->description, 80) }}</td>
                    <td>{{ optional($donation->created_at)->format('d/m/Y') ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 12px;">Aucun don.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Biodex &mdash; Back Office</div>
</body>
</html>