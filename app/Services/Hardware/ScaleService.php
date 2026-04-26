<?php

namespace App\Services\Hardware;

use Illuminate\Support\Str;

class ScaleService
{
    public function parseWeightKg(string $raw): ?float
    {
        $normalized = Str::lower(trim($raw));
        $normalized = str_replace(',', '.', $normalized);

        if (preg_match('/(-?\d+(?:\.\d+)?)/', $normalized, $matches) !== 1) {
            return null;
        }

        $value = (float) $matches[1];

        if (Str::contains($normalized, 'g') && ! Str::contains($normalized, 'kg')) {
            return $value / 1000;
        }

        return $value;
    }

    public function mockWeightKg(): float
    {
        return round(mt_rand(0, 5000) / 1000, 3);
    }
}
