<?php

declare(strict_types=1);

namespace App\Tests\App\DTO;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;
use App\DTO\ProductDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ProductDtoTestCase extends KernelTestCase
{
    protected ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->validator = $this->getContainer()->get(ValidatorInterface::class);
    }

    abstract protected function getProductType(): ProductType;

    abstract protected function generateDto(string $name, int|float $quantity, WeightUnitType $unit, ?int $id): ProductDto;

    /**
     * @return mixed[]
     */
    public static function dataProvider(): array
    {
        return [
            'should not fail' => [
                'name' => 'Orange',
                'quantity' => 200,
                'unit' => WeightUnitType::Grams,
                'id' => 12,
                'hasErrors' => false,
            ],
            'should fails with one error because quantity is less than 0' => [
                'name' => 'Apple',
                'quantity' => -3,
                'unit' => WeightUnitType::Kilograms,
                'id' => 876,
                'hasErrors' => true,
            ],
            'should fails with one error because name is empty' => [
                'name' => '',
                'quantity' => 100,
                'unit' => WeightUnitType::Kilograms,
                'id' => null,
                'hasErrors' => true,
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFruitDtoValidation(string $name, int|float $quantity, WeightUnitType $unit, ?int $id, bool $hasErrors): void
    {
        $dto = $this->generateDto($name, $quantity, $unit, $id);

        $errors = $this->validator->validate($dto);

        if ($hasErrors) {
            $this->assertCount(1, $errors);
        }

        if (!$hasErrors) {
            $this->assertCount(0, $errors);
            $this->assertEquals($name, $dto->name);
            $this->assertEquals($quantity, $dto->quantity);
            $this->assertEquals($unit, $dto->unit);
            $this->assertEquals($this->getProductType(), $dto->type);
        }
    }
}
