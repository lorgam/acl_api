<?php

namespace App\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product", name="api_product_")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="get")
     */
    public function getProducts(): Response
    {
        return new Response('@TODO');
    }
}
