<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class that wraps all the JsonControllers for the API, mainly it manages the responses and headers of the petitions
 */
abstract class AbstractApiJsonController extends AbstractController
{
    protected SerializerInterface $serializer;
    protected ServiceEntityRepository $repo;
    protected ValidatorInterface $validator;

    public function __construct(SerializerInterface $serializer, ServiceEntityRepository $repo, ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
        $this->repo = $repo;
        $this->validator = $validator;
    }

    protected function entityToJsonResponse(null|array|object $data, array $groups = ['rest']): Response
    {
        if ($data == null) return $this->notFoundResponse();

        return JsonResponse::fromJsonString(
            $this->serializer->serialize(
                $data,
                'json',
                ['groups' => $groups]
            )
        );
    }

    protected function notFoundResponse(): Response
    {
        return new Response('Not found', Response::HTTP_NO_CONTENT);
    }

    protected function deletedResponse(bool $deleted): JsonResponse
    {
        return new JsonResponse(['deleted' => $deleted]);
    }

    protected function badRequestResponse(string $message): Response
    {
        return new Response($message, Response::HTTP_BAD_REQUEST);
    }

    protected function arrayToJsonResponse(array $json): JsonResponse
    {
        return new JsonResponse($json);
    }
}
