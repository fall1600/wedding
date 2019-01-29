<?php
namespace Backend\BaseBundle\Tests\Security;

use Backend\BaseBundle\Model\SiteUser;
use Backend\BaseBundle\Security\ApiUserProvider;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Backend\BaseBundle\Token\SecretJWTToken;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiUserProviderTest extends BaseTestCase
{
    public function test_ApiUserProvider_is_a_UserProviderInterface()
    {
        //arrange
        $apiUserProvider = $this->getMockBuilder(ApiUserProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        //act

        //assert
        $this->assertInstanceOf(UserProviderInterface::class, $apiUserProvider);
    }

    public function test_loadUserFromArray()
    {
        //arrange
        $index = 0;
        $array = array(
            'id' => 123,
            'roles' => array('role1', 'role2'),
        );
        $propertyAccessor = $this->getMockBuilder(PropertyAccessor::class)
            ->disableOriginalConstructor()
            ->setMethods(array('setValue'))
            ->getMock();
        $propertyAccessor
            ->expects($this->exactly(count($array)))
            ->method('setValue')
            ->willReturnCallback(function($siteUser, $path, $value) use($array, &$index){
                $keys = array_keys($array);
                $this->assertInstanceOf(SiteUser::class, $siteUser);
                $this->assertEquals($keys[$index++], $path);
                $this->assertEquals($array[$path], $value);
            });
        $apiUserProvider = new ApiUserProvider();
        $this->setObjectAttribute($apiUserProvider, 'propertyAccessor', $propertyAccessor);

        //act
        $result = $this->callObjectMethod($apiUserProvider, 'loadUserFromArray', $array);

        //assert
        $this->assertInstanceOf(SiteUser::class, $result);
        $this->assertFalse($result->isNew());
    }

    public function test_injectPropertyAccessor()
    {
        //arrange
        $propertyAccessor = $this->getMockBuilder(PropertyAccessor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $apiUserProvider = new ApiUserProvider();

        //act
        $apiUserProvider->injectPropertyAccessor($propertyAccessor);

        //assert
        $this->assertEquals($propertyAccessor, $this->getObjectAttribute($apiUserProvider, 'propertyAccessor'));
    }

    public function test_injectSecretJWTToken()
    {
        //arrange
        $secretJWTToken = $this->getMockBuilder(SecretJWTToken::class)
            ->disableOriginalConstructor()
            ->getMock();
        $apiUserProvider = new ApiUserProvider();

        //act
        $apiUserProvider->injectSecretJWTToken($secretJWTToken);

        //assert
        $this->assertEquals($secretJWTToken, $this->getObjectAttribute($apiUserProvider, 'secretJWTToken'));
    }

    public function test_loadUserByJWTToken_verify_fail()
    {
        //arrange
        $data = null;
        $jwtToken = 'test_jwtToken';
        $issuer = 'test_issuer';
        $audience = 'test_audience';

        $secretJWTToken = $this->getMockBuilder(SecretJWTToken::class)
            ->disableOriginalConstructor()
            ->setMethods(array('verify'))
            ->getMock();

        $secretJWTToken
            ->expects($this->once())
            ->method('verify')
            ->with($jwtToken, $issuer, $audience)
            ->willReturn($data);

        $apiUserProvider = $this->getMockBuilder(ApiUserProvider::class)
            ->setMethods(array('loadUserFromArray'))
            ->getMock();

        $apiUserProvider
            ->expects($this->never())
            ->method('loadUserFromArray')
            ;
        $this->setObjectAttribute($apiUserProvider, 'secretJWTToken', $secretJWTToken);

        //act
        $result = $apiUserProvider->loadUserByJWTToken($jwtToken, $issuer, $audience);

        //assert
        $this->assertNull($result);
    }


    public function test_loadUserByJWTToken_verify_not_null()
    {
        //arrange
        $data = array();
        $user = new SiteUser();
        $jwtToken = 'test_jwtToken';
        $issuer = 'test_issuer';
        $audience = 'test_audience';

        $secretJWTToken = $this->getMockBuilder(SecretJWTToken::class)
            ->disableOriginalConstructor()
            ->setMethods(array('verify'))
            ->getMock();

        $secretJWTToken
            ->expects($this->once())
            ->method('verify')
            ->with($jwtToken, $issuer, $audience)
            ->willReturn($data);

        $apiUserProvider = $this->getMockBuilder(ApiUserProvider::class)
            ->setMethods(array('loadUserFromArray'))
            ->getMock();

        $apiUserProvider
            ->expects($this->once())
            ->method('loadUserFromArray')
            ->willReturn($user);
        ;
        $this->setObjectAttribute($apiUserProvider, 'secretJWTToken', $secretJWTToken);

        //act
        $result = $apiUserProvider->loadUserByJWTToken($jwtToken, $issuer, $audience);

        //assert
        $this->assertEquals($user, $result);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function test_loadUserByUsername()
    {
        //arrange
        $username = 'test_user';
        $apiUserProvider = new ApiUserProvider();

        //act
        $apiUserProvider->loadUserByUsername($username);
    }

    public function test_refreshUser()
    {
        //arrange
        $user = new SiteUser();
        $apiUserProvider = new ApiUserProvider();

        //act
        $result = $apiUserProvider->refreshUser($user);

        //assert
        $this->assertEquals($user, $result);
    }

    public function test_supportsClass()
    {
        //arrange
        $apiUserProvider = new ApiUserProvider();

        //act
        $result = $apiUserProvider->supportsClass(SiteUser::class);

        //assert
        $this->assertTrue($result);
    }
}