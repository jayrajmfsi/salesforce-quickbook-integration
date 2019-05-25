<?php
/**
 *  Quickbooks Controller for connecting with quickbooks and calling the update customers api
 *  @category Controller
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Controller\Base\QuickBooks;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Constants\GeneralConstants;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use QuickBooksOnline\API\DataService\DataService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpFoundation\Request;

class QuickBooksController extends FOSRestController
{
    /**
     * Quickbooks page containing the button for connecting to quickbooks
     * @Rest\Get("quickbooks-connect", name="quickbooks-page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function quickBooksPage(Request $request)
    {
        // check if token set is correct else logout user
        $token = $request->getSession()->get('user_token');
        if (!$token || !$this->get('app.user_api_service')->checkRequestToken($token)) {

            return $this->redirect($this->generateUrl('user_login'));
        }

        return $this->render('@App/quickbooks_connect.html.twig');
    }

    /**
     * Connect to quickbooks using the credentials provided in the request
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Rest\Get("/quickbooks-connect-action", name="api-quickbooks-connect")
     * Connect to quickbooks and get the tokens
     */
    public function connectAction(Request $request)
    {
        // Read client id/secret and redirect uri from the url
        $clientId = $request->get('client_id');
        $clientSecret = $request->get('client_secret');
        $redirectUri = $request->get('redirect_uri');

        // If the client requests are empty then throw bad request exception
        if (!$clientId || !$clientSecret || !$redirectUri) {
            throw new BadRequestHttpException(ErrorConstants::BAD_CONNECT_REQUEST);
        }

        // Configure to DataService
        $dataService = DataService::Configure(array(
            'auth_mode' => GeneralConstants::OAUTH_MODE,
            'ClientID' => $clientId,
            'ClientSecret' => $clientSecret,
            'RedirectURI' => $redirectUri,
            'scope' => $this->container->getParameter('oauth_scope'),
            'baseUrl' => $this->container->getParameter('base_url')
        ));
        // Store the information in session so that it can be used in callback
        $request->getSession()->set("QBClientId", $clientId);
        $request->getSession()->set("QBClientSecret", $clientSecret);
        $request->getSession()->set("QBRedirectURI", $redirectUri);


        $oauth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $authUrl = $oauth2LoginHelper->getAuthorizationCodeURL();


        return $this->redirect($authUrl);
    }

    /**
     * Callback controller where quickbooks returns code and the access token api is called here.
     * @param Request $request
     * @Rest\Get("/quickbooks-callback", name="api-quickbooks-callback")
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function callbackAction(Request $request)
    {
        try {
            $clientId = $request->getSession()->get('QBClientId');
            $clientSecret = $request->getSession()->get('QBClientSecret');
            $oauthRedirectUri = $request->getSession()->get('QBRedirectURI');

            // prepare request data
            $dataService = DataService::Configure(array(
                'auth_mode' => GeneralConstants::OAUTH_MODE,
                'ClientID' => $clientId,
                'ClientSecret' => $clientSecret,
                'RedirectURI' => $oauthRedirectUri,
                'scope' => $this->container->getParameter('oauth_scope'),
                'baseUrl' => $this->container->getParameter('base_url')
            ));

            // fetch code
            $oauth2LoginHelper = $dataService->getOAuth2LoginHelper();
            $url = $request->server->get('QUERY_STRING');
            parse_str($url, $qsArray);
            $parseUrl = array('code' => $qsArray['code'],
                'realmId' => $qsArray['realmId']
            );
            /*
             * Update the OAuth2Token
             */
            $accessToken = $oauth2LoginHelper->exchangeAuthorizationCodeForToken(
                $parseUrl['code'],
                $parseUrl['realmId']
            );
            $dataService->updateOAuth2Token($accessToken);
            /*
             * Check whether the access token is present or not.
             */
            if ($accessToken) {
                $em = $this->getDoctrine()->getManager();
                $client = $em->getRepository('AppBundle:OAuth')
                    ->findOneBy(
                        array(
                            'clientId' => $accessToken->getClientID(),
                            'clientSecret' => $accessToken->getClientSecret(),
                        )
                    );
                /**
                 * If the client Id and client secret are not found then show error.
                 * Else update the access and refresh token.
                 */
                if (!$client) {
                    throw new UnauthorizedHttpException(ErrorConstants::INVALID_AUTHORIZATION);
                } else {
                    $client->setAccessToken($accessToken->getAccessToken());
                    $client->setRefreshToken($accessToken->getRefreshToken());
                    $client->getUser()->setQbRealmId($accessToken->getRealmID());
                    $em->persist($client);
                }
                $em->flush();
                $request->getSession()->set('refresh_token', $accessToken->getRefreshToken());
                $request->getSession()->set('sessionAccessToken', $accessToken);

                $this->addFlash(
                    'quickbooks_connect_success',
                    GeneralConstants::QUICKBOOKS_SUCCESS_MESSAGE
                );
                return $this->render(
                    'AppBundle::quickbooks_oauth.html.twig',
                    array("RefreshToken" => $accessToken->getRefreshToken())
                );
            } else {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_AUTHORIZATION);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Calling the update customers api for syncing data
     * @param Request $request
     * @Rest\Get("/update-quickbooks-contacts", name="api-qb-update-contacts")
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function updateCustomersAction(Request $request)
    {
        // check if token is correct
        $token = $request->getSession()->get('user_token');
        $user = $this->get('app.user_api_service')->checkRequestToken($token);

        if (!$token || !$user) {
            return $this->redirect($this->generateUrl('user_login'));
        }

        // fetch access token and call the api using that
        $accessTokenObj = $request->getSession()->get('sessionAccessToken');
        $sfIds = explode(',', base64_decode($request->get('sf_ids')));

        $update = $request->get('update');

        if ($update && $accessTokenObj) {
            $this->get('quickbooks_service')->updateCustomersData($user, $accessTokenObj, $sfIds);
            return $this->redirect($this->generateUrl('sync-data').'?update=1');
        } else {
            throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
        }
    }
}
