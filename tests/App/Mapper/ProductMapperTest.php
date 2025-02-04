<?php

declare(strict_types=1);

namespace App\Tests\App\Mapper;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;
use App\DTO\ProductDto;
use App\Entity\Product;
use App\Mapper\ProductMapper;
use App\Mapper\QuantityMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Mapper\ProductMapper
 */
class ProductMapperTest extends TestCase
{
    private QuantityMapper&MockObject $quantityMapper;

    private ProductMapper $productMapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->quantityMapper = $this->createMock(QuantityMapper::class);

        $this->productMapper = new ProductMapper($this->quantityMapper);
    }

    public function testMapToDto(): void
    {
        $quantity = 1000;
        $name = 'Apple';
        $type = ProductType::Fruit->value;
        $id = 5678;

        $convertedQuantity = 1.0;
        $targetUnit = WeightUnitType::Grams;

        $product = $this->createMock(Product::class);

        $product->expects(self::once())->method('getQuantity')->willReturn($quantity);
        $product->expects(self::once())->method('getName')->willReturn($name);
        $product->expects(self::once())->method('getType')->willReturn($type);
        $product->expects(self::once())->method('getId')->willReturn($id);

        $this->quantityMapper->expects(self::once())->method('convert')->with($quantity, WeightUnitType::Grams, $targetUnit)->willReturn($convertedQuantity);

        $result = $this->productMapper->mapToDto($product, $targetUnit);

        $this->assertInstanceOf(ProductDto::class, $result);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($convertedQuantity, $result->quantity);
        $this->assertEquals($targetUnit, $result->unit);
        $this->assertEquals(ProductType::Fruit, $result->type);
    }

    public function testMapFromDto(): void
    {
        $quantity = 1000;
        $convertedQuantity = 1.0;
        $name = 'Apple';
        $unit = WeightUnitType::Kilograms;
        $type = ProductType::Fruit;
        $id = 56789;

        $productDto = new ProductDto($name, $quantity, $unit, $type, $id);

        $this->quantityMapper->expects(self::once())->method('convert')->with($quantity, $unit, WeightUnitType::Grams)->willReturn($convertedQuantity);

        $result = $this->productMapper->mapFromDto($productDto);

        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($name, $result->getName());
        $this->assertEquals($convertedQuantity, $result->getQuantity());
        $this->assertEquals($type->value, $result->getType());
    }
}
