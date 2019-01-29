<?php
namespace Backend\BaseBundle\Tests\Token\Service;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Backend\BaseBundle\Token\Service\AbstractTokenService;

class AbstractTokenServiceTest extends BaseTestCase
{
    public function test_applyData()
    {
        //arrange
        $id = '12345';
        $type = 'test';
        $payload = array('a' => 'a', 'b' => 'b');
        $data = array('c' => 'c', 'd' => 'd');
        $ttl = 3600;
        $expectedResult = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIn0.eyJpc3MiOiJ0ZXN0X2lzc3VlciIsImV4cCI6MTQ5Njk4MTQ4NiwiaWF0IjoxNDk2OTc3ODg2LCJpZCI6IjEyMzQ1IiwidHlwZSI6InRlc3QiLCJkYXRhIjp7ImMiOiJjIiwiZCI6ImQifX0.';
        $issuer = 'test_issuer';
        $time = '1496977886';
        $tokenRequest = $this->getMockBuilder(MockTokenRequest::class)
            ->setMethods(array('getId', 'getType', 'getData', 'getTtl'))
            ->getMockForAbstractClass();
        $tokenRequest
            ->expects($this->once())
            ->method('getId')
            ->willReturn($id);
        $tokenRequest
            ->expects($this->once())
            ->method('getType')
            ->willReturn($type);
        $tokenRequest
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);
        $tokenRequest
            ->expects($this->once())
            ->method('getTtl')
            ->willReturn($ttl);
        $tokenService = $this->getMockBuilder(AbstractTokenService::class)
            ->setMethods(array('getCurrentTime'))
            ->getMockForAbstractClass();

        $tokenService
            ->expects($this->atLeastOnce())
            ->method('getCurrentTime')
            ->willReturn($time);

        //act
        $builder = $this->callObjectMethod($tokenService, 'applyData', $issuer, $tokenRequest);

        //assert
        $this->assertEquals($expectedResult, (string)$builder->getToken());
    }
}