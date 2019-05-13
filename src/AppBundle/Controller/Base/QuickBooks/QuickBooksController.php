<?php

namespace AppBundle\Controller\Base\QuickBooks;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class QuickBooksController extends FOSRestController
{
    /**
     * @Rest\Get("/salesforce-connect", name="home-page")
     */
    public function salesForcePage(Request $request)
    {
        $token = $request->getSession()->get('user_token');
        if (!$token || !$this->get('app.user_api_service')->checkRequestToken($token)) {

            return $this->redirect($this->generateUrl('user_login'));
        }

        $data = $request->query->get('data');

        return $this->render('@App/salesforce_connect.html.twig');
    }

    /**
     * @Rest\Post("connect-to-salesforce", name="connect-salesforce")
     * @param Request $request
     */
    public function connectToSalesforce(Request $request)
    {
        return new JsonResponse(
            [
                'reasonCode' => '0',
                'reasonText' => 'success'
            ]
        );
    }

    /**
     * Quickbooks page containing the button for connecting to quickbooks
     * @Route("quickbooks-connect", name="quickbooks-page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function qucickBooksPage(Request $request)
    {
        $token = $request->getSession()->get('user_token');
        if (!$token || !$this->get('app.user_api_service')->checkRequestToken($token)) {

            return $this->redirect($this->generateUrl('user_login'));
        }

        return $this->render('@App/quickbooks_connect.html.twig');
    }

    /**
     * Connect to quickbooks using the credentials provided in the request
     * @Rest\Post("connect-to-quickbooks", name="connect-to-quickbooks")
     * @param Request $request
     * @return mixed
     */
    public function connectToQuickbooks(Request $request)
    {
        return new JsonResponse(
            [
                'reasonCode' => '0',
                'reasonText' => 'success'
            ]
        );
    }
}
