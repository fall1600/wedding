<?php
/**
 * Created by PhpStorm.
 * User: bubble
 * Date: 2016/9/2
 * Time: 上午 10:48
 */

namespace Backend\BaseBundle\Controller\API;

use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait APITrait
{
    protected function createHttpException($statusCode, $message = null, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        return new HttpException($statusCode, $message, $previous, $headers, $code);
    }

    protected function createNotFoundJsonResponse($data = null, $headers = array())
    {
        return $this->createJsonResponse($data, Response::HTTP_NOT_FOUND, $headers);
    }

    protected function createJsonResponse($data = null, $status = 200, $headers = array())
    {
        $response = new JsonResponse(array(), $status, $headers);

        if(!is_string($data)){
            $response->setData($data);
        }
        else{
            $response->setContent($data);
        }
        return $response;
    }

    protected function createJsonSerializeResponse($object, array $groups = array())
    {
        $serializer = $this->get('jms_serializer');

        $context = $this->get('jms_serializer.serialization_context_factory')
            ->createSerializationContext()
            ->enableMaxDepthChecks()
            ->setSerializeNull($this->getParameter('serialize_null'));

        if($groups){
            $context->setGroups($groups);
        }
        return $this->createJsonResponse($serializer->serialize($object, 'json', $context));
    }
}