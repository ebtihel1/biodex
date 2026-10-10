<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catégories de déchets</title>
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
    <h1>Catégories de déchets</h1>
    <div class="meta">
        {{ $categories->count() }} catégorie(s) &mdash; généré le {{ $generatedAt->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 20%;">Nom</th>
                <th style="width: 35%;">Description</th>
                <th style="width: 40%;">Instructions de recyclage</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->description }}</td>
                    <td>{{ $category->recycling_instructions }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 12px;">
                        Aucune catégorie.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Biodex &mdash; Back Office</div>
</body>
</html>