<?php
/**
 * UserProcessing Service
 * @category Service
 * @author <jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Entity\OAuth;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserApiProcessingService extends BaseService
{
    /**
     *  Function to validate the Create User request.
     *
     *  @param array $requestContent
     *
     *  @return array
     */
    public function validateCreateUserRequest($requestContent)
    {
        $validateResult['status'] = false;
        try {

            $content = isset($requestContent['UserRequest'])? $requestContent['UserRequest'] : null;
            // Checking that all the required keys should be present.
            if (
                empty($requestContent['UserRequest'])
                ||  empty($content['username'])
                ||  empty($content['email_id'])
                ||  empty($content['password'])
                ||  empty($content['confirm_password'])
                ||  empty($content['sf_account_id'])
                ||  empty($content['sf_client_id'])
                ||  empty($content['sf_client_secret'])
                ||  empty($content['sf_redirect_uri'])
                ||  empty($content['qb_client_id'])
                ||  empty($content['qb_client_secret'])
                ||  empty($content['qb_redirect_uri'])
            ) {
                throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
            }

            // validate username
            $this->validateUserName($content['username']);
            // Validating Email
            $this->validateEmail($content['email_id']);
            // validate user password
            $this->validatePassword(
                $content['password'],
                $content['confirm_password']
            );

            $validateResult['user']['data'] = $content;
            $validateResult['status'] = true;
        } catch (AccessDeniedHttpException $ex) {
            throw $ex;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (ConflictHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $validateResult;
    }

    public function validateLoginRequest($requestContent)
    {
        $validateResult['status'] = false;
        try {
            $content = isset($requestContent['UserRequest'])? $requestContent['UserRequest'] : null;
            // Checking that all the required keys should be present.
            if (
                empty($requestContent['UserRequest'])
                ||  empty($content['username'])
                ||  empty($content['password'])
            ) {
                throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
            }
            // check user's username and password
            $validationResult = $this->serviceContainer->get('authentication_token')
                ->validateUserCredentials([
                    'username' => $content['username'],
                    'password' => $content['password']
                ])
            ;
            /** @var User $user */
            $user = $validationResult['message']['user'];
            $sfData = $this->entityManager->getRepository('AppBundle:OAuth')
                ->fetchOAuthData($user, OAuth::SF_OAUTH)
            ;
            $qbData = $this->entityManager->getRepository('AppBundle:OAuth')
                ->fetchOAuthData($user, OAuth::QB_OAUTH)
            ;

            $sfData = !empty($sfData) ? $sfData[0] : [];
            $qbData = !empty($qbData) ? $qbData[0] : [];

            // Fetching returned User object on Success Case.
            $validateResult['user']['data'] = [
                'sf_oauth' => $sfData,
                'qb_oauth' => $qbData
            ];
            $validateResult['status'] = true;
            $validateResult['user']['token'] = $user->getUniqueId();
        } catch (AccessDeniedHttpException $ex) {
            throw $ex;
        } catch (BadRequestHttpException $ex) {
            throw $ex;
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        return $validateResult;

    }

    /**
     *  Function to validate Email While Updating OR Creating Object.
     *
     *  @param string $email
     *  @param User $user (default = null)
     *  @return User
     *
     *  @return void
     */
    public function validateEmail($email, $user = null)
    {
        // Checking if the email is valid.
        if (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestHttpException(ErrorConstants::INVALID_EMAIL_FORMAT);
        }

        $emailUser = $this->serviceContainer->get('fos_user.user_manager')->findUserByEmail($email);

        // Checking if Email is already taken by someone.
        if (!empty($emailUser) && (empty($user) || $user->getId() !== $emailUser->getId())) {
            throw new ConflictHttpException(ErrorConstants::EMAIL_EXISTS);
        }
        return $emailUser;
    }

    /**
     *  Function to validate UserName While Updating OR Creating Object.
     *
     *  @param string $userName
     *  @param User $user (default = null)
     *
     *  @return void
     */
    public function validateUserName($userName, $user = null)
    {
        // Checking if the username is valid.
        if (strlen($userName) > 50) {
            throw new UnprocessableEntityHttpException(ErrorConstants::INVALID_USERNAME);
        }

        $previousUser = $this->serviceContainer->get('fos_user.user_manager')->findUserByUsername($userName);


        // Checking if UserName is already taken by someone.
        if (!empty($previousUser) && (empty($user) || $user->getId() !== $previousUser->getId())) {
            throw new ConflictHttpException(ErrorConstants::USERNAME_EXISTS);
        }
    }

    /**
     * Validate the password fields in the register request
     * @param $password
     * @param $newPassword
     */
    public function validatePassword($password, $newPassword)
    {
        // Checking the format of Password.
        if (strlen($password) > 100 || strlen($newPassword) > 100 || $password !== $newPassword) {
            throw new BadRequestHttpException(ErrorConstants::INVALID_NEW_PASS_FORMAT);
        }
    }

    /**
     * @param $data
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function processCreateUserRequest($data)
    {
        $userManipulator = $this->serviceContainer->get('fos_user.util.user_manipulator');

        $user = $userManipulator
            ->create(
                $data['username'],
                $data['password'],
                $data['email_id'],
                true,
                false
            );
        $user->setSfAccountId($data['sf_account_id']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $sfOauth = new OAuth();
        $sfOauth->setUser($user)
            ->setClientId($data['sf_client_id'])
            ->setClientSecret($data['sf_client_secret'])
            ->setRedirectUri($data['sf_redirect_uri'])
            ->setAppType(OAuth::SF_OAUTH)
        ;
        $this->entityManager->persist($sfOauth);

        $qbOauth = new OAuth();
        $qbOauth->setUser($user)
            ->setClientId($data['qb_client_id'])
            ->setClientSecret($data['qb_client_secret'])
            ->setRedirectUri($data['qb_redirect_uri'])
            ->setAppType(OAuth::QB_OAUTH)
        ;

        $this->entityManager->persist($qbOauth);
        $this->entityManager->flush();
    }

    public function checkRequestToken($token)
    {
        $user = $this->entityManager->getRepository('AppBundle:User')->findOneBy(['uniqueId' => $token]);
        if ($user) {
            return $user;
        }

        return false;
    }
}
