<?php

declare(strict_types=1);

namespace App\Tests\App\Service;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;
use App\DTO\ProductDto;
use App\Service\FileIngestionService;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @covers \App\Service\FileIngestionService
 */
class FileIngestionServiceTest extends TestCase
{
    private FileIngestionService $fileIngestionService;

    private SerializerInterface&MockObject $serializer;

    private ProductService&MockObject $productService;

    private EntityManagerInterface&MockObject $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->productService = $this->createMock(ProductService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->fileIngestionService = new FileIngestionService($this->serializer, $this->productService, $this->entityManager);
    }

    /**
     * @return mixed[]
     */
    public static function truncateDataProvider(): array
    {
        return [
            'should truncate' => [
                'shouldTruncate' => true,
            ],
            'should not truncate' => [
                'shouldTruncate' => false,
            ],
        ];
    }

    /**
     * @dataProvider truncateDataProvider
     */
    public function testIngestFile(bool $shouldTruncate): void
    {
        $data = [
            ['name' => 'Apple', 'type' => ProductType::Fruit->value, 'quantity' => 10, 'id' => 1],
            ['name' => 'Beans', 'type' => ProductType::Vegetable->value, 'quantity' => 5, 'id' => 2],
        ];

        $filePath = sys_get_temp_dir().'/request.json';
        $fileContent = json_encode($data);

        file_put_contents($filePath, $fileContent);

        $expectedDtos = [
            new ProductDto('Apple', 10, WeightUnitType::Grams, ProductType::Fruit),
            new ProductDto('Beans', 5, WeightUnitType::Grams, ProductType::Vegetable),
        ];

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($fileContent, ProductDto::class.'[]', 'json', [AbstractNormalizer::GROUPS => ['write']])
            ->willReturn($expectedDtos);

        $this->productService
            ->expects(self::exactly(2))
            ->method('add')
            ->willReturnOnConsecutiveCalls($expectedDtos[0], $expectedDtos[1]);

        if ($shouldTruncate) {
            $this->productService
                ->expects(self::once())
                ->method('flush');
        }

        $this->entityManager->expects(self::once())->method('beginTransaction');
        $this->entityManager->expects(self::once())->method('commit');

        $this->fileIngestionService->ingestFile($filePath, $shouldTruncate);

        unlink($filePath);
    }

    public function testIngestFileFileDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The file /invalid/path.json does not exist');

        $this->fileIngestionService->ingestFile('/invalid/path.json');
    }

    public function testIngestFileMalformedJson(): void
    {
        $filePath = sys_get_temp_dir().'/request.json';
        file_put_contents($filePath, '{this json is malformed');

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->willThrowException(new NotEncodableValueException());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('The json file %s is malformed', $filePath));

        $this->entityManager->expects(self::once())->method('beginTransaction');
        $this->entityManager->expects(self::once())->method('rollback');

        $this->fileIngestionService->ingestFile($filePath);

        unlink($filePath);
    }
}
