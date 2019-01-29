<?php
namespace Backend\BaseBundle\Tests\Token;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Backend\BaseBundle\Token\SecretJWTToken;
use Symfony\Component\HttpFoundation\Request;

class SecretJWTTokenTest extends BaseTestCase
{
    public function test_injectSecret()
    {
        //arrange
        $secret = 'test_secret';
        $secretJWTToken = new SecretJWTToken();

        //act
        $secretJWTToken->injectSecret($secret);

        //assert
        $this->assertEquals(SecretJWTToken::SECRET_PREFIX.$secret, $this->getObjectAttribute($secretJWTToken, 'secret'));
    }

    public function test_sign()
    {
        //arrange
        $secret = 'test_secret';
        $issuer = 'test_issuer';
        $audience = 'test_audience';
        $time = 1474862580;

        $data = array(
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
            'd' => array(0, 1, 2, 3, 4,),
        );
        $secretJWTToken = $this->getMockBuilder(SecretJWTToken::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getCurrentTime'))
            ->getMock();
        $secretJWTToken
            ->expects($this->once())
            ->method('getCurrentTime')
            ->willReturn($time);

        $this->setObjectAttribute($secretJWTToken, 'secret', $secret);

        //act
        $token = $secretJWTToken->sign($issuer, $audience, $data);

        //assert
        $this->assertInternalType('string', $token);
        list($header, $payload, $verify) = explode('.', $token);
        $payloadArray = json_decode(base64_decode($payload), true);
        $this->assertArrayHasKey('iat', $payloadArray);
        $this->assertArrayHasKey('nbf', $payloadArray);
        $this->assertArrayHasKey('exp', $payloadArray);
        $this->assertEquals($time, $payloadArray['nbf']);
        $this->assertEquals($time, $payloadArray['iat']);
        $this->assertEquals($time + 3600, $payloadArray['exp']);
        $this->assertEquals($issuer, $payloadArray['iss']);
        $this->assertEquals($audience, $payloadArray['aud']);
        $this->assertEquals($data, $payloadArray['data']);
    }

    public function test_verify_all_valid()
    {
        //arrange
        $secret = 'test_secret';
        $issuer = 'test_issuer';
        $audience = 'test_audience';
        $data = array(
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
            'd' => array(0, 1, 2, 3, 4,),
        );
        $time = 1474862580;
        $jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE0NzQ4NjI1ODAsIm5iZiI6MTQ3NDg2MjU4MCwiaXNzIjoidGVzdF9pc3N1ZXIiLCJhdWQiOiJ0ZXN0X2F1ZGllbmNlIiwiZXhwIjoxNDc0ODY2MTgwLCJkYXRhIjp7ImEiOiJhIiwiYiI6ImIiLCJjIjoiYyIsImQiOlswLDEsMiwzLDRdfX0.9pvVOZlc4HzRCbLxQq35Zmig1tFvUOIa_tdARGkVa88';
        $secretJWTToken = $this->getMockBuilder(SecretJWTToken::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getCurrentTime'))
            ->getMock();
        $secretJWTToken
            ->expects($this->once())
            ->method('getCurrentTime')
            ->willReturn($time);
        $this->setObjectAttribute($secretJWTToken, 'secret', $secret);

        //act
        $result = $secretJWTToken->verify($jwtToken, $issuer, $audience);

        //assert
        $this->assertEquals($data, $result);
    }

    public function test_verify_bad_token()
    {
        //arrange
        $secret = 'test_secret';
        $issuer = 'test_issuer';
        $audience = 'test_audience';
        $data = array(
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
            'd' => array(0, 1, 2, 3, 4,),
        );
        $time = 1474862580;
        $jwtToken = 'shdflkjahslkdjhl.asas.as231df';
        $secretJWTToken = $this->getMockBuilder(SecretJWTToken::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getCurrentTime'))
            ->getMock();
        $secretJWTToken
            ->expects($this->once())
            ->method('getCurrentTime')
            ->willReturn($time + 86400);
        $this->setObjectAttribute($secretJWTToken, 'secret', $secret);

        //act
        $result = $secretJWTToken->verify($jwtToken, $issuer, $audience);

        //assert
        $this->assertNull($result);
    }

    public function test_verify_expired()
    {
        //arrange
        $secret = 'test_secret';
        $issuer = 'test_issuer';
        $audience = 'test_audience';
        $data = array(
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
            'd' => array(0, 1, 2, 3, 4,),
        );
        $time = 1474862580;
        $jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE0NzQ4NjI1ODAsIm5iZiI6MTQ3NDg2MjU4MCwiaXNzIjoidGVzdF9pc3N1ZXIiLCJhdWQiOiJ0ZXN0X2F1ZGllbmNlIiwiZXhwIjoxNDc0ODY2MTgwLCJkYXRhIjp7ImEiOiJhIiwiYiI6ImIiLCJjIjoiYyIsImQiOlswLDEsMiwzLDRdfX0.9pvVOZlc4HzRCbLxQq35Zmig1tFvUOIa_tdARGkVa88';
        $secretJWTToken = $this->getMockBuilder(SecretJWTToken::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getCurrentTime'))
            ->getMock();
        $secretJWTToken
            ->expects($this->once())
            ->method('getCurrentTime')
            ->willReturn($time + 86400);
        $this->setObjectAttribute($secretJWTToken, 'secret', $secret);

        //act
        $result = $secretJWTToken->verify($jwtToken, $issuer, $audience);

        //assert
        $this->assertNull($result);
    }

    public function test_verify_bad_issuer()
    {
        //arrange
        $secret = 'test_secret_bad';
        $issuer = 'test_issuer';
        $audience = 'test_audience';
        $data = array(
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
            'd' => array(0, 1, 2, 3, 4,),
        );
        $time = 1474862580;
        $jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE0NzQ4NjI1ODAsIm5iZiI6MTQ3NDg2MjU4MCwiaXNzIjoidGVzdF9pc3N1ZXIiLCJhdWQiOiJ0ZXN0X2F1ZGllbmNlIiwiZXhwIjoxNDc0ODY2MTgwLCJkYXRhIjp7ImEiOiJhIiwiYiI6ImIiLCJjIjoiYyIsImQiOlswLDEsMiwzLDRdfX0.9pvVOZlc4HzRCbLxQq35Zmig1tFvUOIa_tdARGkVa88';
        $secretJWTToken = $this->getMockBuilder(SecretJWTToken::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getCurrentTime'))
            ->getMock();
        $secretJWTToken
            ->expects($this->once())
            ->method('getCurrentTime')
            ->willReturn($time);
        $this->setObjectAttribute($secretJWTToken, 'secret', $secret);

        //act
        $result = $secretJWTToken->verify($jwtToken, $issuer, $audience);

        //assert
        $this->assertNull($result);
    }

    public function test_verify_bad_audience()
    {
        //arrange
        $secret = 'test_secret_bad';
        $issuer = 'test_issuer';
        $audience = 'test_audience';
        $data = array(
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
            'd' => array(0, 1, 2, 3, 4,),
        );
        $time = 1474862580;
        $jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE0NzQ4NjI1ODAsIm5iZiI6MTQ3NDg2MjU4MCwiaXNzIjoidGVzdF9pc3N1ZXIiLCJhdWQiOiJ0ZXN0X2F1ZGllbmNlIiwiZXhwIjoxNDc0ODY2MTgwLCJkYXRhIjp7ImEiOiJhIiwiYiI6ImIiLCJjIjoiYyIsImQiOlswLDEsMiwzLDRdfX0.9pvVOZlc4HzRCbLxQq35Zmig1tFvUOIa_tdARGkVa88';
        $secretJWTToken = $this->getMockBuilder(SecretJWTToken::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getCurrentTime'))
            ->getMock();
        $secretJWTToken
            ->expects($this->once())
            ->method('getCurrentTime')
            ->willReturn($time);
        $this->setObjectAttribute($secretJWTToken, 'secret', $secret);

        //act
        $result = $secretJWTToken->verify($jwtToken, $issuer, $audience);

        //assert
        $this->assertNull($result);
    }

    public function test_verify_bad_nbf()
    {
        //arrange
        $secret = 'test_secret_bad';
        $issuer = 'test_issuer';
        $audience = 'test_audience';
        $data = array(
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
            'd' => array(0, 1, 2, 3, 4,),
        );
        $time = 1474862580;
        $jwtToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE0NzQ4NjI1ODAsIm5iZiI6MTQ3NDg2MjU4MCwiaXNzIjoidGVzdF9pc3N1ZXIiLCJhdWQiOiJ0ZXN0X2F1ZGllbmNlIiwiZXhwIjoxNDc0ODY2MTgwLCJkYXRhIjp7ImEiOiJhIiwiYiI6ImIiLCJjIjoiYyIsImQiOlswLDEsMiwzLDRdfX0.9pvVOZlc4HzRCbLxQq35Zmig1tFvUOIa_tdARGkVa88';
        $secretJWTToken = $this->getMockBuilder(SecretJWTToken::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getCurrentTime'))
            ->getMock();
        $secretJWTToken
            ->expects($this->once())
            ->method('getCurrentTime')
            ->willReturn($time - 86400);
        $this->setObjectAttribute($secretJWTToken, 'secret', $secret);

        //act
        $result = $secretJWTToken->verify($jwtToken, $issuer, $audience);

        //assert
        $this->assertNull($result);
    }

    public function test_createJWTToken_null_origin()
    {
        //arrange
        $token = 'test.ssecret.token';
        $secretJWTToken = $this->getMockBuilder(SecretJWTToken::class)
            ->disableOriginalConstructor()
            ->setMethods(array('sign'))
            ->getMock();
        $issure = 'test_issure';
        $audience = 'test_audience';
        $data = array(0, 1, 2, 3, 4, 5);
        $secretJWTToken
            ->expects($this->once())
            ->method('sign')
            ->with($issure, $issure, $data)
            ->willReturn($token);
        $request = $this->getMockBuilder(Request::class)
            ->setMethods(array('getHttpHost'))
            ->getMock();
        $request
            ->expects($this->once())
            ->method('getHttpHost')
            ->willReturn($issure);

        //act
        $result = $secretJWTToken->createJWTToken($request, $data);

        //assert
        $this->assertEquals($token, $result);
    }

    public function test_createJWTToken()
    {
        //arrange
        $token = 'test.ssecret.token';
        $secretJWTToken = $this->getMockBuilder(SecretJWTToken::class)
            ->disableOriginalConstructor()
            ->setMethods(array('sign'))
            ->getMock();
        $issure = 'test_issure';
        $audience = 'test_audience';
        $data = array(0, 1, 2, 3, 4, 5);
        $secretJWTToken
            ->expects($this->once())
            ->method('sign')
            ->with($issure, $audience, $data)
            ->willReturn($token);
        $request = $this->getMockBuilder(Request::class)
            ->setMethods(array('getHttpHost'))
            ->getMock();
        $request
            ->expects($this->once())
            ->method('getHttpHost')
            ->willReturn($issure);
        $request->headers->set('Origin', $audience);
        //act
        $result = $secretJWTToken->createJWTToken($request, $data);

        //assert
        $this->assertEquals($token, $result);
    }

}