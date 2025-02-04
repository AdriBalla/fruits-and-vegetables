<?php

declare(strict_types=1);

namespace App\Mapper;

use App\DTO\Enum\WeightUnitType;

class QuantityMapper
{
    public function convert(float $quantity, WeightUnitType $sourceUnitType, WeightUnitType $targetUnitType): float
    {
        // Convert quantity to grams
        $quantityInGrams = $quantity * WeightUnitType::conversionRates()[$sourceUnitType->value];

        // Convert to target unit
        return round($quantityInGrams / WeightUnitType::conversionRates()[$targetUnitType->value], 2);
    }
}
