<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Points de collecte</title>
    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
        }
        body {
            font-size: 10px;
            color: #1b3a36;
        }
        h1 {
            font-size: 18px;
            color: #137d52;
            margin: 0 0 4px 0;
        }
        .meta {
            font-size: 9px;
            color: #68817c;
            margin-bottom: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead th {
            background-color: #137d52;
            color: #ffffff;
            text-align: left;
            padding: 6px 5px;
            font-size: 9px;
            border: 1px solid #0f6242;
        }
        tbody td {
            padding: 5px;
            border: 1px solid #dfe9e5;
            vertical-align: top;
        }
        tbody tr:nth-child(even) {
            background-color: #f2faf6;
        }
        .status-active {
            color: #137d52;
            font-weight: bold;
        }
        .status-inactive {
            color: #9a6b1a;
            font-weight: bold;
        }
        .footer {
            margin-top: 12px;
            font-size: 8px;
            color: #68817c;
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Points de collecte</h1>
    <div class="meta">
        {{ $points->count() }} point(s) de collecte &mdash; généré le {{ $generatedAt->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">ID</th>
                <th style="width: 16%;">Nom</th>
                <th style="width: 20%;">Adresse</th>
                <th style="width: 10%;">Ville</th>
                <th style="width: 8%;">Code postal</th>
                <th style="width: 12%;">Téléphone</th>
                <th style="width: 8%;">Statut</th>
                <th style="width: 22%;">Catégories acceptées</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($points as $point)
                <tr>
                    <td>{{ $point->id }}</td>
                    <td>{{ $point->name }}</td>
                    <td>{{ $point->address }}</td>
                    <td>{{ $point->city }}</td>
                    <td>{{ $point->postal_code }}</td>
                    <td>{{ $point->contact_phone }}</td>
                    <td class="{{ $point->status === 'active' ? 'status-active' : 'status-inactive' }}">
                        {{ $point->status === 'active' ? 'Actif' : 'Inactif' }}
                    </td>
                    <td>
                        @php
                            $categories = $point->accepted_categories;
                            if (is_string($categories)) {
                                $categories = json_decode($categories, true);
                            }
                        @endphp
                        {{ is_array($categories) ? implode(', ', $categories) : '' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 12px;">
                        Aucun point de collecte.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Biodex &mdash; Back Office</div>
</body>
</html>
