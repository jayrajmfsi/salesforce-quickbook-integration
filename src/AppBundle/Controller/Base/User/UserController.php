<?php
/**
 *  UserController Controller for declaring user related actions
 *  @category Controller
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Controller\Base\User;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Constants\GeneralConstants;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UserController extends FOSRestController
{
    /**
     * Login Page
     * @Rest\Get("/login", name="user_login")
     */
    public function loginPage(Request $request)
    {
        // Redirect to home page for already authenticated users
        $token = $request->getSession()->get('user_token');
        if ($token && $this->get('app.user_api_service')->checkRequestToken($token)) {

            return $this->redirect($this->generateUrl('home-page'));
        }

        return $this->render('@App/Security/login.html.twig');
    }

    /**
     * Check login credentials
     * @Rest\Post("/api-check-credentials", name="api-check-credentials")
     */
    public function checkLoginCredentials(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = null;
        try {
            $utils = $this->container->get('app.utils');
            $content = json_decode(trim($request->getContent()), true);
            // Trimming Request Content.
            $content = !empty($content) ? $utils->trimArrayValues($content) : $content;

            // Validating the request content.
            $validatedResult = $this->container->get('app.user_api_service')->validateLoginrequest($content);

            // Creating final response Array to be released from API Controller.
            $response = $this->container
                ->get('api_response')
                ->createUserApiSuccessResponse(
                    'api.response.success.message',
                    'UserResponse',
                    $validatedResult['user']['data']
                )
            ;
            // set session for user token
            $request->getSession()->set('user_token', $validatedResult['user']['token']);

        } catch (AccessDeniedHttpException $ex) {
            throw $ex;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (HttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $logger->error(__FUNCTION__.' function failed due to Error : '.
                $ex->getMessage());
            // Throwing Internal Server Error Response In case of Unknown Errors.
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $response;
    }

    /**
     * Show register page
     * @Rest\Get("/register", name="user_register")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerPage(Request $request)
    {
        // redirect already registered users
        $token = $request->getSession()->get('user_token');
        if ($token && $this->get('app.user_api_service')->checkRequestToken($token)) {

            return $this->redirect($this->generateUrl('home-page'));
        }
        return $this->render('@App/Security/register.html.twig');
    }

    /**
     * Logout action for logging out the user
     * @Rest\Get("/logout", name="logout")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logoutAction(Request $request)
    {
        // redirect already registered users
        if ($request->getSession()->get('user_token')) {
            $request->getSession()->remove('user_token');
        }

        return $this->render('@App/Security/login.html.twig');
    }

    /**
     * Register user data
     * @Rest\Post("/register", name="api-register-user")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function storeUserData(Request $request)
    {
        $logger = $this->container->get('monolog.logger.exception');
        // $response to be returned from API.
        $response = null;
        try {
            $utils = $this->container->get('app.utils');
            $content = json_decode(trim($request->getContent()), true);
            // Trimming Request Content.
            $content = !empty($content) ? $utils->trimArrayValues($content) : $content;

            // Validating the request content.
            $validatedResult = $this->container->get('app.user_api_service')->validateCreateUserRequest($content);

            // Processing the request and creating the final streamed response to be sent in response.
            $this->container->get('app.user_api_service')
                ->processCreateUserRequest($validatedResult['user']['data'])
            ;

            // Creating final response Array to be released from API Controller.
            $response = $this->container
                ->get('api_response')
                ->createUserApiSuccessResponse(
                    'api.response.success.message',
                    'UserResponse',
                    [
                    'status' => $this->container
                            ->get('translator.default')->trans('api.response.success.user_created')
                    ]
                )
            ;
            // flashbag to show user only once
            $this->addFlash(
                'account_create_success',
                GeneralConstants::ACCOUNT_CREATE_SUCCESS_MESSAGE
            );
        } catch (AccessDeniedHttpException $ex) {
            throw $ex;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (HttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $logger->error(__FUNCTION__.' function failed due to Error : '.
                $ex->getMessage());
            // Throwing Internal Server Error Response In case of Unknown Errors.
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $response;
    }

    /**
     * Sync Data page
     * @Rest\Get("sync-data", name="sync-data")
     */
    public function syncPage(Request $request)
    {
        $token = $this->get('session')->get('user_token');
        if (!$token || !$this->get('app.user_api_service')->checkRequestToken($token)) {

            return $this->redirect($this->generateUrl('user_login'));
        }
        $update = $request->get('update') ?? null;
        return $this->render('@App/sync_data.html.twig', ['updated' => $update]);
    }

    /**
     * Dashboard page for redirecting user to login or home page
     * @Rest\Get("/", name="dashboard")
     * @param Request $request
     * @return mixed
     */
    public function indexAction()
    {
        // if token is correct then home page else login
        $token = $this->get('session')->get('user_token');
        if ($token && $this->get('app.user_api_service')->checkRequestToken($token)) {
            return $this->redirect($this->generateUrl('home-page'));
        }

        return $this->redirect($this->generateUrl('user_login'));
    }
}
