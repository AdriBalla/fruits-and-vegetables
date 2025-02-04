<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Enum\ProductType;
use App\DTO\Enum\WeightUnitType;
use App\Request\ProductListRequest;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ListController extends AbstractController
{
    #[Route('/fruits', name: 'fruits.list', defaults: ['productType' => ProductType::Fruit->value], methods: 'GET')]
    #[Route('/vegetables', name: 'vegetables.list', defaults: ['productType' => ProductType::Vegetable->value], methods: 'GET')]
    public function list(ProductType $productType, Request $request, ValidatorInterface $validator, ProductService $productService): JsonResponse
    {
        $listRequest = new ProductListRequest(
            $request->query->has('unit') ? $request->query->getString('unit') : null,
            $request->query->has('page') ? $request->query->getInt('page') : null,
            $request->query->has('size') ? $request->query->getInt('size') : null,
        );

        $errors = $validator->validate($listRequest);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $unit = $listRequest->unit ? WeightUnitType::from($listRequest->unit) : null;

        $products = $productService->list($productType, $unit, $listRequest->page, $listRequest->size);

        return $this->json(data: $products, context: [AbstractNormalizer::GROUPS => ['read']]);
    }
}
