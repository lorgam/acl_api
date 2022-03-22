<?php

namespace App\Controller\Api\V1;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Controller\AbstractApiJsonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/product", name="api_product_")
 */
class ProductController extends AbstractApiJsonController
{
    private CategoryRepository $catRepo;

    public function __construct(SerializerInterface $serializer, ProductRepository $repo, ValidatorInterface $validator, CategoryRepository $catRepo)
    {
        parent::__construct($serializer, $repo, $validator);
        $this->catRepo = $catRepo;
    }

    /**
     * @Route("/featured", methods={"GET"}, name="featured")
     */
    public function featuredProducts(Request $request, array $currencies): Response
    {
        $products = null;
        $currency = $request->query->get('currency', null);
        if ($currency == null) {
            return $this->arrayToJsonResponse($this->repo->getFeaturedProducts());
        } elseif (in_array($currency, $currencies)) {
            return $this->arrayToJsonResponse($this->repo->getFeaturedProductsCurrencyConverted($currency));
        }
        return $this->badRequestResponse($e->getMessage());
    }

    /**
     * @Route("/", methods={"GET"}, name="get")
     */
    public function getProducts(): Response
    {
        return $this->entityToJsonResponse($this->repo->findAll());
    }

    /**
     * @Route("/", methods={"POST"}, name="post")
     */
    public function postProduct(Request $request): Response
    {
        try {
            $product = $this->serializer->deserialize($request->getContent(), Product::class, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['category']]);
        } catch (\UnexpectedValueException $e) {
            return $this->badRequestResponse($e->getMessage());
        }
        //validate the product
        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            return $this->badRequestResponse((string) $errors);
        }
        // Manage the category for this product
        $jsonObject = json_decode($request->getContent(), true);
        if (isset($jsonObject['category']) && isset($jsonObject['category']['id'])) {
            $category = $this->catRepo->find($jsonObject['category']['id']);
            if ($category == null) {
                return $this->badRequestResponse('Category not found');
            }
            $product->setCategory($category);
        }

        $this->repo->add($product);
        return $this->entityToJsonResponse($product);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="get_id")
     */
    public function getProduct(?Product $product): Response
    {
        return $this->entityToJsonResponse($product);
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
                return $this->badRequestResponse($e->getMessage());
            }
            //validate the product
            $errors = $this->validator->validate($product);
            if (count($errors) > 0) {
                return $this->badRequestResponse((string) $errors);
            }

            // Manage the category for this product
            $jsonObject = json_decode($request->getContent(), true);
            if (isset($jsonObject['category'])) {
                if (isset($jsonObject['category']['id'])) {
                    $category = $this->catRepo->find($jsonObject['category']['id']);
                    if ($category == null) {
                        return $this->badRequestResponse('Category not found');
                    }
                    $product->setCategory($category);
                } else {
                    $product->setCategory(null);
                }
            }
            $this->repo->add($product);
        }

        return $this->entityToJsonResponse($product);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete_id")
     */
    public function deleteProduct(?Product $product): Response
    {
        if ($product == null) {
            return $this->notFoundResponse();
        }

        $this->repo->remove($product);
        return $this->deletedResponse(true);
    }
}
