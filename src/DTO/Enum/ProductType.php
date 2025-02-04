<?php

declare(strict_types=1);

namespace App\DTO\Enum;

enum ProductType: string
{
    case Fruit = 'fruit';
    case Vegetable = 'vegetable';
}
