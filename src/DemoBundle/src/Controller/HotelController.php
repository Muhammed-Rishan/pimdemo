<?php


namespace DemoBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Hotel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HotelController extends AbstractController
{
    /**
     * @Route("/hotel")
     */
    public function indexAction(Request $request): Response
    {
        $hotels = new Hotel\Listing();
        $hotels = $hotels->load();

        return $this->render('@DemoBundle/Hotel/index.html.twig', [
            'hotels' => $hotels
        ]);
    }
}
