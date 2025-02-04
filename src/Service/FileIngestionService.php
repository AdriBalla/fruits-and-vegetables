<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ProductDto;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class FileIngestionService
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ProductService $productService,
        private readonly EntityManagerInterface $entityManager,
        private ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function ingestFile(string $path, bool $shouldTruncate = false, string $format = 'json'): void
    {
        $this->logger->info(sprintf('Ingesting the file %s', $path));

        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('The file %s does not exist', $path));
        }

        $content = file_get_contents($path, true);

        if (false === $content) {
            $this->logger->critical(sprintf('The file %s is not readable', $path));
            throw new \InvalidArgumentException(sprintf('The file %s could not be read', $path));
        }

        $this->entityManager->beginTransaction();

        try {
            if ($shouldTruncate) {
                $this->logger->info('Truncating before ingestion operation');
                $this->productService->flush();
            }

            $this->ingestJson($content, $format);
        } catch (NotEncodableValueException) {
            $this->entityManager->rollback();
            $this->logger->critical(sprintf('The file %s is not encodable, transaction aborted', $path));
            throw new \InvalidArgumentException(sprintf('The json file %s is malformed', $path));
        }

        $this->entityManager->commit();

        $this->logger->info('Ingestion finished successfully');
    }

    /**
     * @throws NotEncodableValueException
     */
    private function ingestJson(string $json, string $format): void
    {
        /** @var ProductDto[] $products */
        $products = $this->serializer->deserialize(
            data: $json,
            type: ProductDto::class.'[]',
            format: $format,
            context: [AbstractNormalizer::GROUPS => ['write']]);

        foreach ($products as $product) {
            $product = $this->productService->add($product);
            $this->logger->info(sprintf('Product %s of type %s with id %s ingested', $product->name, $product->type->value, $product->id));
        }
    }
}
