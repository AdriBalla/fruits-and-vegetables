<?php

namespace App\Mapper;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;
use App\DTO\ProductDto;
use App\Entity\Product;

class ProductMapper
{
    public function __construct(
        protected readonly QuantityMapper $quantityMapper,
    ) {
    }

    public function mapToDto(Product $product, ?WeightUnitType $unit = null): ProductDto
    {
        $quantity = $product->getQuantity();

        if (null !== $unit) {
            $quantity = $this->quantityMapper->convert($quantity, WeightUnitType::Grams, $unit);
        }

        return new ProductDto(
            $product->getName(),
            $quantity,
            $unit ?? WeightUnitType::Grams,
            ProductType::from($product->getType()),
            $product->getId(),
        );
    }

    public function mapFromDto(ProductDto $productDto): Product
    {
        $product = new Product();
        $product->setId($productDto->id);
        $product->setName($productDto->name);
        $product->setQuantity((int) $this->quantityMapper->convert($productDto->quantity, $productDto->unit, WeightUnitType::Grams));
        $product->setType($productDto->type->value);

        return $product;
    }
}
