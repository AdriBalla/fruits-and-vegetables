<?php

namespace App\Tests\App\Controller;

use App\DTO\Enum\ProductType;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\RemoveController
 */
class RemoveControllerTest extends ProductControllerTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testRemove(ProductType $productType, string $route): void
    {
        $product = $this->insertProduct($productType, 'product to delete', 1000);
        $id = $product->getId();

        $dbProduct = $this->entityManager->find(Product::class, $id);
        $this->assertNotNull($dbProduct);

        $this->client->request('DELETE', $route.'/'.$id);

        $this->entityManager->detach($dbProduct);

        $this->assertResponseIsSuccessful();

        $dbProduct = $this->entityManager->find(Product::class, $product->getId());
        $this->assertNull($dbProduct);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRemoveNoProduct(ProductType $productType, string $route): void
    {
        $id = 546789;
        $this->client->request('DELETE', $route.'/'.$id);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
