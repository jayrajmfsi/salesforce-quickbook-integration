<?php


namespace AppBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use AppBundle\Service\BaseService;

class RequestListener extends BaseService
{
    /**
     * @var LoggerInterface
     */
    private $apiLogger;

    /**
     *  RequestListener constructor.
     *
     * @param LoggerInterface $apiLogger
     */
    public function __construct(LoggerInterface $apiLogger)
    {
        $this->apiLogger = $apiLogger;
    }

    /**
     *  Function for api request authorization.
     *
     * @param GetResponseEvent $event
     *
     * @return boolean
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Checking the route hit by request is topps API route or not.
        $route = $request->attributes->get('_route');

        // Checking if request is for APIs.
        if (0 !== strpos($route, 'api_v')) {
            return true;
        }

        $this->setRequestContent($request);

        // Logging request.
        $this->apiLogger->debug('API Request: ', [
            'Request' => [
                'headers' => $request->headers->all(),
                'content' => $request->getContent()
            ]
        ]);
    }

    /**
     * Function to format request content.
     *
     * @param Request $request
     *
     * @return bool|string
     */
    private function setRequestContent(Request $request)
    {
        $content = $request->getContent();

        if ($request->isMethod('GET') && empty($content)) {
            $content = base64_decode($request->get('data'));
            $request->initialize($request->query->all(), array(), $request->attributes->all(),
                $request->cookies->all(), array(), $request->server->all(), $content);
            $request->headers->set('Content-Length', strlen($content));
        }

        return $content;
    }
}
