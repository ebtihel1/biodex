<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commandes</title>
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
    <h1>Commandes</h1>
    <div class="meta">
        {{ $orders->count() }} commande(s) &mdash; généré le {{ $generatedAt->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">ID</th>
                <th style="width: 14%;">Utilisateur</th>
                <th style="width: 16%;">Produit</th>
                <th style="width: 6%;">Qté</th>
                <th style="width: 9%;">Total</th>
                <th style="width: 10%;">Statut</th>
                <th style="width: 20%;">Adresse</th>
                <th style="width: 10%;">Paiement</th>
                <th style="width: 11%;">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ optional($order->user)->name ?? '—' }}</td>
                    <td>{{ optional($order->product)->name ?? '—' }}</td>
                    <td>{{ $order->quantity }}</td>
                    <td>{{ number_format((float) $order->total_amount, 2) }} DT</td>
                    <td>{{ is_object($order->status) ? $order->status->value : $order->status }}</td>
                    <td>{{ $order->shipping_address }}</td>
                    <td>{{ $order->payment_method }}</td>
                    <td>{{ optional($order->order_date)->format('d/m/Y') ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 12px;">Aucune commande.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Biodex &mdash; Back Office</div>
</body>
</html>