<?php

declare(strict_types=1);

namespace App\DTO\Enum;

enum WeightUnitType: string
{
    case Kilograms = 'kg';
    case Grams = 'g';

    /**
     * @return int[]
     */
    public static function conversionRates(): array
    {
        return [
            self::Kilograms->value => 1000,
            self::Grams->value => 1,
        ];
    }
}
