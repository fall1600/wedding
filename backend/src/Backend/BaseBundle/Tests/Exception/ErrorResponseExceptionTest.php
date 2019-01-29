<?php
namespace Backend\BaseBundle\Exception;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponseExceptionTest extends BaseTestCase
{
    public function test___construct_default_code()
    {
        //arrange
        $message = 'error';
        //act
        $e = new ErrorResponseException($message);

        //assert
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $e->getCode());
        $this->assertEquals($message, $this->getObjectAttribute($e, 'errorMessage'));
    }

    public function test___construct_custom_code()
    {
        //arrange
        $message = 'error';
        //act
        $e = new ErrorResponseException($message, Response::HTTP_FORBIDDEN);

        //assert
        $this->assertEquals(Response::HTTP_FORBIDDEN, $e->getCode());
        $this->assertEquals($message, $this->getObjectAttribute($e, 'errorMessage'));
    }

    public function test_makeJsonResponse_with_message()
    {
        //arrange
        $message = 'error';
        $e = new ErrorResponseException($message, Response::HTTP_FORBIDDEN);

        //act
        $result = $e->makeJsonResponse();

        //assert
        $this->assertEquals($message, json_decode($result->getContent(), true));
    }

    public function test_makeJsonResponse()
    {
        //arrange
        $message = 'error';
        $e = new ErrorResponseException($message, Response::HTTP_FORBIDDEN);

        //act
        $result = $e->makeJsonResponse();

        //assert
        $this->assertEquals($message, json_decode($result->getContent(), true));
        $this->assertEquals(Response::HTTP_FORBIDDEN, $result->getStatusCode());
    }

    public function test_makeJsonResponse_with_custom_code()
    {
        //arrange
        $message = 'error';
        $e = new ErrorResponseException($message, Response::HTTP_FORBIDDEN);

        //act
        $result = $e->makeJsonResponse(Response::HTTP_REQUEST_TIMEOUT);

        //assert
        $this->assertEquals($message, json_decode($result->getContent(), true));
        $this->assertEquals(Response::HTTP_REQUEST_TIMEOUT, $result->getStatusCode());
    }
}