<?php

declare(strict_types=1);

namespace App\Tests\App\Mapper;

use App\DTO\Enum\WeightUnitType;
use App\Mapper\QuantityMapper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapper\QuantityMapper
 */
class QuantityMapperTest extends TestCase
{
    private QuantityMapper $quantityMapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->quantityMapper = new QuantityMapper();
    }

    /**
     * @return mixed[]
     */
    public static function dataProvider(): array
    {
        return [
            'convert g to kg' => [
                'sourceUnit' => WeightUnitType::Grams,
                'targetUnit' => WeightUnitType::Kilograms,
                'quantity' => 1000,
                'expectedQuantity' => 1,
            ],
            'convert g to g' => [
                'sourceUnit' => WeightUnitType::Grams,
                'targetUnit' => WeightUnitType::Grams,
                'quantity' => 45778,
                'expectedQuantity' => 45778,
            ],
            'convert kg to g' => [
                'sourceUnit' => WeightUnitType::Kilograms,
                'targetUnit' => WeightUnitType::Grams,
                'quantity' => 87.650,
                'expectedQuantity' => 87650,
            ],
            'convert kg to kg' => [
                'sourceUnit' => WeightUnitType::Kilograms,
                'targetUnit' => WeightUnitType::Kilograms,
                'quantity' => 786,
                'expectedQuantity' => 786,
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testConvert(WeightUnitType $sourceUnit, WeightUnitType $targetUnit, int|float $quantity, int|float $expectedQuantity): void
    {
        $result = $this->quantityMapper->convert($quantity, $sourceUnit, $targetUnit);
        $this->assertEquals($expectedQuantity, $result);
    }
}
