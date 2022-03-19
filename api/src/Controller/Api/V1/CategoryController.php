<?php

namespace App\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="api_category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="get")
     */
    public function getCategories(): Response
    {
        return new Response('@TODO');
    }
}
