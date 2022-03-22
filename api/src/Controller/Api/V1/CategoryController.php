<?php

namespace App\Controller\Api\V1;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Controller\AbstractApiJsonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/category", name="api_category_")
 */
class CategoryController extends AbstractApiJsonController
{

    public function __construct(SerializerInterface $serializer, CategoryRepository $repo, ValidatorInterface $validator)
    {
        parent::__construct($serializer, $repo, $validator);
    }

    /**
     * @Route("/", methods={"GET"}, name="get")
     */
    public function getCategories(): Response
    {
        return $this->entityToJsonResponse($this->repo->findAll());
    }

    /**
     * @Route("/", methods={"POST"}, name="post")
     */
    public function postCategoy(Request $request): Response
    {
        try {
            $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json');
        } catch (\UnexpectedValueException $e) {
            return $this->badRequestResponse($e->getMessage());
        }

        //validate the category
        $errors = $this->validator->validate($category);
        if (count($errors) > 0) {
            return $this->badRequestResponse((string) $errors);
        }

        $this->repo->add($category);
        return $this->entityToJsonResponse($category);
    }

    /**
     * @Route("/{id}", methods={"GET"}, name="get_id")
     */
    public function getCategory(?Category $category): Response
    {
        return $this->entityToJsonResponse($category);
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
                return $this->badRequestResponse($e->getMessage());
            }
            //validate the category
            $errors = $this->validator->validate($category);
            if (count($errors) > 0) {
                return $this->badRequestResponse((string) $errors);
            }

            $this->repo->add($category);
        }

        return $this->entityToJsonResponse($category);
    }

    /**
     * @Route("/{id}", methods={"DELETE"}, name="delete_id")
     */
    public function deleteCategory(?Category $category): Response
    {
        if ($category == null) {
            return $this->notFoundResponse();
        }

        $this->repo->remove($category);
        return $this->deletedResponse(true);
    }
}
