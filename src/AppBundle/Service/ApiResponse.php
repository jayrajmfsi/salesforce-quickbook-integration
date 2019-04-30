<?php

namespace AppBundle\Service;

class ApiResponse extends BaseService
{
    /**
     *  Function to create API Error Response.
     *
     *  @return array
     */
    public function createApiErrorResponse()
    {
        $response = [
            'Response' => [
                'reasonCode' => '1',
                'reasonText' => $this->translator->trans('api.response.failure'),
                'error' => [
                    'code' => '500',
                    'text' => 'Some Error Occurred'
                ],
            ]
        ];

        return $response;
    }
}
