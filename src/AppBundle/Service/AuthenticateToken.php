<?php


namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use AppBundle\Constants\GeneralSFConstants;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthenticateToken extends BaseService
{
    public function ValidateSFHeaders($request, $requestType)
    {
        try {
            $authorization = $request->headers->get('Authorization');
            $contentType = $request->headers->get('Content-Type');
            if ($authorization === null) {
                throw new BadRequestHttpException(ErrorConstants::BAD_FETCH_HEADERS);
            }
            $auth = explode(' ', $authorization);
            if (
                (sizeof($auth) !== 2)
                || ($auth[0] !== GeneralSFConstants::REQUEST_AUTHORIZATION_TYPE)) {
                throw new BadRequestHttpException(ErrorConstants::BAD_FETCH_HEADERS);
            }
            if ($requestType !== GeneralSFConstants::NEW_TOKEN) {
                if (($contentType === null)
                    || ($contentType !== GeneralSFConstants::CONTENT_TYPE_JSON)) {
                    throw new BadRequestHttpException(ErrorConstants::BAD_FETCH_HEADERS);
                }
            }

            return $auth[1];
        } catch (BadRequestHttpException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw $exception;
        }

    }
}
