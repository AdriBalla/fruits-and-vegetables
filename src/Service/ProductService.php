<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;
use App\DTO\ProductDto;
use App\Mapper\ProductMapper;
use App\Repository\ProductRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ProductMapper $mapper,
        private ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @return ProductDto[]
     */
    public function list(ProductType $type, ?WeightUnitType $unit, ?int $page = null, ?int $size = null): array
    {
        $products = [];
        foreach ($this->productRepository->list($type, $page, $size) as $product) {
            $products[] = $this->mapper->mapToDto($product, $unit);
        }

        return $products;
    }

    public function add(ProductDto $productDto): ProductDto
    {
        $product = $this->mapper->mapFromDto($productDto);

        $this->productRepository->add($product, true);

        $this->logger->info('Product added', (array) $product);

        return $this->mapper->mapToDto($product, $productDto->unit);
    }

    public function remove(ProductType $type, int $id): bool
    {
        $this->logger->info('Product removed', ['type' => $type->value, 'id' => $id]);

        return $this->productRepository->remove($type, $id);
    }

    public function flush(): void
    {
        $this->productRepository->truncate();
    }
}
