<?php

declare(strict_types=1);

namespace App\DTO;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ProductDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Groups(['read', 'write'])]
        public readonly string $name,

        #[Assert\NotBlank]
        #[Assert\GreaterThanOrEqual(0)]
        #[Groups(['read', 'write'])]
        public readonly float|int $quantity,

        #[Assert\NotBlank]
        #[Groups(['read', 'write'])]
        public readonly WeightUnitType $unit,

        #[Assert\NotBlank]
        #[Groups(['write'])]
        public readonly ProductType $type,

        #[Groups(['read', 'write'])]
        public ?int $id = null,
    ) {
    }
}
