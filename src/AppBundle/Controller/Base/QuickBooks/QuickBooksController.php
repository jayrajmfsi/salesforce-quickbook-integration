<?php


namespace AppBundle\Controller\Base\QuickBooks;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class QuickBooksController extends AbstractFOSRestController
{
    /**
     * @Route("/", name="home-page")
     */
    public function salesforcePage(Request $request)
    {
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
    public function qucickBooksPage()
    {
        return $this->render('@App/quickbooks_connect.html.twig');
    }

    /**
     * Connect to quickbooks using the credentials provided in the request
     * @Rest\Post("connect-to-quickbooks", name="connect-to-quickbooks")
     * @param Request $request
     * @return mixed
     */
    public function connectQuickbooks(Request $request)
    {
        return new JsonResponse(
            [
                'reasonCode' => '0',
                'reasonText' => 'success'
            ]
        );
    }
}
