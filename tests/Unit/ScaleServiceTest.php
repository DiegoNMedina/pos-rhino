<?php

namespace Tests\Unit;

use App\Services\Hardware\ScaleService;
use PHPUnit\Framework\TestCase;

class ScaleServiceTest extends TestCase
{
    public function test_parses_kg(): void
    {
        $service = new ScaleService;

        $this->assertSame(1.25, $service->parseWeightKg('1.250 kg'));
    }

    public function test_parses_g_as_kg(): void
    {
        $service = new ScaleService;

        $this->assertSame(0.25, $service->parseWeightKg('250 g'));
    }

    public function test_returns_null_when_no_number(): void
    {
        $service = new ScaleService;

        $this->assertNull($service->parseWeightKg('sin dato'));
    }
}
