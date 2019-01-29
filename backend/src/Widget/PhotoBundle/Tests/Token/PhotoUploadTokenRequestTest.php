<?php
namespace Widget\PhotoBundle\Tests\Token;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Backend\BaseBundle\Token\Service\TokenRequest\TokenRequestInterface;
use Widget\PhotoBundle\Token\PhotoUploadTokenRequest;

abstract class MockTokenRequestInterfaceClass implements TokenRequestInterface
{

}

class PhotoUploadTokenRequestTest extends BaseTestCase
{
    public function test_is_a()
    {
        //arrange
        $tokenRequest = $this->getMockBuilder(MockTokenRequestInterfaceClass::class)
            ->getMockForAbstractClass();

        //act
        $photoUploadTokenRequest = new PhotoUploadTokenRequest($tokenRequest);

        //assert
        $this->assertInstanceOf(TokenRequestInterface::class, $photoUploadTokenRequest);
    }

    public function test_getId()
    {
        //arrange
        $tokenRequest = $this->getMockBuilder(MockTokenRequestInterfaceClass::class)
            ->setMethods(array('getId'))
            ->getMockForAbstractClass();
        $tokenRequest
            ->expects($this->any())
            ->method('getId')
            ->willReturn(12345);
        $photoUploadTokenRequest = new PhotoUploadTokenRequest($tokenRequest);

        //act
        $result = $photoUploadTokenRequest->getId();

        //assert
        $this->assertEquals('12345', $result);
    }

    public function test_getType()
    {
        //arrange
        $tokenRequest = $this->getMockBuilder(MockTokenRequestInterfaceClass::class)
            ->setMethods(array('getType'))
            ->getMockForAbstractClass();
        $tokenRequest
            ->expects($this->any())
            ->method('getType')
            ->willReturn(12345);
        $photoUploadTokenRequest = new PhotoUploadTokenRequest($tokenRequest);

        //act
        $result = $photoUploadTokenRequest->getType();

        //assert
        $this->assertEquals('12345', $result);
    }

    public function test_getPayload()
    {
        //arrange
        $tokenRequest = $this->getMockBuilder(MockTokenRequestInterfaceClass::class)
            ->setMethods(array('getPayload'))
            ->getMockForAbstractClass();
        $tokenRequest
            ->expects($this->any())
            ->method('getPayload')
            ->willReturn(array(0 , 1, 2, 3));
        $photoUploadTokenRequest = new PhotoUploadTokenRequest($tokenRequest);

        //act
        $result = $photoUploadTokenRequest->getPayload();

        //assert
        $this->assertEquals(array(0 , 1, 2, 3), $result);
    }

    public function test_getData()
    {
        //arrange
        $tokenRequest = $this->getMockBuilder(MockTokenRequestInterfaceClass::class)
            ->setMethods(array('getData'))
            ->getMockForAbstractClass();
        $tokenRequest
            ->expects($this->any())
            ->method('getData')
            ->willReturn(array('a' => 'a', 'b' => 'b'));
        $photoUploadTokenRequest = new PhotoUploadTokenRequest($tokenRequest);

        //act
        $result = $photoUploadTokenRequest->getData();

        //assert
        $this->assertEquals(array('a' => 'a', 'b' => 'b', 'photo' => true), $result);
    }

    public function test_getTtl()
    {
        //arrange
        $tokenRequest = $this->getMockBuilder(MockTokenRequestInterfaceClass::class)
            ->setMethods(array('getTtl'))
            ->getMockForAbstractClass();
        $tokenRequest
            ->expects($this->any())
            ->method('getTtl')
            ->willReturn(12345);
        $photoUploadTokenRequest = new PhotoUploadTokenRequest($tokenRequest);

        //act
        $result = $photoUploadTokenRequest->getTtl();

        //assert
        $this->assertEquals('12345', $result);
    }
}