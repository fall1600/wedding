<?php
namespace Backend\BaseBundle\Controller\API;

use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class BaseController extends Controller
{

    /**
     * @param int $statusCode
     * @param string $message
     * @return JsonResponse
     */
    protected function createHttpExceptionResponse($statusCode = Response::HTTP_OK, $message = "")
    {
        return new JsonResponse(array('msg' => $message), $statusCode);
    }

    protected function createNotFoundJsonResponse($data = '', $headers = array())
    {
        return $this->createJsonResponse($data, Response::HTTP_NOT_FOUND, $headers);
    }

    protected function createJsonResponse($data = '', $status = 200, $headers = array())
    {
        $response = new JsonResponse(array(), $status, $headers);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_UNESCAPED_UNICODE);
        if($data === '' || !is_string($data)){
            $response->setData($data);
        }
        else{
            $response->setContent($data);
        }
        return $response;
    }

    protected function createJsonSerializeResponse($object, array $groups = array(), $status = 200, $headers = array())
    {
        $serializer = $this->get('jms_serializer');

        $context = $this->get('jms_serializer.serialization_context_factory')
            ->createSerializationContext()
            ->enableMaxDepthChecks()
            ->setSerializeNull($this->getParameter('serialize_null'));

        if($groups){
            $context->setGroups($groups);
        }
        return $this->createJsonResponse($serializer->serialize($object, 'json', $context), $status, $headers);
    }

    protected function createValidatorErrorJsonResponse(ConstraintViolationListInterface $constraintViolationList, $status = Response::HTTP_BAD_REQUEST, $headers = array())
    {
        $errors = $this->get('api.validator.error.convertor')->toArray($constraintViolationList);
        return $this->createJsonResponse($errors, $status, $headers);
    }
}