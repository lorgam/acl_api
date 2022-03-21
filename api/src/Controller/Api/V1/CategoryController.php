<?php

namespace App\Controller\Api\V1;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

// @TODO: Validate Json Structure, Headers...

/**
 * @Route("/category", name="api_category_")
 */
class CategoryController extends AbstractController
{

    private $serializer;
    private $repo;

    public function __construct(SerializerInterface $serializer, CategoryRepository $repo)
    {
        $this->serializer = $serializer;
        $this->repo = $repo;
    }

    /**
     * @Route("/", methods={"GET"}, name="get")
     */
    public function getCategories(): JsonResponse
    {
        return $this->categoryToJsonResponse($this->repo->findAll());
    }

    /**
     * @Route("/", methods={"POST"}, name="post")
     */
    public function postCategoy(Request $request): JsonResponse
    {
        $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json');
        $this->repo->add($category);
        return $this->categoryToJsonResponse($category);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="get_id")
     */
    public function getCategory(?Category $category): JsonResponse
    {
        return $this->categoryToJsonResponse($category);
    }

    /**
     * @Route("/{id}", methods={"PUT"}, name="put_id")
     */
    public function putCategory(Request $request, ?Category $category): JsonResponse
    {
        if ($category != null) {
            $this->serializer->deserialize($request->getContent(), Category::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $category]);
            $this->repo->add($category);
        }

        return $this->categoryToJsonResponse($category);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete_id")
     */
    public function deleteCategory(?Category $category): JsonResponse
    {
        $this->repo->remove($category);
        return new JsonResponse(['deleted' => true]);
    }

    private function categoryToJsonResponse(null|array|Category $data, array $groups = ['rest']): JsonResponse
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
