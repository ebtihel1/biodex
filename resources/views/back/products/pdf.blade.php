<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produits recyclés</title>
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
        .st-yes { color: #137d52; font-weight: bold; }
        .st-no  { color: #b02a37; font-weight: bold; }
        .footer { margin-top: 12px; font-size: 8px; color: #68817c; text-align: right; }
    </style>
</head>
<body>
    <h1>Produits recyclés</h1>
    <div class="meta">
        {{ $products->count() }} produit(s) &mdash; généré le {{ $generatedAt->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">ID</th>
                <th style="width: 18%;">Nom</th>
                <th style="width: 20%;">Description</th>
                <th style="width: 8%;">Prix (DT)</th>
                <th style="width: 6%;">Stock</th>
                <th style="width: 12%;">Catégorie</th>
                <th style="width: 14%;">Processus</th>
                <th style="width: 8%;">Disponible</th>
                <th style="width: 10%;">Créé le</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($product->description, 60) }}</td>
                    <td>{{ number_format((float) $product->price, 2) }}</td>
                    <td>{{ $product->stock_quantity }}</td>
                    <td>{{ optional($product->category)->name ?? '—' }}</td>
                    <td>{{ optional($product->recyclingProcess)->method ?? '—' }}</td>
                    <td class="{{ $product->is_available ? 'st-yes' : 'st-no' }}">
                        {{ $product->is_available ? 'Oui' : 'Non' }}
                    </td>
                    <td>{{ optional($product->created_at)->format('d/m/Y') ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 12px;">
                        Aucun produit.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Biodex &mdash; Back Office</div>
</body>
</html>