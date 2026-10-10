<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservations</title>
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
    <h1>Réservations</h1>
    <div class="meta">
        {{ $reservations->count() }} réservation(s) &mdash; généré le {{ $generatedAt->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 22%;">Utilisateur</th>
                <th style="width: 26%;">Produit</th>
                <th style="width: 8%;">Qté</th>
                <th style="width: 15%;">Statut</th>
                <th style="width: 24%;">Réservé jusqu'au</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->id }}</td>
                    <td>{{ optional($reservation->user)->name ?? 'Unknown' }}</td>
                    <td>{{ \App\Http\Controllers\ReservationController::getProductName($reservation->product_id) }}</td>
                    <td>{{ $reservation->quantity }}</td>
                    <td>{{ is_object($reservation->status) ? $reservation->status->value : $reservation->status }}</td>
                    <td>{{ optional($reservation->reserved_until)->format('d/m/Y H:i') ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 12px;">Aucune réservation.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Biodex &mdash; Back Office</div>
</body>
</html>