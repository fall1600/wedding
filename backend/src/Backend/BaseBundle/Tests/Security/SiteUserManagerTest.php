<?php
namespace Backend\BaseBundle\Tests\Security;

use Backend\BaseBundle\Security\SiteUserManager;
use Backend\BaseBundle\Model;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;

/**
 * @group unit
 */
class SiteUserManagerTest extends BaseTestCase
{
    public function test_createToken()
    {
        //arrange
        $firewall = 'test_firewall';
        $expectRoles = array(
            new Role('ROLE0'),
            new Role('ROLE1'),
            new Role('ROLE2'),
        );

        $siteUser = $this->getMockBuilder(Model\SiteUser::class)->setMethods(array('getRoles'))->getMock();
        $siteUser
            ->expects($this->once())
            ->method('getRoles')
            ->willReturn($expectRoles);
        $userManager = new SiteUserManager();

        //act
        $token = $this->callObjectMethod($userManager, 'createToken', $firewall, $siteUser);

        //assert
        $this->assertEquals($expectRoles, $token->getRoles());
    }

    public function test_getEncoder()
    {
        //arrange
        $siteUser = new Model\SiteUser();
        $fakeEncoder = 'fakeEncoder';

        $encoderFactory = $this->getMockBuilder(EncoderFactory::class)
            ->setMethods(array('getEncoder'))
            ->disableOriginalConstructor()
            ->getMock();
        $encoderFactory
            ->expects($this->once())
            ->method('getEncoder')
            ->willReturnCallback(function($user) use($siteUser, $fakeEncoder){
                $this->assertEquals($siteUser, $user);
                return $fakeEncoder;
            });

        $userManager = new SiteUserManager();
        $this->setObjectAttribute($userManager, 'encoderFactory', $encoderFactory);

        //act
        $encoder = $this->callObjectMethod($userManager, 'getEncoder', $siteUser);

        //assert
        $this->assertEquals($fakeEncoder, $encoder);
    }
}