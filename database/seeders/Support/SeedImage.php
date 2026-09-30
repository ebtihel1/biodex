<?php

namespace Database\Seeders\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Genere des images de substitution (SVG) dans storage/app/public
 * afin que les vues n'affichent pas d'images cassees apres le seeding.
 */
class SeedImage
{
    /**
     * Cree l'image si elle n'existe pas et retourne le chemin relatif
     * a stocker en base (ex: wastes/bouteille-plastique.svg).
     */
    public static function make(string $folder, string $name, string $label, array $palette = ['#15803d', '#0f766e']): string
    {
        [$from, $to] = $palette;

        $path = trim($folder, '/').'/'.$name.'.svg';
        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            $disk->put($path, self::svg($label, $from, $to));
        }

        return $path;
    }

    /**
     * Idem que make() mais retourne l'URL publique relative au site
     * (utile pour les colonnes consommees telles quelles par le front,
     * comme campaigns.image).
     */
    public static function url(string $folder, string $name, string $label, array $palette = ['#15803d', '#0f766e']): string
    {
        return '/storage/'.self::make($folder, $name, $label, $palette);
    }

    private static function svg(string $label, string $from, string $to): string
    {
        $label = htmlspecialchars($label, ENT_QUOTES | ENT_XML1, 'UTF-8');

        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">
            <defs>
                <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="{$from}"/>
                    <stop offset="100%" stop-color="{$to}"/>
                </linearGradient>
            </defs>
            <rect width="800" height="600" fill="url(#bg)"/>
            <circle cx="400" cy="262" r="132" fill="#ffffff" fill-opacity="0.14"/>
            <text x="400" y="300" font-size="118" text-anchor="middle" fill="#ffffff" fill-opacity="0.9"
                font-family="Segoe UI Symbol, DejaVu Sans, Noto Sans Symbols2, sans-serif">&#9851;</text>
            <text x="400" y="470" font-size="40" font-weight="700" text-anchor="middle" fill="#ffffff"
                font-family="Segoe UI, Arial, sans-serif">{$label}</text>
            <text x="400" y="516" font-size="22" text-anchor="middle" fill="#ffffff" fill-opacity="0.75"
                font-family="Segoe UI, Arial, sans-serif">Biodex - Economie circulaire</text>
        </svg>
        SVG;
    }
}
