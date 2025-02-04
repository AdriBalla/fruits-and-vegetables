<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Enum\ProductType;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RemoveController extends AbstractController
{
    #[Route('/fruits/{id}', name: 'fruits.remove', defaults: ['productType' => ProductType::Fruit->value], methods: 'DELETE')]
    #[Route('/vegetables/{id}', name: 'vegetables.remove', defaults: ['productType' => ProductType::Vegetable->value], methods: 'DELETE')]
    public function remove(ProductType $productType, int $id, ProductService $productService): JsonResponse
    {
        $remove = $productService->remove($productType, $id);

        return new JsonResponse(null, $remove ? Response::HTTP_NO_CONTENT : Response::HTTP_NOT_FOUND);
    }
}
