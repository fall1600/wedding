<?php
namespace Backend\BaseBundle\Exception;


use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorResponseException extends \Exception
{
    protected $errorMessage;

    public function __construct($message, $code = JsonResponse::HTTP_BAD_REQUEST, Exception $previous = null)
    {
        $this->errorMessage = $message;
        parent::__construct('', $code, $previous);
    }
    
    public function makeJsonResponse($status = null, $headers = array())
    {
        if($status === null){
            $status = $this->getCode();
        }
        return new JsonResponse($this->errorMessage, $status, $headers);
    }
}
