<?php

namespace App\Tests\App\Controller;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;

/**
 * @covers \App\Controller\ListController
 */
class ListControllerTest extends ProductControllerTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testList(ProductType $productType, string $route): void
    {
        $product1 = $this->insertProduct($productType, 'test product 1', 3000);
        $product2 = $this->insertProduct($productType, 'test product 2', 4000);

        $this->client->request('GET', $route);

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(2, $responseData);

        $this->assertSame($product1->getId(), $responseData[0]['id']);
        $this->assertSame($product1->getName(), $responseData[0]['name']);
        $this->assertSame($product1->getQuantity(), $responseData[0]['quantity']);

        $this->assertSame($product2->getId(), $responseData[1]['id']);
        $this->assertSame($product2->getName(), $responseData[1]['name']);
        $this->assertSame($product2->getQuantity(), $responseData[1]['quantity']);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testListWithConversion(ProductType $productType, string $route): void
    {
        $product1 = $this->insertProduct($productType, 'test product 1', 3000);
        $product2 = $this->insertProduct($productType, 'test product 2', 4000);

        $this->client->request('GET', $route, ['unit' => WeightUnitType::Kilograms->value]);

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(2, $responseData);

        $this->assertSame($product1->getId(), $responseData[0]['id']);
        $this->assertSame($product1->getName(), $responseData[0]['name']);
        $this->assertEquals($product1->getQuantity() / 1000, $responseData[0]['quantity']);

        $this->assertSame($product2->getId(), $responseData[1]['id']);
        $this->assertSame($product2->getName(), $responseData[1]['name']);
        $this->assertEquals($product2->getQuantity() / 1000, $responseData[1]['quantity']);
    }

    /**
     * @dataProvider  dataProvider
     */
    public function testListWithPageAndSize(ProductType $productType, string $route): void
    {
        $this->insertProduct($productType, 'test product 1', 3000);
        $product2 = $this->insertProduct($productType, 'test product 2', 4000);

        $this->client->request('GET', $route, ['page' => 1, 'size' => 1]);

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(1, $responseData);

        $this->assertSame($product2->getId(), $responseData[0]['id']);
        $this->assertSame($product2->getName(), $responseData[0]['name']);
        $this->assertEquals($product2->getQuantity(), $responseData[0]['quantity']);
    }
}
