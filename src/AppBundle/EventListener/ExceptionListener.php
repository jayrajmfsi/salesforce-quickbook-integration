<?php

namespace AppBundle\EventListener;

use AppBundle\Service\BaseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener extends BaseService
{
    /**
     * Function for handling exceptions
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $status = method_exists($event->getException(), 'getStatusCode')
            ? $event->getException()->getStatusCode()
            : 500;

        $this->logger->error("Error",
            [
                $status => $event->getException()->getMessage(),
                'TRACE' => $event->getException()->getTraceAsString()
            ]
        );

        $responseService = $this->serviceContainer->get('api_response');
        // Creating Http Error response.
        $result = $responseService->createApiErrorResponse();
        $response = new JsonResponse($result, $status);
        // Logging Exception in Exception log.
        $this->logger->error('Data Integration Exception :', [
            'Response' => [
                'Headers' => $response->headers->all(),
                'Content' => $response->getContent()
            ]
        ]);

        $event->setResponse($response);
    }
}
