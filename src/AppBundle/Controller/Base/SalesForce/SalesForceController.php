<?php


namespace AppBundle\Controller\Base\SalesForce;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Constants\GeneralConstants;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class SalesForceController
 * @package AppBundle\Controller\Base\SalesForce
 */
class SalesForceController extends FOSRestController
{
    /**
     * @Rest\Get("/salesforce-connect", name="home-page")
     * @param Request $request
     * @return mixed
     */
    public function salesForcePage(Request $request)
    {
        $token = $request->getSession()->get('user_token');
        if (!$token || !$this->get('app.user_api_service')->checkRequestToken($token)) {

            return $this->redirect($this->generateUrl('user_login'));
        }

        return $this->render('@App/salesforce_connect.html.twig');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Rest\Get("/salesforce-connect-action", name="api-salesforce-connect")
     * Connect to sales-force and get the tokens
     */
    public function connectAction(Request $request)
    {
        try {
            // Read client id/secret and redirect uri from the url
            $client_id = $request->get('client_id');
            $client_secret = $request->get('client_secret');
            $redirect_uri = $request->get('redirect_uri');

            // If the client requests are empty then throw bad request exception
            if (!$client_id || !$client_secret || !$redirect_uri) {
                throw new BadRequestHttpException(ErrorConstants::BAD_CONNECT_REQUEST);
            }

            // Store the information in session so that it can be used in callback
            $request->getSession()->set("SFClientId", $client_id);
            $request->getSession()->set("SFClientSecret", $client_secret);
            $request->getSession()->set("SFRedirectURI", $redirect_uri);

            // Connect to sales-force
            return $this->redirect("https://login.salesforce.com/services/oauth2/authorize?response_type=code&client_id=" . $client_id . "&redirect_uri=" . $redirect_uri);
        } catch (BadRequestHttpException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param Request $request
     * @Rest\Get("/salesforce-callback", name="api-salesforce-callback")
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * Callback controller where sales-force returns code and the access token api is called here.
     */
    public function sfCallback(Request $request)
    {
        try {
            // Read the code from the URL
            $code = $request->get('code');
            if (!$code) {
                throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
            }
            // Read session values
            $clientId = $request->getSession()->get('SFClientId');
            $clientSecret = $request->getSession()->get('SFClientSecret');
            $redirectUri = $request->getSession()->get('SFRedirectURI');

            if (!$clientId || !$clientSecret || !$redirectUri) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_AUTHORIZATION);
            }
            // Connect to salesforce using the code and client secret.
            // If the response is 200 then store the tokens in browser's local storage
            $result = $this->container->get('salesforce_service')
                ->ConnectToSalesforce($code, $clientId, $clientSecret, $redirectUri)
            ;
            $this->addFlash(
                'salesforce_connect_success',
                "Connected to Salesforce Successfully"
            );
            return $this->render(
                'AppBundle::salesforce_oauth.html.twig',
                array("RefreshToken" => $result->getRefreshToken())
            );
        } catch (\Exception $exception) {
            throw $exception;
        }
    }


    /**
     * @param Request $request
     * @Rest\Post("/fetch-salesforce-contacts", name="api-fetch-sf-contacts")
     * @return mixed
     * @throws \Exception
     * Fetch customers from sales force based on the date range
     */
    public function fetchCustomersAction(Request $request)
    {
        $response = null;
        try {
            // Validate the headers.
            $result = $this->container->get('authentication_token')->validateSfHeaders($request);

            // Get the date range from API body
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');

            // Fetch customers
            if ($result) {
                $sfIds = $this->container->get('salesforce_service')
                    ->FetchCustomers($result[1], $fromDate, $toDate)
                ;
                $response = $this->get('api_response')
                    ->createUserApiSuccessResponse(
                        'api.response.salesforce.success.fetch_customer_success',
                        'sf_ids',
                        $sfIds? base64_encode($sfIds): ''
                    )
                ;
            }
        } catch (\Exception $exception) {
            throw $exception;
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     * @Rest\Post("/newtoken", name="api-fetch-token")
     */
    public function newTokenAction(Request $request)
    {
        $response = null;
        try {
            // Validate the headers.
            $result = $this->container->get('authentication_token')
                ->validateSfHeaders($request,GeneralConstants::NEW_TOKEN)
            ;

            // Generate new token from the existing refresh tokens
            if($result) {
                $response = $this->container->get('salesforce_service')->NewAccessToken($result);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }

        return $response;
    }
}
