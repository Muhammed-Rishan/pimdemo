<?php

namespace ProductBundle\Controller;

//use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    class ProductController extends AbstractController
{
    /**
     * @Route("/product/{id}", name="products")
     */
    public function indexAction(Request $request, $id): Response
    {
        $object = Product::getById($id);

        return $this->render('@ProductBundle/index.html.twig', [
            'object' => $object
        ]);

    }
}

