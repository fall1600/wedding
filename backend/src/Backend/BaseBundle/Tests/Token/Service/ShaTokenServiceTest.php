<?php
namespace Backend\BaseBundle\Tests\Token\Service;

use Backend\BaseBundle\Tests\Fixture\BaseKernelTestCase;
use Backend\BaseBundle\Token\Service\ShaTokenService;
use Backend\BaseBundle\Token\Service\Token;
use Backend\BaseBundle\Token\Service\TokenServiceInterface;
use Lcobucci\JWT\Builder;


class ShaTokenServiceTest extends BaseKernelTestCase
{
    public function test_inject()
    {
        //arrange
        $issuer = $this->container->getParameter('token_issuer');
        $secret = $this->container->getParameter('secret');

        //act
        $tokenService = $this->container->get('token_service.sha');

        //assert
        $this->assertEquals($secret, $this->getObjectAttribute($tokenService, 'secret'));
        $this->assertEquals($issuer, $this->getObjectAttribute($tokenService, 'issuer'));
    }

    public function test_is_a()
    {
        //arrange

        //act
        $tokenService = new ShaTokenService();

        //assert
        $this->assertInstanceOf(TokenServiceInterface::class, $tokenService);
    }

    public function test_sign()
    {
        //arrange
        $issuer = 'test_issuer';
        $secret = 'test_secret';
        $expectedResult = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.W10.g0hK5fUTUQpSHq7L6u8osOs90oVRidML1rv6jITMKtE';
        $tokenRequest = $this->getMockBuilder(MockTokenRequest::class)
            ->getMockForAbstractClass();
        $tokenService = $this->getMockBuilder(ShaTokenService::class)
            ->setMethods(array('applyData'))
            ->getMock();
        $tokenService
            ->expects($this->atLeastOnce())
            ->method('applyData')
            ->willReturnCallback(function($issuer, $tokenRequestForTest) use($tokenRequest){
                $this->assertEquals('test_issuer', $issuer);
                $this->assertTrue($tokenRequest === $tokenRequestForTest);
                return new Builder();
            });

        $this->setObjectAttribute($tokenService, 'issuer', $issuer);
        $this->setObjectAttribute($tokenService, 'secret', $secret);

        //act
        $result = $tokenService->sign($tokenRequest);

        //assert
        $this->assertEquals($expectedResult, $result);
    }

    public function test_verify_ok()
    {
        //arrange
        $resultForTest = array(
            "iss" => "test_issuer",
            "exp" => 1496981486,
            "id" => "12345",
            "type" => "test",
            "payload" =>  array(
                "a" => "a",
                "b" => "b"
            ),
            "data" => array(
                "c" => "c",
                "d" => "d"
            )
        );
        $jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0X2lzc3VlciIsImV4cCI6MTQ5Njk4MTQ4NiwiaWQiOiIxMjM0NSIsInR5cGUiOiJ0ZXN0IiwicGF5bG9hZCI6eyJhIjoiYSIsImIiOiJiIn0sImRhdGEiOnsiYyI6ImMiLCJkIjoiZCJ9fQ.2FxCSdhmO5bxzqS4CVe35dooTSsLZeDxTbzt9kyQKFE';
        $issuer = 'test_issuer';
        $time = 1496977886;
        $secret = 'test_secret';
        $tokenService = $this->getMockBuilder(ShaTokenService::class)
            ->setMethods(array('getCurrentTime'))
            ->getMock();
        $tokenService
            ->expects($this->any())
            ->method('getCurrentTime')
            ->willReturn($time);
        $this->setObjectAttribute($tokenService, 'issuer', $issuer);
        $this->setObjectAttribute($tokenService, 'secret', $secret);

        //act
        $result = $this->callObjectMethod($tokenService, 'verify', $jwtToken);;

        //assert
        $this->assertEquals($resultForTest, $result);
    }

    public function test_verify_bad_issuer()
    {
        //arrange
        $resultForTest = array(
            "iss" => "test_issuer",
            "exp" => 1496981486,
            "id" => "12345",
            "type" => "test",
            "payload" =>  array(
                "a" => "a",
                "b" => "b"
            ),
            "data" => array(
                "c" => "c",
                "d" => "d"
            )
        );
        $jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0X2lzc3VlciIsImV4cCI6MTQ5Njk4MTQ4NiwiaWQiOiIxMjM0NSIsInR5cGUiOiJ0ZXN0IiwicGF5bG9hZCI6eyJhIjoiYSIsImIiOiJiIn0sImRhdGEiOnsiYyI6ImMiLCJkIjoiZCJ9fQ.2FxCSdhmO5bxzqS4CVe35dooTSsLZeDxTbzt9kyQKFE';
        $issuer = 'bad_issuer';
        $time = 1496977886;
        $secret = 'test_secret';
        $tokenService = $this->getMockBuilder(ShaTokenService::class)
            ->setMethods(array('getCurrentTime'))
            ->getMock();
        $tokenService
            ->expects($this->any())
            ->method('getCurrentTime')
            ->willReturn($time);

        $this->setObjectAttribute($tokenService, 'issuer', $issuer);
        $this->setObjectAttribute($tokenService, 'secret', $secret);

        //act
        $result = $this->callObjectMethod($tokenService, 'verify', $jwtToken);;

        //assert
        $this->assertFalse($result);
    }

    public function test_verify_expired()
    {
        //arrange
        $resultForTest = array(
            "iss" => "test_issuer",
            "exp" => 1496981486,
            "id" => "12345",
            "type" => "test",
            "payload" =>  array(
                "a" => "a",
                "b" => "b"
            ),
            "data" => array(
                "c" => "c",
                "d" => "d"
            )
        );
        $jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0X2lzc3VlciIsImV4cCI6MTQ5Njk4MTQ4NiwiaWQiOiIxMjM0NSIsInR5cGUiOiJ0ZXN0IiwicGF5bG9hZCI6eyJhIjoiYSIsImIiOiJiIn0sImRhdGEiOnsiYyI6ImMiLCJkIjoiZCJ9fQ.2FxCSdhmO5bxzqS4CVe35dooTSsLZeDxTbzt9kyQKFE';
        $issuer = 'test_issuer';
        $time = 1496981487;
        $secret = 'test_secret';

        $tokenService = $this->getMockBuilder(ShaTokenService::class)
            ->setMethods(array('getCurrentTime'))
            ->getMock();
        $tokenService
            ->expects($this->any())
            ->method('getCurrentTime')
            ->willReturn($time);

        $this->setObjectAttribute($tokenService, 'issuer', $issuer);
        $this->setObjectAttribute($tokenService, 'secret', $secret);

        //act
        $result = $this->callObjectMethod($tokenService, 'verify', $jwtToken);;

        //assert
        $this->assertFalse($result);
    }

    public function test_parse()
    {
        //arrange
        $jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0X2lzc3VlciIsImV4cCI6MTQ5Njk4MTQ4NiwiaWQiOiIxMjM0NSIsInR5cGUiOiJ0ZXN0IiwicGF5bG9hZCI6eyJhIjoiYSIsImIiOiJiIn0sImRhdGEiOnsiYyI6ImMiLCJkIjoiZCJ9fQ.2FxCSdhmO5bxzqS4CVe35dooTSsLZeDxTbzt9kyQKFE';
        $tokenResult = array(
            "iss" => "test_issuer",
            "exp" => 1496981486,
            "id" => "12345",
            "type" => "test",
            "payload" =>  array(
                "a" => "a",
                "b" => "b"
            ),
            "data" => array(
                "c" => "c",
                "d" => "d"
            )
        );
        $tokenService = $this->getMockBuilder(ShaTokenService::class)
            ->setMethods(array('verify'))
            ->getMock();
        $tokenService
            ->expects($this->any())
            ->method('verify')
            ->with($jwtToken)
            ->willReturn($tokenResult);

        //act
        $result = $tokenService->parse($jwtToken);

        //assert
        $this->assertInstanceOf(Token::class, $result);
    }

    public function test_parse_invalid()
    {
        //arrange
        $jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ0ZXN0X2lzc3VlciIsImV4cCI6MTQ5Njk4MTQ4NiwiaWQiOiIxMjM0NSIsInR5cGUiOiJ0ZXN0IiwicGF5bG9hZCI6eyJhIjoiYSIsImIiOiJiIn0sImRhdGEiOnsiYyI6ImMiLCJkIjoiZCJ9fQ.2FxCSdhmO5bxzqS4CVe35dooTSsLZeDxTbzt9kyQKFE';
        $tokenResult = array(
            "iss" => "test_issuer",
            "exp" => 1496981486,
            "id" => "12345",
            "type" => "test",
            "payload" =>  array(
                "a" => "a",
                "b" => "b"
            ),
            "data" => array(
                "c" => "c",
                "d" => "d"
            )
        );
        $tokenService = $this->getMockBuilder(ShaTokenService::class)
            ->setMethods(array('verify'))
            ->getMock();
        $tokenService
            ->expects($this->any())
            ->method('verify')
            ->with($jwtToken)
            ->willReturn(false);

        //act
        $result = $tokenService->parse($jwtToken);

        //assert
        $this->assertNull($result);
    }
}
