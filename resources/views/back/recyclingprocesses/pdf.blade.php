<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Processus de recyclage</title>
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
        .st-pending     { color: #9a6b1a; font-weight: bold; }
        .st-in_progress { color: #0d6efd; font-weight: bold; }
        .st-completed   { color: #137d52; font-weight: bold; }
        .st-failed      { color: #b02a37; font-weight: bold; }
        .footer { margin-top: 12px; font-size: 8px; color: #68817c; text-align: right; }
    </style>
</head>
<body>
    <h1>Processus de recyclage</h1>
    <div class="meta">
        {{ $processes->count() }} processus &mdash; généré le {{ $generatedAt->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">ID</th>
                <th style="width: 14%;">Déchet</th>
                <th style="width: 12%;">Catégorie</th>
                <th style="width: 12%;">Méthode</th>
                <th style="width: 9%;">Statut</th>
                <th style="width: 9%;">Début</th>
                <th style="width: 9%;">Fin</th>
                <th style="width: 8%;">Qté (kg)</th>
                <th style="width: 12%;">Responsable</th>
                <th style="width: 11%;">Qualité</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($processes as $process)
                <tr>
                    <td>{{ $process->id }}</td>
                    <td>{{ optional($process->waste)->type ?? '—' }}</td>
                    <td>{{ optional(optional($process->waste)->category)->name ?? '—' }}</td>
                    <td>{{ $process->method }}</td>
                    <td class="st-{{ $process->status }}">
                        {{ ucfirst(str_replace('_', ' ', $process->status)) }}
                    </td>
                    <td>{{ optional($process->start_date)->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ optional($process->end_date)->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $process->output_quantity ?? '—' }}</td>
                    <td>{{ optional($process->responsibleUser)->name ?? '—' }}</td>
                    <td>{{ $process->output_quality ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 12px;">
                        Aucun processus de recyclage.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Biodex &mdash; Back Office</div>
</body>
</html>