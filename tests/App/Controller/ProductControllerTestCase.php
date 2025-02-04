<?php

namespace App\Tests\App\Controller;

use App\DTO\Enum\ProductType;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Controller\ProductController
 */
class ProductControllerTestCase extends WebTestCase
{
    private const FRUITS_ROUTE = '/fruits';

    private const VEGETABLES_ROUTE = '/vegetables';

    protected EntityManagerInterface $entityManager;

    protected KernelBrowser $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createClient();

        $this->entityManager = $this->getContainer()->get('doctrine')->getManager();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
    }

    protected function insertProduct(ProductType $productType, string $name, int $quantity): Product
    {
        $product = new Product();
        $product->setName($name);
        $product->setType($productType->value);
        $product->setQuantity($quantity);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    /**
     * @return mixed[]
     */
    public static function dataProvider(): array
    {
        return [
            'test with fruits' => [
                'productType' => ProductType::Fruit,
                'route' => self::FRUITS_ROUTE,
            ],
            'test with vegetables' => [
                'productType' => ProductType::Vegetable,
                'route' => self::VEGETABLES_ROUTE,
            ],
        ];
    }
}
