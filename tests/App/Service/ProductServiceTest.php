<?php

declare(strict_types=1);

namespace App\Tests\App\Service;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;
use App\DTO\ProductDto;
use App\Entity\Product;
use App\Mapper\ProductMapper;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\ProductService
 */
class ProductServiceTest extends TestCase
{
    private ProductService $productService;

    private ProductRepository&MockObject $productRepository;

    private ProductMapper&MockObject $productMapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->productMapper = $this->createMock(ProductMapper::class);

        $this->productService = new ProductService($this->productRepository, $this->productMapper);
    }

    public function testList(): void
    {
        $type = ProductType::Fruit;
        $unit = WeightUnitType::Kilograms;
        $page = 12;
        $size = 4;

        $repositoryResult1 = $this->createMock(Product::class);
        $repositoryResult2 = $this->createMock(Product::class);

        $dtoResult1 = $this->createMock(ProductDto::class);
        $dtoResult2 = $this->createMock(ProductDto::class);

        $expectedRepositoryResult = [
            $repositoryResult1,
            $repositoryResult2,
        ];

        $expectedDtoResult = [
            $dtoResult1,
            $dtoResult2,
        ];

        $this->productMapper->expects(self::any())->method('mapToDto')->with($repositoryResult1, $unit)->willReturn($dtoResult1);
        $this->productMapper->expects(self::any())->method('mapToDto')->with($repositoryResult2, $unit)->willReturn($dtoResult2);

        $this->productRepository->expects(self::once())->method('list')->with($type, $page, $size)->willReturn($expectedRepositoryResult);

        $results = $this->productService->list($type, $unit, $page, $size);

        $this->assertEquals($expectedDtoResult, $results);
    }

    public function testAdd(): void
    {
        $dtoResult = new ProductDto('test', 10000, WeightUnitType::Kilograms, ProductType::Fruit);
        $repositoryResult = $this->createMock(Product::class);

        $this->productMapper->expects(self::any())->method('mapFromDto')->with($dtoResult)->willReturn($repositoryResult);
        $this->productMapper->expects(self::any())->method('mapToDto')->with($repositoryResult, $dtoResult->unit)->willReturn($dtoResult);

        $this->productRepository->expects(self::once())->method('add')->with($repositoryResult, true);

        $result = $this->productService->add($dtoResult);

        $this->assertEquals($dtoResult, $result);
    }

    public function testRemove(): void
    {
        $id = 678;
        $type = ProductType::Fruit;

        $this->productRepository->expects(self::once())->method('remove')->with($type, $id)->willReturn(true);

        $result = $this->productService->remove($type, $id);

        $this->assertTrue($result);
    }

    public function testFlush(): void
    {
        $this->productRepository->expects(self::once())->method('truncate');

        $this->productService->flush();
    }
}
