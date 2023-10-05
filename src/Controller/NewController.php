<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Customer;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewController extends FrontendController
{



    public function newAction(Request $request): Response
    {
        $customer = Customer::getById(9);

        return $this->render('new.html.twig', [
            'customer' => $customer,
        ]);
    }

}
