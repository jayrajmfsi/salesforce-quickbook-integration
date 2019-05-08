<?php


namespace AppBundle\Controller\Base\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;


class UserController extends AbstractFOSRestController
{
    /**
     * @Route("/login", name="user_login")
     */
    public function loginPage()
    {
        return $this->render('@App/Security/login.html.twig');
    }

    /**
     * @Rest\Post("/check-credentials", name="check-credentials")
     */
    public function checkLoginCredentials(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        return new JsonResponse(
            [
                'reasonCode' => '0',
                'reasonText' => 'success'
            ]
        );
    }

    /**
     * @Route("/register", name="user_register")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerPage()
    {
        return $this->render('@App/Security/register.html.twig');
    }

    /**
     * Sync Data
     * @Rest\Get("sync-data", name="sync-data")
     */
    public function syncPage()
    {
        return $this->render('@App/sync_data.html.twig');
    }

    /**
     * @Rest\Post("sync-data", name="syncing-data")
     * @param Request $request
     * @return JsonResponse
     */
    public function syncData(Request $request)
    {
        return new JsonResponse(
            [
                'reasonCode' => '0',
                'reasonText' => 'success'
            ]
        );
    }
}
