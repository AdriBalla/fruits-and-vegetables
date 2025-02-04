<?php

declare(strict_types=1);

namespace App\Tests\App\Request;

use App\DTO\Enum\WeightUnitType;
use App\Request\ProductListRequest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \App\Request\ProductListRequest
 */
class ProductListRequestTest extends KernelTestCase
{
    protected ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->validator = $this->getContainer()->get(ValidatorInterface::class);
    }

    /**
     * @return mixed[]
     */
    public static function dataProvider(): array
    {
        return [
            'should not fail' => [
                'unit' => WeightUnitType::Grams->value,
                'page' => 5,
                'size' => 10,
                'hasErrors' => false,
            ],
            'should not fail even if everything is null' => [
                'unit' => null,
                'page' => null,
                'size' => null,
                'hasErrors' => false,
            ],
            'should fail because unit is wrong' => [
                'unit' => 'test',
                'page' => 5,
                'size' => 10,
                'hasErrors' => true,
            ],
            'should fail because page is below 0' => [
                'unit' => WeightUnitType::Kilograms->value,
                'page' => -1,
                'size' => 10,
                'hasErrors' => true,
            ],
            'should fail because size is 0' => [
                'unit' => WeightUnitType::Grams->value,
                'page' => 12,
                'size' => 0,
                'hasErrors' => true,
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFruitDtoValidation(?string $unit, ?int $page, ?int $size, bool $hasErrors): void
    {
        $dto = new ProductListRequest($unit, $page, $size);

        $errors = $this->validator->validate($dto);

        if ($hasErrors) {
            $this->assertCount(1, $errors);
        }

        if (!$hasErrors) {
            $this->assertCount(0, $errors);
            $this->assertEquals($unit, $dto->unit);
            $this->assertEquals($page, $dto->page);
            $this->assertEquals($size, $dto->size);
        }
    }
}
