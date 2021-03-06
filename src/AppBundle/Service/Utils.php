<?php
/**
 *  Service Class for providing functions other than APIs main processing Logic to other Application Services.
 *
 *  @category Service
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */

namespace AppBundle\Service;

use AppBundle\Constants\ErrorConstants;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class Utils
 * @package AppBundle\Service
 */
class Utils extends BaseService
{
    /**
     * @var int
     */
    private $recursiveCount = 0;

    /**
     *  Function to Trim values in a PHP Array recursively.
     *
     *  @param array $arrayContent
     *
     *  @return array
     *  @throws BadRequestHttpException
     */
    public function trimArrayValues($arrayContent)
    {
        // checking first if $arrayContent passed is empty then returning the input parameter content.
        if (empty($arrayContent)) {
            return $arrayContent;
        }
        // Iterating through array Content and trimming values.
        foreach ($arrayContent as $key => $value) {
            if (is_array($value)) {
                // Increasing recursion count
                $this->recursiveCount++;
                // For Stopping recursive call to go beyond limit.
                if ($this->recursiveCount > 2000) {
                    break;
                }
                // recursive call to function for trimming Array content values.
                $arrayContent[trim($key)] = (!empty($value))
                    ? $this->trimArrayValues($value)
                    : $value
                ;

                // Removing non-trimmed Keys.
                if ((string)trim($key) !== (string)$key) {
                    unset($arrayContent[$key]);
                }
            } elseif (!is_array($value) && !is_object($value)) {
                $arrayContent[trim($key)] = is_string($value)
                    ? ((empty($value = trim($value)) && $value !== "0")
                        ? null
                        // Handling Html input
                        : htmlspecialchars($value, ENT_QUOTES, 'UTF-8'))
                    // for XSS Prevention
                    : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

                // Removing non-trimmed Keys From Array.
                if ((string)trim($key) !== (string)$key) {
                    unset($arrayContent[$key]);
                }
            }
        }

        return $arrayContent;
    }

    /**
     *  Function to fill Array data into the Class Object.
     *
     *  @param array $data
     *  @param string $className
     *  @param object $object
     *
     *  @return object
     *  @throws \Exception
     */
    public function createObjectFromArray($data, $className, $object)
    {
        // Checking the Object and Class Name Provided
        if (!empty($object) && !$object instanceof $className) {
            $this->logger->error('Invalid parameters provided to function '.__FUNCTION__);
            throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
        }

        if (empty($object)) {
            $resourceClass = new \ReflectionClass($className);
            if (!$resourceClass->isInstantiable()) {
                $this->logger->error($className. ' class name passed to function '.__FUNCTION__.
                    ' is not instantiable');
                throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
            }

            $object = $resourceClass->newInstance();
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        /**
         * filling Array data to Object.
         * all the properties of the class should be should be available to be set by Setters.
         */
        foreach ($data as $attribute => $value) {
            if (!$propertyAccessor->isWritable($object, $attribute)) {
                $this->logger->error('Invalid array data provided to function '. __FUNCTION__);
                throw new HttpException(500, ErrorConstants::INTERNAL_ERR);
            }
            $propertyAccessor->setValue($object, $attribute, $value);
        }

        return $object;
    }
}
