<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\FruitDto;
use App\DTO\VegetableDto;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class AddController extends AbstractController
{
    #[Route('/fruits', name: 'fruits.add', methods: 'POST')]
    public function addFruit(#[MapRequestPayload] FruitDto $product, ProductService $productService): JsonResponse
    {
        return $this->json(data: $productService->add($product), context: [AbstractNormalizer::GROUPS => ['read']]);
    }

    #[Route('/vegetables', name: 'vegetables.add', methods: 'POST')]
    public function addVegetable(#[MapRequestPayload] VegetableDto $product, ProductService $productService): JsonResponse
    {
        return $this->json(data: $productService->add($product), context: [AbstractNormalizer::GROUPS => ['read']]);
    }
}
