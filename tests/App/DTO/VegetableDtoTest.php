<?php

declare(strict_types=1);

namespace App\Tests\App\DTO;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;
use App\DTO\ProductDto;
use App\DTO\VegetableDto;

/**
 * @covers \App\DTO\VegetableDto
 */
class VegetableDtoTest extends ProductDtoTestCase
{
    protected function getProductType(): ProductType
    {
        return ProductType::Vegetable;
    }

    protected function generateDto(string $name, float|int $quantity, WeightUnitType $unit, ?int $id): ProductDto
    {
        return new VegetableDto($name, $quantity, $unit, $id);
    }
}
