<?php

namespace App\Tests\App\Controller;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;
use App\Entity\Product;

/**
 * @covers \App\Controller\AddController
 */
class AddControllerTest extends ProductControllerTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testAdd(ProductType $productType, string $route): void
    {
        $params = [
            'name' => 'test',
            'quantity' => 36,
            'unit' => WeightUnitType::Kilograms->value,
        ];

        $this->client->request('POST', $route, $params);

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotNull($responseData['id']);
        $this->assertSame('test', $responseData['name']);
        $this->assertSame(WeightUnitType::Kilograms->value, $responseData['unit']);
        $this->assertEquals(36, $responseData['quantity']);

        $product = $this->entityManager->find(Product::class, $responseData['id']);
        $this->assertSame('test', $product->getName());
        $this->assertSame(36000, $product->getQuantity());
        $this->assertSame($productType->value, $product->getType());
    }
}
