<?php

declare(strict_types=1);

namespace App\DTO;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;

class FruitDto extends ProductDto
{
    public function __construct(
        string $name,
        float|int $quantity,
        WeightUnitType $unit,
        ?int $id = null,
    ) {
        parent::__construct($name, $quantity, $unit, ProductType::Fruit, $id);
    }
}
