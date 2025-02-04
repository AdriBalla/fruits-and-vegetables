<?php

declare(strict_types=1);

namespace App\Request;

use App\DTO\Enum\WeightUnitType;
use Symfony\Component\Validator\Constraints as Assert;

class ProductListRequest
{
    public function __construct(
        #[Assert\Choice(choices: [WeightUnitType::Grams->value, WeightUnitType::Kilograms->value])]
        public readonly ?string $unit,

        #[Assert\PositiveOrZero]
        public readonly ?int $page,

        #[Assert\GreaterThan(0)]
        public readonly ?int $size,
    ) {
    }
}
