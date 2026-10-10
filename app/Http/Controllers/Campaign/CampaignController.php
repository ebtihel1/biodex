<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    // ============================================================
    // API (JSON) — utilisé par DataTables dans le back-office
    // ============================================================
    public function index()
    {
        $campaigns = Campaign::with('user')->get();
        return response()->json($campaigns);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'in:draft,active,closed',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $campaign = Campaign::create($validated);
        return response()->json($campaign, 201);
    }

    public function show($id)
    {
        $campaign = Campaign::with('user')->findOrFail($id);
        return response()->json($campaign);
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'status' => 'in:draft,active,closed',
        ]);

        $campaign->update($validated);
        return response()->json($campaign);
    }

    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();

        return response()->json(['message' => 'Campaign deleted successfully']);
    }

    public function frontIndex()
    {
        $campaigns = Campaign::where('status', 'active')->get();
        return view('front.campaign.campaigns', compact('campaigns'));
    }

    // ============================================================
    // EXPORT CSV — export complet de toutes les campagnes
    // ============================================================
    public function exportCsv()
    {
        $campaigns = Campaign::with('user')->orderBy('id')->get();
        $filename = 'campagnes-' . date('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($campaigns) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID', 'Titre', 'Description', 'Image',
                'Date début', 'Date fin', 'Statut',
                'Utilisateur', 'Créé le',
            ], ';');

            foreach ($campaigns as $campaign) {
                fputcsv($handle, [
                    $campaign->id,
                    $campaign->title,
                    $campaign->description,
                    $campaign->image,
                    optional($campaign->start_date)->format('Y-m-d'),
                    optional($campaign->end_date)->format('Y-m-d'),
                    $campaign->status,
                    optional($campaign->user)->name,
                    optional($campaign->created_at)->format('Y-m-d H:i:s'),
                ], ';');
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ============================================================
    // EXPORT PDF
    // ============================================================
    public function exportPdf()
    {
        $campaigns = Campaign::with('user')->orderBy('id')->get();

        $pdf = Pdf::loadView('back.campaign.pdf', [
            'campaigns' => $campaigns,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('campagnes-' . date('Y-m-d-His') . '.pdf');
    }

    // ============================================================
    // IMPORT CSV
    // ============================================================
    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|max:4096']);

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
                'title', 'description', 'image',
                'start_date', 'end_date', 'status', 'user_id',
            ]);
        }

        $imported = 0;
        $errors = [];

        foreach ($dataRows as $line => $row) {
            $attributes = $this->buildAttributes($row, $columns);
            $lineNumber = $line + ($hasHeader ? 2 : 1);

            if ($attributes['title'] === '') {
                $errors[] = "Ligne {$lineNumber} : titre manquant.";
                continue;
            }
            if (empty($attributes['start_date']) || empty($attributes['end_date'])) {
                $errors[] = "Ligne {$lineNumber} : dates manquantes.";
                continue;
            }
            if ($attributes['user_id'] !== null
                && ! User::whereKey($attributes['user_id'])->exists()) {
                $errors[] = "Ligne {$lineNumber} : user_id {$attributes['user_id']} inexistant.";
                continue;
            }

            try {
                Campaign::create($attributes);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Ligne {$lineNumber} : " . $e->getMessage();
            }
        }

        $message = $imported . ' campagne(s) importée(s).';
        if (! empty($errors)) {
            $message .= ' ' . count($errors) . ' ligne(s) ignorée(s).';
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', array_slice($errors, 0, 20));
    }

    // ============================================================
    // HELPERS
    // ============================================================
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

    protected function mapColumns(array $header): array
    {
        $aliases = [
            'title' => ['title', 'titre', 'nom'],
            'description' => ['description', 'desc'],
            'image' => ['image', 'photo'],
            'start_date' => ['start date', 'start_date', 'date debut', 'date début'],
            'end_date' => ['end date', 'end_date', 'date fin'],
            'status' => ['status', 'statut'],
            'user_id' => ['user_id', 'user', 'utilisateur'],
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

    protected function buildAttributes(array $row, array $columns): array
    {
        $get = function (string $attribute) use ($row, $columns) {
            if (! array_key_exists($attribute, $columns)) return null;
            return $row[$columns[$attribute]] ?? null;
        };

        $status = strtolower(trim((string) ($get('status') ?? 'draft')));
        if (! in_array($status, ['draft', 'active', 'closed'], true)) {
            $status = 'draft';
        }

        return [
            'title' => trim((string) ($get('title') ?? '')),
            'description' => $this->nullIfEmpty($get('description')),
            'image' => $this->nullIfEmpty($get('image')),
            'start_date' => $this->parseDate($get('start_date')),
            'end_date' => $this->parseDate($get('end_date')),
            'status' => $status,
            'user_id' => $this->parseId($get('user_id')),
        ];
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

    protected function parseId($value): ?int
    {
        $value = trim((string) $value);
        if ($value === '' || ! is_numeric($value)) return null;
        return (int) $value;
    }

    protected function nullIfEmpty($value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }
}