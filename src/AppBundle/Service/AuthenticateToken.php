<?php

namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use AppBundle\Constants\GeneralConstants;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthenticateToken extends BaseService
{
    /**
     *  Function to Validate User and create Response Array.
     *
     * @param array $credentials
     *
     * @return array
     */
    public function validateUserCredentials($credentials)
    {
        $validateResult['status'] = false;
        try {
            /** @var User $user */
            $user = $this->getUser($credentials['username']);
            // checking if email is valid or not.
            if (empty($user)) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_CRED);
            }
            // fetch encoder service to encode password
            $encoder = $this->serviceContainer->get('security.encoder_factory')->getEncoder($user);

            if (!$user->isEnabled()) {
                throw new UnprocessableEntityHttpException(ErrorConstants::DISABLEDUSER);
            }

            // Checking if Password Provided is right
            if ($user->getPassword() !== $encoder->encodePassword($credentials['password'], $user->getSalt())) {
                throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_CRED);
            }

            $validateResult['message']['user'] = $user;
            $validateResult['status'] = true;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error('User credentials validation failed due to Error : ' . $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }
        return $validateResult;
    }

    public function validateSfHeaders($request, $requestType = null)
    {
        try {
            $authorization = $request->headers->get('Authorization');
            $contentType = $request->headers->get('Content-Type');
            if ($authorization === null) {
                throw new BadRequestHttpException(ErrorConstants::BAD_FETCH_HEADERS);
            }
            $auth = explode(' ', $authorization);
            if (
                (count($auth) !== 2)
                || ($auth[0] !== GeneralConstants::REQUEST_AUTHORIZATION_TYPE)) {
                throw new BadRequestHttpException(ErrorConstants::BAD_FETCH_HEADERS);
            }
            if ($requestType !== GeneralConstants::NEW_TOKEN
                && (($contentType === null) || ($contentType !== GeneralConstants::CONTENT_TYPE_JSON))) {
                    throw new BadRequestHttpException(ErrorConstants::BAD_FETCH_HEADERS);
            }

            return $auth;
        } catch (BadRequestHttpException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     *  Function to return User Object from email input.
     *
     * @param string $username
     * @param string $password (default = null)
     *
     * @return User $user
     */
    public function getUser($username, $password = null)
    {
        $userManager = $this->serviceContainer->get('fos_user.user_manager');

        $params = ['username' => $username];

        // Checking if password is set then adding the password to the params.
        if (!empty($password)) {
            $params['password'] = $password;
        }

        return $userManager->findUserBy($params);
    }
}
