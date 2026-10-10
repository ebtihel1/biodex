<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\RecyclingProcess;
use App\Models\Waste;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecyclingProcessController extends Controller
{
    /**
     * Colonnes autorisées pour le tri serveur.
     */
    protected array $sortable = [
        'id', 'waste_id', 'method', 'status',
        'start_date', 'end_date', 'output_quantity',
        'output_quality', 'responsible_user_id', 'created_at',
    ];

    /**
     * Display a listing of the recycling processes.
     */
    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $direction = $request->query('direction') === 'desc' ? 'desc' : 'asc';
        $sort = in_array($request->query('sort'), $this->sortable, true)
            ? $request->query('sort')
            : 'id';

        $query->orderBy($sort, $direction);

        // Summary (basé sur la requête filtrée)
        $summary = (clone $query)
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count")
            ->selectRaw("SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_count")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count")
            ->selectRaw("SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count")
            ->first();

        $wastes = Waste::query()->orderBy('type')->get(['id', 'type']);
        $users = User::query()->orderBy('name')->get(['id', 'name']);

        $perPage = (int) $request->query('per_page', 10);
        if (! in_array($perPage, [5, 10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $recyclingProcesses = $query->paginate($perPage)->withQueryString();

        return view('back.recyclingprocesses.index', compact(
            'recyclingProcesses',
            'summary',
            'wastes',
            'users'
        ));
    }

    public function create()
    {
        $wastes = Waste::with('category')->orderBy('created_at', 'desc')->get();
        $users = User::all();

        return view('back.recyclingprocesses.create', compact('wastes', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'waste_id' => 'required|exists:wastes,id|unique:recycling_processes,waste_id',
            'method' => 'required|string|max:255',
            'status' => 'required|in:pending,in_progress,completed,failed',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'output_quantity' => 'nullable|numeric|min:0',
            'output_quality' => 'nullable|string|max:255',
            'responsible_user_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        if (! isset($validated['responsible_user_id'])) {
            $validated['responsible_user_id'] = Auth::id();
        }

        RecyclingProcess::create($validated);

        return redirect()->route('recyclingprocesses.index')
            ->with('success', 'Recycling process created successfully.');
    }

    public function show($id)
    {
        $recyclingProcess = RecyclingProcess::with(['waste', 'waste.category', 'responsibleUser', 'products'])
            ->findOrFail($id);

        return view('back.recyclingprocesses.show', compact('recyclingProcess'));
    }

    public function edit($id)
    {
        $recyclingProcess = RecyclingProcess::findOrFail($id);

        $wastes = Waste::whereDoesntHave('recyclingProcess')
            ->orWhere('id', $recyclingProcess->waste_id)
            ->with('category')
            ->get();

        $users = User::all();

        return view('back.recyclingprocesses.edit', compact('recyclingProcess', 'wastes', 'users'));
    }

    public function update(Request $request, $id)
    {
        $recyclingProcess = RecyclingProcess::findOrFail($id);

        $validated = $request->validate([
            'waste_id' => 'required|exists:wastes,id|unique:recycling_processes,waste_id,' . $id,
            'method' => 'required|string|max:255',
            'status' => 'required|in:pending,in_progress,completed,failed',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'output_quantity' => 'nullable|numeric|min:0',
            'output_quality' => 'nullable|string|max:255',
            'responsible_user_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $recyclingProcess->update($validated);

        return redirect()->route('recyclingprocesses.index')
            ->with('success', 'Recycling process updated successfully.');
    }

    public function destroy($id)
    {
        $recyclingProcess = RecyclingProcess::findOrFail($id);

        if ($recyclingProcess->products()->count() > 0) {
            return redirect()->route('recyclingprocesses.index')
                ->with('error', 'Cannot delete this process because products are associated with it.');
        }

        $recyclingProcess->delete();

        return redirect()->route('recyclingprocesses.index')
            ->with('success', 'Recycling process deleted successfully.');
    }

    // =========================================================================
    // EXPORT CSV
    // =========================================================================
    public function exportCsv(Request $request)
    {
        $query = $this->filteredQuery($request)->orderBy('id');
        $filename = 'processus-recyclage-' . date('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID', 'Déchet', 'Catégorie', 'Méthode', 'Statut',
                'Date début', 'Date fin', 'Quantité sortie (kg)',
                'Qualité sortie', 'Responsable', 'Notes', 'Créé le',
            ], ';');

            $query->chunk(200, function ($processes) use ($handle) {
                foreach ($processes as $process) {
                    fputcsv($handle, [
                        $process->id,
                        optional($process->waste)->type,
                        optional(optional($process->waste)->category)->name,
                        $process->method,
                        $process->status,
                        optional($process->start_date)->format('Y-m-d'),
                        optional($process->end_date)->format('Y-m-d'),
                        $process->output_quantity,
                        $process->output_quality,
                        optional($process->responsibleUser)->name,
                        $process->notes,
                        optional($process->created_at)->format('Y-m-d H:i:s'),
                    ], ';');
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // =========================================================================
    // EXPORT PDF
    // =========================================================================
    public function exportPdf(Request $request)
    {
        $processes = $this->filteredQuery($request)->orderBy('id')->get();

        $pdf = Pdf::loadView('back.recyclingprocesses.pdf', [
            'processes' => $processes,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('processus-recyclage-' . date('Y-m-d-His') . '.pdf');
    }

    // =========================================================================
    // IMPORT CSV
    // =========================================================================
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:4096',
        ]);

        $file = $request->file('csv_file');

        if (strtolower($file->getClientOriginalExtension()) !== 'csv') {
            return back()->with('error', 'Le fichier doit être au format CSV.');
        }

        $rows = $this->readCsv($file->getRealPath());

        if (empty($rows)) {
            return back()->with('error', 'Le fichier CSV est vide ou illisible.');
        }

        $columns = $this->mapColumns($rows[0]);
        $hasHeader = count($columns) >= 2;
        $dataRows = $hasHeader ? array_slice($rows, 1) : $rows;

        if (! $hasHeader) {
            $columns = array_flip([
                'waste_id', 'method', 'status', 'start_date', 'end_date',
                'output_quantity', 'output_quality',
                'responsible_user_id', 'notes',
            ]);
        }

        $imported = 0;
        $errors = [];

        foreach ($dataRows as $line => $row) {
            $attributes = $this->buildAttributes($row, $columns);
            $lineNumber = $line + ($hasHeader ? 2 : 1);

            // Validation ligne
            if (empty($attributes['waste_id'])) {
                $errors[] = "Ligne {$lineNumber} : waste_id manquant.";
                continue;
            }

            if (empty($attributes['method'])) {
                $errors[] = "Ligne {$lineNumber} : méthode manquante.";
                continue;
            }

            if (empty($attributes['start_date'])) {
                $errors[] = "Ligne {$lineNumber} : date de début manquante.";
                continue;
            }

            // Vérifier que waste_id existe et n'est pas déjà pris
            if (! Waste::whereKey($attributes['waste_id'])->exists()) {
                $errors[] = "Ligne {$lineNumber} : waste_id {$attributes['waste_id']} inexistant.";
                continue;
            }

            if (RecyclingProcess::where('waste_id', $attributes['waste_id'])->exists()) {
                $errors[] = "Ligne {$lineNumber} : ce déchet a déjà un processus de recyclage.";
                continue;
            }

            try {
                RecyclingProcess::create($attributes);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Ligne {$lineNumber} : " . $e->getMessage();
            }
        }

        $message = $imported . ' processus importé(s).';
        if (! empty($errors)) {
            $message .= ' ' . count($errors) . ' ligne(s) ignorée(s).';
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', array_slice($errors, 0, 20));
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Construit la requête filtrée (recherche + statut + méthode + responsable).
     */
    protected function filteredQuery(Request $request): Builder
    {
        $query = RecyclingProcess::query()
            ->with(['waste', 'waste.category', 'responsibleUser']);

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('method', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('output_quality', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('waste', fn ($w) => $w->where('type', 'like', "%{$search}%"))
                    ->orWhereHas('waste.category', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('responsibleUser', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if (in_array($request->query('status'), ['pending', 'in_progress', 'completed', 'failed'], true)) {
            $query->where('status', $request->query('status'));
        }

        if (is_numeric($request->query('method')) && $request->query('method') !== '') {
            // Si vous avez une table methods, sinon ignorer
        }

        if (is_numeric($request->query('responsible'))) {
            $query->where('responsible_user_id', (int) $request->query('responsible'));
        }

        if (is_numeric($request->query('waste'))) {
            $query->where('waste_id', (int) $request->query('waste'));
        }

        return $query;
    }

    /**
     * Lit un fichier CSV en détectant le séparateur.
     */
    protected function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if ($handle === false) return [];

        $firstLine = fgets($handle);
        if ($firstLine === false) { fclose($handle); return []; }

        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
        rewind($handle);

        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) === 1 && trim((string) $row[0]) === '') continue;
            $rows[] = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row);
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Associe les en-têtes CSV aux attributs du modèle.
     */
    protected function mapColumns(array $header): array
    {
        $aliases = [
            'waste_id' => ['waste_id', 'waste', 'dechet id', 'déchet id', 'dechet', 'déchet'],
            'method' => ['method', 'methode', 'méthode', 'process'],
            'status' => ['status', 'statut', 'etat', 'état'],
            'start_date' => ['start date', 'start_date', 'date debut', 'date début', 'debut', 'début'],
            'end_date' => ['end date', 'end_date', 'date fin', 'fin'],
            'output_quantity' => ['output quantity', 'output_quantity', 'quantite sortie', 'quantité sortie', 'quantite', 'quantité'],
            'output_quality' => ['output quality', 'output_quality', 'qualite sortie', 'qualité sortie', 'qualite', 'qualité'],
            'responsible_user_id' => ['responsible_user_id', 'responsible', 'responsable', 'user'],
            'notes' => ['notes', 'note', 'remarques'],
        ];

        $columns = [];
        foreach ($header as $index => $label) {
            $normalized = $this->normalizeHeader($label);
            foreach ($aliases as $attribute => $list) {
                if (in_array($normalized, $list, true)) {
                    $columns[$attribute] = $index;
                    break;
                }
            }
        }

        return $columns;
    }

    protected function normalizeHeader($value): string
    {
        $value = strtolower(trim((string) $value));
        $value = str_replace(['-', '_'], ' ', $value);
        return preg_replace('/\s+/', ' ', $value);
    }

    /**
     * Construit les attributs d'une ligne CSV.
     */
    protected function buildAttributes(array $row, array $columns): array
    {
        $get = function (string $attribute) use ($row, $columns) {
            if (! array_key_exists($attribute, $columns)) return null;
            return $row[$columns[$attribute]] ?? null;
        };

        $status = strtolower(trim((string) ($get('status') ?? '')));
        $statusMap = [
            'en attente' => 'pending',
            'en cours' => 'in_progress',
            'terminé' => 'completed',
            'termine' => 'completed',
            'échoué' => 'failed',
            'echoue' => 'failed',
        ];
        $status = $statusMap[$status] ?? $status;
        if (! in_array($status, ['pending', 'in_progress', 'completed', 'failed'], true)) {
            $status = 'pending';
        }

        return [
            'waste_id' => $this->parseId($get('waste_id')),
            'method' => trim((string) ($get('method') ?? '')),
            'status' => $status,
            'start_date' => $this->parseDate($get('start_date')),
            'end_date' => $this->parseDate($get('end_date')),
            'output_quantity' => $this->parseDecimal($get('output_quantity')),
            'output_quality' => $this->nullIfEmpty($get('output_quality')),
            'responsible_user_id' => $this->parseId($get('responsible_user_id')) ?? Auth::id(),
            'notes' => $this->nullIfEmpty($get('notes')),
        ];
    }

    protected function parseId($value): ?int
    {
        $value = trim((string) $value);
        if ($value === '' || ! is_numeric($value)) return null;
        return (int) $value;
    }

    protected function parseDecimal($value): ?float
    {
        $value = trim((string) $value);
        if ($value === '' || ! is_numeric($value)) return null;
        $n = (float) $value;
        return $n < 0 ? null : $n;
    }

    protected function parseDate($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') return null;
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function nullIfEmpty($value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}