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
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/category", name="api_category_")
 */
class CategoryController extends AbstractController
{

    private $serializer;
    private $repo;
    private $validator;

    public function __construct(SerializerInterface $serializer, CategoryRepository $repo, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->repo = $repo;
        $this->validator = $validator;
    }

    /**
     * @Route("/", methods={"GET"}, name="get")
     */
    public function getCategories(): Response
    {
        return $this->categoryToJsonResponse($this->repo->findAll());
    }

    /**
     * @Route("/", methods={"POST"}, name="post")
     */
    public function postCategoy(Request $request): Response
    {
        try {
            $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json');
        } catch (\UnexpectedValueException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        //validate the category
        $errors = $this->validator->validate($category);
        if (count($errors) > 0) {
            return new Response((string) $errors, Response::HTTP_BAD_REQUEST);
        }

        $this->repo->add($category);
        return $this->categoryToJsonResponse($category);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="get_id")
     */
    public function getCategory(?Category $category): Response
    {
        return $this->categoryToJsonResponse($category);
    }

    /**
     * @Route("/{id}", methods={"PUT"}, name="put_id")
     */
    public function putCategory(Request $request, ?Category $category): Response
    {
        if ($category != null) {
            try {
                $this->serializer->deserialize($request->getContent(), Category::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $category]);
            } catch (\UnexpectedValueException $e) {
                return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
            //validate the category
            $errors = $this->validator->validate($category);
            if (count($errors) > 0) {
                return new Response((string) $errors, Response::HTTP_BAD_REQUEST);
            }

            $this->repo->add($category);
        }

        return $this->categoryToJsonResponse($category);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete_id")
     */
    public function deleteCategory(?Category $category): Response
    {
        if ($category == null) return $this->categoryToJsonResponse(null); // Return standard not found response

        $this->repo->remove($category);
        return new JsonResponse(['deleted' => true]);
    }

    private function categoryToJsonResponse(null|array|Category $data, array $groups = ['rest']): Response
    {
        if ($data == null) return new Response('Not found', Response::HTTP_NO_CONTENT);

        return JsonResponse::fromJsonString(
            $this->serializer->serialize(
                $data,
                'json',
                ['groups' => $groups]
            )
        );
    }
}
