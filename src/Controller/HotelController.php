<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\Asset;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HotelController extends FrontendController
{

    public function hotelAction(Request $request): Response
    {
        return $this->render('hotel/hotel.html.twig');
    }
    #[Template('hotel/gallery.html.twig')]
    public function galleryAction(Request $request): array
    {
        if ('asset' === $request->get('type')) {
            $asset = Asset::getById((int) $request->get('id'));
            if ('folder' === $asset->getType()) {
                return [
                    'assets' => $asset->getChildren()
                ];
            }
        }

        return [];
    }
    public function footerAction(Request $request): Response
    {
        return $this->render('footer.html.twig');
    }
    public function demoAction(Request $request): Response
    {
        return $this->render('demo.html.twig');
    }
}
