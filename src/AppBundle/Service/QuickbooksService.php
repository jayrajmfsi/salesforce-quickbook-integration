<?php

namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Entity\OAuth;
use QuickBooksOnline\API\Core\Http\Serialization\XmlObjectSerializer;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Customer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class QuickbooksService extends BaseService
{
    /**
     * Update Customers ids
     * @param $user
     * @param $accessTokenObj
     * @param $ids
     * @throws \Exception
     */
    public function updateCustomersData($user, $accessTokenObj, $ids)
    {
        $oauthObj = $this->entityManager->getRepository('AppBundle:OAuth')
            ->findOneBy(['appType' => OAuth::QB_OAUTH, 'user' => $user])
        ;

        if (!$oauthObj) {
            throw new BadRequestHttpException(ErrorConstants::INVALID_REQ_DATA);
        }

        // Prep Data Services
        $dataService = DataService::Configure(array(
            'auth_mode' => 'oauth2',
            'ClientID' => $oauthObj->getClientId(),
            'ClientSecret' =>  $oauthObj->getClientSecret(),
            'baseUrl' => 'development'
        ));

        $dataService->updateOAuth2Token($accessTokenObj);
        $dataService->setLogLocation("");
        foreach ($ids as $sfId) {
            $customer = $this->entityManager->getRepository('AppBundle:Customer')
                ->findOneBy(['user' => $user, 'sfCustId' => $sfId])
            ;

            if (!$customer) {
                throw new UnprocessableEntityHttpException(ErrorConstants::CONTENT_NOT_FOUND);
            }

            $this->createOrUpdateCustomer($dataService, $customer);
        }

        return true;
    }


    /**
     * @param DataService $dataService
     * @param \AppBundle\Entity\Customer $customer
     * @throws \Exception
     */
    public function createOrUpdateCustomer($dataService, $customer)
    {
        try {
            $id = $customer->getQbCustId();
            $name = $customer->getName();
            $requestData = [
                "BillAddr" => [
                    "Line1"=>  $customer->getMailingStreet(),
                    "City"=>  $customer->getMailingCity(),
                    "Country"=>  $customer->getMailingCountry(),
                    "PostalCode"=>  $customer->getMailingPostalCode()
                ],
                "Title"=>  "Mr",
                "DisplayName"=>  $customer->getName(),
                "PrimaryPhone"=>  [
                    "FreeFormNumber"=>  $customer->getPhone()
                ],
                "PrimaryEmailAddr"=>  [
                    "Address" => $customer->getEmail()
                ]
            ];
            // if id is set means qb customer is present in qb online so call the update api else call the create api
            if (!$id) {
                $customerObj = Customer::create($requestData);
                $resultingCustomerObj = $dataService->Add($customerObj);
                $error = $dataService->getLastError();

                if ($error) {
                    $this->logQuickbooksError($name, $error);
                } else {
                    $this->logQuickbooksError($name, null, $resultingCustomerObj);
                    $customer->setQbCustId($resultingCustomerObj->Id);

                    $this->entityManager->persist($customer);
                    $this->entityManager->flush();
                }
            } else {
                $entities = $dataService->Query("SELECT * FROM Customer where Id='$id'");
                $error = $dataService->getLastError();
                if ($error) {
                    $this->logQuickbooksError($name, $error);
                }
                if(empty($entities)) {
                    throw new UnprocessableEntityHttpException(ErrorConstants::CONTENT_NOT_FOUND);
                }
                //Get the first element
                $theCustomer = reset($entities);
                $requestData['sparse'] = 'false';
                $updateCustomer = Customer::update($theCustomer, $requestData);
                $resultingCustomerUpdatedObj = $dataService->Update($updateCustomer);
                if ($error) {
                    $this->logQuickbooksError($name, $error);
                }

                $xmlBody = XmlObjectSerializer::getPostXmlFromArbitraryEntity($resultingCustomerUpdatedObj, $urlResource);
                $successMsg = "Completed a Sparse Update on {$id} - updated object state is:\n{$xmlBody}\n\n";
                $this->logQuickbooksError($name, null, $successMsg);

            }
        } catch (UnprocessableEntityHttpException $ex) {
            throw $ex;
        } catch (HttpException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            $this->logger->error(__FUNCTION__.' Function failed due to Error :'. $ex->getMessage());

            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }
    }

    /**
     * @param $name
     * @param null $error
     * @param null $response
     */
    public function logQuickbooksError($name, $error = null, $response = null) {
        $responseLog = array();
        if ($error) {
            $responseLog['status_code'] = $error->getHttpStatusCode();
            $responseLog['helper_message'] = $error->getOAuthHelperError();
            $responseLog['response'] = $error->getResponseBody();
        } else if ($response) {
            $responseLog['success_response'] = $response;
        }
        $this->serviceContainer->get('monolog.logger.quickbooks_log')
            ->debug('Quickbooks Log for Customer: '. $name, $responseLog)
        ;
        if ($error) {
            throw new UnprocessableEntityHttpException(ErrorConstants::INTERNAL_ERR);
        }
    }
}
