<?php

namespace AppBundle\Service;

class ApiResponse extends BaseService
{
    /**
     *  Function to create API Error Response.
     *
     *  @return array
     */
    public function createApiErrorResponse($message,$code)
    {
        $response = [
            'Response' => [
                'reasonCode' => '1',
                'reasonText' => $this->translator->trans('api.response.failure'),
                'error' => [
                    'code' => $code,
                    'text' => $message
                ],
            ]
        ];

        return $response;
    }

    public function createSuccessFetchCustomerSync()
    {
        return [
            'Response' => [
                'reasonCode' => '0',
                'reasonText' => $this->translator->trans('api.salesforce.success.fetchcustomersuccess'),
            ]
        ];
    }

    public function createNewAccessToken($token)
    {
        return [
            'Response' => [
                'reasonCode' => '0',
                'reasonText' => $this->translator->trans('api.salesforce.success.newaccesstoken'),
                'accessToken' => $token
            ]
        ];
    }
}
