<?php
namespace Backend\BaseBundle\Tests\Security;

use Backend\BaseBundle\Model\SiteUser;
use Backend\BaseBundle\Security\ApiKeyAuthenticator;
use Backend\BaseBundle\Security\ApiUserProvider;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class ApiKeyAuthenticatorTest extends BaseTestCase
{
    public function test_is_a_SimplePreAuthenticatorInterface()
    {
        //arrange
        $apiKeyAuthenticator = new ApiKeyAuthenticator();
        //act
        //assert
        $this->assertInstanceOf(SimplePreAuthenticatorInterface::class, $apiKeyAuthenticator);
    }


    public function test_is_a_AuthenticationFailureHandlerInterface()
    {
        //arrange
        $apiKeyAuthenticator = new ApiKeyAuthenticator();

        //act

        //assert
        $this->assertInstanceOf(AuthenticationFailureHandlerInterface::class, $apiKeyAuthenticator);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function test_createToken_request_with_no_Authorization_header()
    {
        //arrange
        $request = new Request();
        $apiKeyAuthenticator = new ApiKeyAuthenticator();
        $providerKey = 'test_provider';

        //act
        $result = $apiKeyAuthenticator->createToken($request, $providerKey);

        //assert
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function test_createToken_request_with_no_bearer_token()
    {
        //arrange
        $request = new Request();
        $apiKeyAuthenticator = new ApiKeyAuthenticator();
        $providerKey = 'test_provider';
        $request->headers->set('Authorization', 'bad_token');

        //act
        $result = $apiKeyAuthenticator->createToken($request, $providerKey);

        //assert
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function test_createToken_request_apiUserPdovider_user_not_found()
    {
        //arrange
        $httpHost = 'test_httphost';
        $origin = 'test_origin';
        $token = 'tets_token';
        $providerKey = 'test_provider';
        $request = $this->getMockBuilder(Request::class)
            ->setMethods(array('getHttpHost'))
            ->getMock();
        $request
            ->expects($this->once())
            ->method('getHttpHost')
            ->willReturn($httpHost);
        $request->headers->set('Authorization', "Bearer $token");
        $request->headers->set('Origin', $origin);

        $apiUserProvider = $this->getMockBuilder(ApiUserProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(array('loadUserByJWTToken'))
            ->getMock();

        $apiUserProvider
            ->expects($this->once())
            ->method('loadUserByJWTToken')
            ->with($token, $httpHost, $origin)
            ->willThrowException(new BadCredentialsException());

        $apiKeyAuthenticator = new ApiKeyAuthenticator();
        $this->setObjectAttribute($apiKeyAuthenticator, 'apiUserProvider', $apiUserProvider);
        //act
        $result = $apiKeyAuthenticator->createToken($request, $providerKey);

        //assert
    }

    public function test_createToken_request_apiUserPdovider_user_found()
    {
        //arrange
        $httpHost = 'test_httphost';
        $origin = 'test_origin';
        $token = 'tets_token';
        $providerKey = 'test_provider';
        $user = new SiteUser();
        $request = $this->getMockBuilder(Request::class)
            ->setMethods(array('getHttpHost'))
            ->getMock();
        $request
            ->expects($this->once())
            ->method('getHttpHost')
            ->willReturn($httpHost);
        $request->headers->set('Authorization', "Bearer $token");
        $request->headers->set('Origin', $origin);

        $apiUserProvider = $this->getMockBuilder(ApiUserProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(array('loadUserByJWTToken'))
            ->getMock();

        $apiUserProvider
            ->expects($this->once())
            ->method('loadUserByJWTToken')
            ->with($token, $httpHost, $origin)
            ->willReturn($user);

        $apiKeyAuthenticator = new ApiKeyAuthenticator();
        $this->setObjectAttribute($apiKeyAuthenticator, 'apiUserProvider', $apiUserProvider);
        //act
        $result = $apiKeyAuthenticator->createToken($request, $providerKey);

        //assert
        $this->assertInstanceOf(PreAuthenticatedToken::class, $result);
    }
}
