<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WasteImageClassifier
{
    /**
     * Correspondance code catégorie (API) -> id table waste_categories.
     */
    public const CATEGORY_BY_CODE = [
        'plastic' => 1,
        'glass' => 2,
        'paper' => 3,
        'metal' => 4,
        'organic' => 5,
        'electronic' => 6,
        'textile' => 7,
        'wood' => 8,
    ];

    public function classify(UploadedFile $file): array
    {
        try {
            $response = Http::timeout(30)
                ->attach('image', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post(config('services.classify_service.url', 'http://127.0.0.1:5004').'/classify');

            if ($response->successful()) {
                $data = $response->json();
                if (($data['success'] ?? false) && ! empty($data['category'])) {
                    return [
                        'category_code' => $data['category'],
                        'category_label' => $data['category_label'] ?? $data['category'],
                        'confidence' => (float) ($data['confidence'] ?? 0),
                        'model' => $data['model'] ?? 'unknown',
                        'source' => 'python_cnn',
                        'top_categories' => $data['top_categories'] ?? [],
                    ];
                }
            }
        } catch (ConnectionException $e) {
            Log::warning('Service de classification d\'images injoignable : '.$e->getMessage());
        } catch (\Throwable $e) {
            Log::warning('Erreur de classification d\'images : '.$e->getMessage());
        }

        return [
            'category_code' => 'unknown',
            'category_label' => 'Inconnu',
            'confidence' => 0.0,
            'model' => 'service-unavailable',
            'source' => 'php_fallback',
            'top_categories' => [],
        ];
    }

    public function categoryIdForCode(?string $code): ?int
    {
        if (! $code) {
            return null;
        }

        return self::CATEGORY_BY_CODE[$code] ?? null;
    }
}
