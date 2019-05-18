<?php

namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Constants\GeneralSFConstants;
use AppBundle\Entity\Customer;
use AppBundle\Entity\OAuth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class SalesforceService
 * @package AppBundle\Service
 */
class SalesforceService extends BaseService
{

    /**
     * @param $code
     * @param $client_id
     * @param $client_secret
     * @param $redirect_uri
     * @return bool|string
     * @throws \Exception
     * Method to connect to salesforce.
     */
    public function ConnectToSalesforce($code, $client_id, $client_secret, $redirect_uri)
    {
        try {
            //Check if the client Id and secret are present or not
            $OAuthObject = $this->CheckClient($client_id, $client_secret);
            // Make Curl request to sales-force server and get the oauth tokens.
            $url = GeneralSFConstants::SF_AUTH_URI;
            $postField = "grant_type=" . GeneralSFConstants::GrantType . "&code=" . $code . "&client_id=" . $client_id . "&client_secret=" . $client_secret . "&redirect_uri=" . $redirect_uri;
            $requestType = 1;
            $headers = array();
            $headers[] = 'Content-Type: '.GeneralSFConstants::CONTENT_TYPE_URL_ENCODED;

            $tokenResponse = $this->MakeCurlRequest($requestType,$url,$postField,$headers);

            // Find the user object and store the tokens accordingly.
            $accessToken = $tokenResponse['access_token'];
            $instanceUrl = $tokenResponse['instance_url'];
            $tokenType = $tokenResponse['token_type'];
            $refreshToken = $tokenResponse['refresh_token'] ? $tokenResponse : GeneralSFConstants::DUMMY_REFRESH_TOKEN;

            $result = $this->StoreTokens($accessToken, $refreshToken, $instanceUrl, $tokenType, $OAuthObject);

            return $result;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $accessToken
     * @param $refreshToken
     * @param $instanceUrl
     * @param $tokenType
     * @param OAuth $OAuthObject
     * @return bool
     * @throws \Exception
     * Method to store tokens in the database.
     */
    public function StoreTokens($accessToken, $refreshToken, $instanceUrl, $tokenType, $OAuthObject)
    {
        try {
            // Store the token in the database.
            $OAuthObject->setAccessToken($accessToken);
            $OAuthObject->setRefreshToken($refreshToken);

            if ($tokenType) {
                $OAuthObject->setGrantType($tokenType);
            }

            if ($instanceUrl) {
                $OAuthObject->getUser()->setSFinstanceUrl($instanceUrl);
            }
            $this->entityManager->persist($OAuthObject);
            $this->entityManager->flush();

            return $OAuthObject;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $clientId
     * @param $clientSecret
     * @return object|null
     * @throws \Exception
     * Method to validate client information.
     */
    public function CheckClient($clientId, $clientSecret)
    {
        try {

            // Get OAuth object from the DB.
            $OAuthObject = $this->entityManager
                ->getRepository('AppBundle:OAuth')
                ->findOneBy(array('clientId' => $clientId, 'clientSecret' => $clientSecret));

            // If OAuth object is not found then throw NotFoundHttpException
            if (!$OAuthObject) {
                throw new NotFoundHttpException(ErrorConstants::CLIENT_NOT_FOUND);
            }
            return $OAuthObject;
        } catch (NotFoundHttpException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $refreshToken
     * @param $fromDate
     * @param $toDate
     * @return mixed
     * @throws \Exception
     * Method to fetch customers from salesforce.
     */
    public function FetchCustomers($refreshToken, $fromDate, $toDate)
    {
        try {
            // Search the refresh token in the database.
            // If not found, then throw unauthorized 401 exception
            $OAuthObject = $this->entityManager
                ->getRepository('AppBundle:OAuth')
                ->findOneBy(array('refreshToken' => $refreshToken));
            if (!$OAuthObject) {
                throw  new UnauthorizedHttpException(null, ErrorConstants::UNAUTHORIZED_CODE);
            }

            // Get access token.
            $accessToken = $OAuthObject->getAccessToken();

            // Make Curl request to fetch the customers.
            $url = $OAuthObject->getUser()->getSfInstanceUrl() . '/services/data/v45.0/query?q=select+id+,+Name+,+Email,+Phone+,+MailingStreet+,+MailingCity+,+MailingState+,+MailingPostalCode+,+MailingCountry+from+Contact+where+accountid=\'' . $OAuthObject->getUser()->getSfAccountId() . '\'AND+CreatedDate>=' . $fromDate . 'T00:00:00.000Z+AND+CreatedDate<=' . $toDate . 'T07:13:54.000Z';
            $postField = null;
            $requestType = 0;
            $headers = array();
            $headers[] = 'Authorization:' . 'Bearer ' . ' ' . $accessToken;
            $response = $this->MakeCurlRequest($requestType,$url,$postField,$headers);
            if (isset($response[0])) {
                if ($response[0]['message'] === ErrorConstants::UNAUTHORIZED_MESSAGE &&
                    $response[0]['errorCode'] === ErrorConstants::UNAUTHORIZED_CODE) {
                    throw  new UnauthorizedHttpException(null, ErrorConstants::UNAUTHORIZED_CODE);
                }
            }
            // If record size is zero, then throw content not found exception.
            $size = $response['totalSize'];
            if ($size === 0) {
                return null;
            }
            // Insert the records into the database in a loop
            for ($i = 0; $i < $size; $i++) {
                $insertResult[] = $this->InsertCustomers($response['records'][$i], $OAuthObject->getUser());
            }
            $this->entityManager->flush();

            return implode(',', $insertResult);
        } catch (UnauthorizedHttpException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $records
     * @param $user
     * @return mixed
     * @throws \Exception
     * Function to insert the customer details into the database.
     */
    public function InsertCustomers($records, $user)
    {
        try {

            // Check if the SFCustomerID is already present then skip the record.
            // Else create new customer in the database.
            $id = $records['Id'];
            $findCustomer = $this->entityManager
                ->getRepository('AppBundle:Customer')
                ->findOneBy(array('sfCustId' => $id));
            if ($findCustomer) {
                $customer = $findCustomer;
            } else {
                $customer = new Customer();
            }

            // Parse the result.
            $name = $records['Name'];
            $email = $records['Email'];
            $phone = $records['Phone'];
            $mailingStreet = $records['MailingStreet'];
            $mailingCity = $records['MailingCity'];
            $mailingState = $records['MailingState'];
            $mailingPostalcode = $records['MailingPostalCode'];
            $mailingCountry = $records['MailingCountry'];

            $customer->setSfCustId($id);
            $customer->setEmail($email);
            $customer->setName($name);
            $customer->setPhone($phone);
            $customer->setMailingStreet($mailingStreet);
            $customer->setMailingCity($mailingCity);
            $customer->setMailingState($mailingState);
            $customer->setMailingPostalCode($mailingPostalcode);
            $customer->setMailingCountry($mailingCountry);
            $customer->setUser($user);
            $this->entityManager->persist($customer);

            return $id;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $refreshToken
     * @return mixed
     * @throws \Exception
     * Method to generate new access token.
     */
    public function NewAccessToken($refreshToken)
    {
        try {
            $OAuthObject = $this->entityManager
                ->getRepository('AppBundle:OAuth')
                ->findOneBy(array('refreshToken' => $refreshToken));
            if (!$OAuthObject) {
                throw  new UnauthorizedHttpException(null, $this->translator->trans('api.salesforce.failure.invalid_refresh_token'));
            }

            // Get new access tokens.
            $url = GeneralSFConstants::SF_AUTH_URI;
            $requestType = 1;
            $postField = "grant_type=".GeneralSFConstants::RefreshType."&client_id=".$OAuthObject->getClientId()."&client_secret=".$OAuthObject->getClientSecret()."&refresh_token=".$refreshToken;
            $headers = array();
            $headers[] = 'Content-Type: '.GeneralSFConstants::CONTENT_TYPE_URL_ENCODED;
            $response = $this->MakeCurlRequest($requestType,$url,$postField,$headers);

            // Store the new tokens in the database.
            $OAuthObject->setAccessToken($response['access_token']);
            $this->entityManager->persist($OAuthObject);
            $this->entityManager->flush();

            $response = $this->serviceContainer->get('api_response')
                ->createUserApiSuccessResponse(
                    'api.response.salesforce.success.new_access_token',
                    'accessToken',
                    $response['access_token']
                )
            ;

            return $response;
        } catch (UnauthorizedHttpException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $requestType
     * @param $url
     * @param $postField
     * @param $headers
     * @return mixed
     * @throws \Exception
     * Common method to make curl requests.
     */
    public function MakeCurlRequest($requestType, $url, $postField, $headers)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postField);
            curl_setopt($ch, CURLOPT_POST, $requestType);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $this->serviceContainer->get('monolog.logger.exception')
                    ->debug('Curl Api exception: '. $ch)
                ;

                throw new \Exception(ErrorConstants::INTERNAL_ERR);
            }
            $this->serviceContainer->get('monolog.logger.api')
                ->debug('Curl Api Request Info: ', curl_getinfo($ch))
            ;
            // Json_decode the response
            $response = json_decode($result, true);
            curl_close($ch);

            $this->serviceContainer->get('monolog.logger.api')->debug('Curl Api Response: '. $result);

            return $response;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
