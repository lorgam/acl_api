<?php

namespace App\Controller\Api\V1;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
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
    private $catRepo;

    public function __construct(SerializerInterface $serializer, ProductRepository $repo, CategoryRepository $catRepo)
    {
        $this->serializer = $serializer;
        $this->repo = $repo;
        $this->catRepo = $catRepo;
    }

    /**
     * @Route("/featured", methods={"GET"}, name="featured")
     */
    public function featyredProducts(Request $request): JsonResponse
    {
        $products = $this->repo->getFeaturedProducts();
        return new JsonResponse($products);
    }

    /**
     * @Route("/", methods={"GET"}, name="get")
     */
    public function getProducts(): Response
    {
        return $this->productToJsonResponse($this->repo->findAll());
    }

    /**
     * @Route("/", methods={"POST"}, name="post")
     */
    public function postProduct(Request $request): JsonResponse
    {
        $product = $this->serializer->deserialize($request->getContent(), Product::class, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['category']]);

        $jsonObject = json_decode($request->getContent(), true);
        if (isset($jsonObject['category']) && isset($jsonObject['category']['id'])) {
            $category = $this->catRepo->find($jsonObject['category']['id']);
            // @TODO: Validate existence of category
            $product->setCategory($category);
        }

        $this->repo->add($product);
        return $this->productToJsonResponse($product);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="get_id")
     */
    public function getProduct(?Product $product): JsonResponse
    {
        return $this->productToJsonResponse($product);
    }

    /**
     * @Route("/{id}", methods={"PUT"}, name="put_id")
     */
    public function putProduct(Request $request, ?Product $product): JsonResponse
    {
        if ($product != null) {
            $this->serializer->deserialize($request->getContent(), Product::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $product, AbstractNormalizer::IGNORED_ATTRIBUTES => ['category']]);

            $jsonObject = json_decode($request->getContent(), true);
            if (isset($jsonObject['category'])) {
                if (isset($jsonObject['category']['id'])) {
                    $category = $this->catRepo->find($jsonObject['category']['id']);
                    // @TODO: Validate existence of category
                    $product->setCategory($category);
                } else {
                    $product->setCategory(null);
                }
            }

            $this->repo->add($product);
        }

        return $this->productToJsonResponse($product);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete_id")
     */
    public function deleteProduct(?Product $product): JsonResponse
    {
        $this->repo->remove($product);
        return new JsonResponse(['deleted' => true]);
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
