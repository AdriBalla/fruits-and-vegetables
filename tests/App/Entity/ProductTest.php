<?php

declare(strict_types=1);

namespace App\Tests\App\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Entity\Product
 */
class ProductTest extends TestCase
{
    public function testNameAccessors(): void
    {
        $product = new Product();
        $product->setName('Apple');
        $this->assertSame('Apple', $product->getName());
    }

    public function testTypeAccessors(): void
    {
        $product = new Product();
        $product->setType('Random');
        $this->assertSame('Random', $product->getType());
    }

    public function testQuantityAccessors(): void
    {
        $product = new Product();
        $product->setQuantity(5);
        $this->assertSame(5, $product->getQuantity());
    }

    public function testIdIsNullOnInit(): void
    {
        $product = new Product();
        $this->assertNull($product->getId());
    }

    public function testIdAccessors(): void
    {
        $product = new Product();
        $product->setId(10);

        $this->assertSame(10, $product->getId());

        $product->setId(null);
        $this->assertNull($product->getId());
    }
}
