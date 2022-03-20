<?php

namespace App\Controller\Api\V1;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/product", name="api_product_")
 */
class ProductController extends AbstractController
{
    private $serializer;
    private $repo;

    public function __construct(SerializerInterface $serializer, ProductRepository $repo)
    {
        $this->serializer = $serializer;
        $this->repo = $repo;
    }

    /**
     * @Route("/", methods={"GET"}, name="get")
     */
    public function getProducts(): Response
    {
        return $this->productToJsonResponse($this->repo->findAll());
    }

    private function productToJsonResponse(null|array|Product $data, array $groups = ['rest']): JsonResponse
    {
        if ($data == null) return new JsonResponse([], Response::HTTP_NO_CONTENT);

        return JsonResponse::fromJsonString(
            $this->serializer->serialize(
                $data,
                'json',
                ['groups' => $groups]
            )
        );
    }
}
