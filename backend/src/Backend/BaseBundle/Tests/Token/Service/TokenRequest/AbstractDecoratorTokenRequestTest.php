<?php
namespace Backend\BaseBundle\Tests\Token\Service\TokenRequest;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Backend\BaseBundle\Token\Service\TokenRequest\AbstractDecoratorTokenRequest;
use Backend\BaseBundle\Token\Service\TokenRequest\TokenRequestInterface;

class AbstractDecoratorTokenRequestTest extends BaseTestCase
{
    public function test___construct()
    {
        //arrange
        $tokenRequest = $this->getMockBuilder(TokenRequestInterface::class)
            ->getMockForAbstractClass();
        $decoratorTokenRequest = $this->getMockBuilder(AbstractDecoratorTokenRequest::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        //act
        $reflect = new \ReflectionClass($decoratorTokenRequest);
        $method = $reflect->getConstructor();
        $method->invoke($decoratorTokenRequest, $tokenRequest);

        //assert
        $this->assertEquals($tokenRequest, $this->getObjectAttribute($decoratorTokenRequest, 'tokenRequest'));
    }

    public function test_getId()
    {
        //arrange
        $tokenRequest = $this->getMockBuilder(TokenRequestInterface::class)
            ->setMethods(array('getId'))
            ->getMockForAbstractClass();
        $tokenRequest
            ->expects($this->once())
            ->method('getId')
            ->willReturn('id');

        $decoratorTokenRequest = $this->getMockBuilder(AbstractDecoratorTokenRequest::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->setObjectAttribute($decoratorTokenRequest, 'tokenRequest', $tokenRequest);

        //act
        $result = $decoratorTokenRequest->getId();

        //assert
        $this->assertEquals('id', $result);
    }

    public function test_getType()
    {
        //arrange
        $tokenRequest = $this->getMockBuilder(TokenRequestInterface::class)
            ->setMethods(array('getType'))
            ->getMockForAbstractClass();
        $tokenRequest
            ->expects($this->once())
            ->method('getType')
            ->willReturn('type');

        $decoratorTokenRequest = $this->getMockBuilder(AbstractDecoratorTokenRequest::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->setObjectAttribute($decoratorTokenRequest, 'tokenRequest', $tokenRequest);

        //act
        $result = $decoratorTokenRequest->getType();

        //assert
        $this->assertEquals('type', $result);
    }

    public function test_getData()
    {
        //arrange
        $tokenRequest = $this->getMockBuilder(TokenRequestInterface::class)
            ->setMethods(array('getData'))
            ->getMockForAbstractClass();
        $tokenRequest
            ->expects($this->once())
            ->method('getData')
            ->willReturn(array('data' => true));

        $decoratorTokenRequest = $this->getMockBuilder(AbstractDecoratorTokenRequest::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->setObjectAttribute($decoratorTokenRequest, 'tokenRequest', $tokenRequest);

        //act
        $result = $decoratorTokenRequest->getData();

        //assert
        $this->assertEquals(array('data' => true), $result);
    }

    public function test_getTtl()
    {
        //arrange
        $tokenRequest = $this->getMockBuilder(TokenRequestInterface::class)
            ->setMethods(array('getTtl'))
            ->getMockForAbstractClass();
        $tokenRequest
            ->expects($this->once())
            ->method('getTtl')
            ->willReturn(123);

        $decoratorTokenRequest = $this->getMockBuilder(AbstractDecoratorTokenRequest::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->setObjectAttribute($decoratorTokenRequest, 'tokenRequest', $tokenRequest);

        //act
        $result = $decoratorTokenRequest->getTtl();

        //assert
        $this->assertEquals(123, $result);
    }

}