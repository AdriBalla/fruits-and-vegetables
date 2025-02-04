<?php

declare(strict_types=1);

namespace App\Tests\App\Repository;

use App\DTO\Enum\ProductType;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Repository\ProductRepository
 */
class ProductRepositoryTest extends KernelTestCase
{
    private ProductRepository $productRepository;

    protected EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema($this->entityManager->getMetadataFactory()->getAllMetadata());

        $this->productRepository = $this->getContainer()->get(ProductRepository::class);
    }

    public function testAdd(): void
    {
        $name = 'Apple';
        $quantity = 1000;
        $type = ProductType::Fruit->value;

        $product = new Product();
        $product->setName($name);
        $product->setType($type);
        $product->setQuantity($quantity);

        $this->productRepository->add($product, true);

        $savedProduct = $this->entityManager->find(Product::class, $product->getId());

        $this->assertNotNull($savedProduct);
        $this->assertSame($name, $savedProduct->getName());
        $this->assertSame($type, $savedProduct->getType());
        $this->assertSame($quantity, $savedProduct->getQuantity());
    }

    public function testRemove(): void
    {
        $name = 'To remove vegetable';
        $quantity = 1;
        $type = ProductType::Vegetable->value;

        $product = new Product();
        $product->setName($name);
        $product->setType($type);
        $product->setQuantity($quantity);

        $this->productRepository->add($product, true);
        $id = $product->getId();

        $this->entityManager->detach($product);
        $remove = $this->productRepository->remove(ProductType::Fruit, $id);
        $this->assertFalse($remove);

        $dbProduct = $this->entityManager->find(Product::class, $id);
        $this->assertNotNull($dbProduct);

        $this->entityManager->detach($dbProduct);
        $remove = $this->productRepository->remove(ProductType::Vegetable, $id);
        $this->assertTrue($remove);

        $dbProduct = $this->entityManager->find(Product::class, $id);
        $this->assertNull($dbProduct);
    }

    private function insertProduct(ProductType $productType, string $name, int $quantity): Product
    {
        $product = new Product();
        $product->setName($name);
        $product->setType($productType->value);
        $product->setQuantity($quantity);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    public function testList(): void
    {
        $product1 = $this->insertProduct(ProductType::Fruit, 'Apple', 3);
        $product2 = $this->insertProduct(ProductType::Fruit, 'Orange', 4);
        $product3 = $this->insertProduct(ProductType::Vegetable, 'Beans', 5);

        $products = $this->productRepository->list(ProductType::Fruit, 0, 1);

        $this->assertCount(1, $products);
        $this->assertSame($product1->getId(), $products[0]->getId());

        $products = $this->productRepository->list(ProductType::Fruit, 1, 1);

        $this->assertCount(1, $products);
        $this->assertSame($product2->getId(), $products[0]->getId());

        $products = $this->productRepository->list(ProductType::Fruit);

        $this->assertCount(2, $products);
        $this->assertSame($product1->getId(), $products[0]->getId());
        $this->assertSame($product2->getId(), $products[1]->getId());

        $products = $this->productRepository->list(ProductType::Vegetable);
        $this->assertSame($product3->getId(), $products[0]->getId());
    }

    public function testTruncate(): void
    {
        $this->insertProduct(ProductType::Fruit, 'Apple', 3);
        $this->insertProduct(ProductType::Fruit, 'Orange', 4);
        $this->insertProduct(ProductType::Vegetable, 'Beans', 5);

        $this->productRepository->truncate();

        $this->assertEmpty($this->productRepository->findAll());
    }
}
