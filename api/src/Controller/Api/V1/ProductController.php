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
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/product", name="api_product_")
 */
class ProductController extends AbstractController
{
    private $serializer;
    private $repo;
    private $catRepo;
    private $validator;

    public function __construct(SerializerInterface $serializer, ProductRepository $repo, CategoryRepository $catRepo, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->repo = $repo;
        $this->catRepo = $catRepo;
        $this->validator = $validator;
    }

    /**
     * @Route("/featured", methods={"GET"}, name="featured")
     */
    public function featuredProducts(Request $request, array $currencies): Response
    {
        $products = null;
        $currency = $request->query->get('currency', null);
        if ($currency == null) {
            return new JsonResponse($this->repo->getFeaturedProducts());
        } elseif (in_array($currency, $currencies)) {
            return new JsonResponse($this->repo->getFeaturedProductsCurrencyConverted($currency));
        }
        return new JsonResponse(['message' => 'Currency not valid'], Response::HTTP_BAD_REQUEST);
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
    public function postProduct(Request $request): Response
    {
        try {
            $product = $this->serializer->deserialize($request->getContent(), Product::class, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['category']]);
        } catch (\UnexpectedValueException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        //validate the product
        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            return new Response((string) $errors, Response::HTTP_BAD_REQUEST);
        }

        $jsonObject = json_decode($request->getContent(), true);
        //Check category
        if (isset($jsonObject['category']) && isset($jsonObject['category']['id'])) {
            $category = $this->catRepo->find($jsonObject['category']['id']);
            if ($category == null) return new JsonResponse(['error' => 'Category not found'], Response::HTTP_BAD_REQUEST);
            $product->setCategory($category);
        }

        $this->repo->add($product);
        return $this->productToJsonResponse($product);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="get_id")
     */
    public function getProduct(?Product $product): Response
    {
        return $this->productToJsonResponse($product);
    }

    /**
     * @Route("/{id}", methods={"PUT"}, name="put_id")
     */
    public function putProduct(Request $request, ?Product $product): Response
    {
        if ($product != null) {
            try {
                $this->serializer->deserialize($request->getContent(), Product::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $product, AbstractNormalizer::IGNORED_ATTRIBUTES => ['category']]);
            } catch (\UnexpectedValueException $e) {
                return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
            //validate the product
            $errors = $this->validator->validate($product);
            if (count($errors) > 0) {
                return new Response((string) $errors, Response::HTTP_BAD_REQUEST);
            }


            $jsonObject = json_decode($request->getContent(), true);
            if (isset($jsonObject['category'])) {
                if (isset($jsonObject['category']['id'])) {
                    $category = $this->catRepo->find($jsonObject['category']['id']);
                    if ($category == null) return new Response('Category not found', Response::HTTP_BAD_REQUEST);
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
    public function deleteProduct(?Product $product): Response
    {
        if ($product == null) {
            return $this->productToJsonResponse(null); // Return standard not found response
        }

        $this->repo->remove($product);
        return new JsonResponse(['deleted' => true]);
    }

    private function productToJsonResponse(null|array|Product $data, array $groups = ['rest']): Response
    {
        if ($data == null) {
            return new Response('Not found', Response::HTTP_NO_CONTENT);
        }

        return JsonResponse::fromJsonString(
            $this->serializer->serialize(
                $data,
                'json',
                ['groups' => $groups]
            )
        );
    }
}
