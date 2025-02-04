<?php

declare(strict_types=1);

namespace App\Tests\App\DTO\Enum;

use App\DTO\Enum\WeightUnitType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\DTO\Enum\WeightUnitType
 */
class WeightUnitTypeTest extends TestCase
{
    public function testConversionRates(): void
    {
        $expectedConversionRates = [
            'kg' => 1000,
            'g' => 1,
        ];

        $this->assertEquals($expectedConversionRates, WeightUnitType::conversionRates());
    }
}
