<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Customer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Service;
use Pimcore\Bundle\ApplicationLoggerBundle\ApplicationLogger;

class NewController extends FrontendController
{
    /**
     * @Route("/new")
     * @throws \Exception
     */
    public function newAction(Request $request): Response
    {

        $customer = Customer::getById(9);

        $fields = $customer->getName();

        foreach ($fields as $field) {
            $field->setLocked(true);
        }

        $customer->save();

        return $this->render('new.html.twig', [
            'customer' => $customer,
        ]);
    }

    /**
     * @Route("/iframe/summary")
     */
    public function summaryAction(Request $request): Response
    {
        $context = json_decode($request->get("context"), true);
        $objectId = $context["objectId"];

        $language = $context["language"] ?? "default_language";

        $object = Service::getElementFromSession('object', $objectId, '');

        if ($object === null) {
            $object = Service::getElementById('object', $objectId);
        }

        $response = '<h1>Title for language "' . $language . '": ' . $object->getData($language) . "</h1>";

        $response .= '<h2>Context</h2>';
        $response .= array_to_html_attribute_string($context);
        return new Response($response);
    }
    public function testsAction(ApplicationLogger $logger): void
    {
        $logger->error('Your error message');
        $logger->alert('Your alert');
        $logger->debug('Your debug message', ['foo' => 'bar']);
    }

    public function anotherAction(): void
    {

        $logger = $this->get(ApplicationLogger::class);
        $logger->error('Your error message');
    }
}
