<?php

declare(strict_types=1);

namespace App\Tests\App\DTO;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;
use App\DTO\FruitDto;
use App\DTO\ProductDto;

/**
 * @covers \App\DTO\FruitDto
 */
class FruitDtoTest extends ProductDtoTestCase
{
    protected function getProductType(): ProductType
    {
        return ProductType::Fruit;
    }

    protected function generateDto(string $name, float|int $quantity, WeightUnitType $unit, ?int $id): ProductDto
    {
        return new FruitDto($name, $quantity, $unit, $id);
    }
}
