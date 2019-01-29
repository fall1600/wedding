<?php
namespace Backend\BaseBundle\Tests\Model;

use Backend\BaseBundle\Model\Site;
use Backend\BaseBundle\Model\SiteGroup;
use Backend\BaseBundle\Model\SiteUser;
use Backend\BaseBundle\Security\SecurityEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;

/**
 * @group unit
 */
class SiteUserTest extends BaseTestCase
{
    public function test_is_a_SecurityEncoderInterface()
    {
        //arrange
        //act
        $user = new SiteUser();

        //assert
        $this->assertInstanceOf(SecurityEncoderInterface::class, $user);
    }

    public function test_is_a_UserInterface()
    {
        //arrange
        //act
        $user = new SiteUser();

        //assert
        $this->assertInstanceOf(UserInterface::class, $user);
    }

    public function test_getUserName()
    {
        $mokeUser = $this->getMockBuilder(SiteUser::class)
            ->setMethods(array('getLoginName'))
            ->getMock();
        $mokeUser
            ->expects($this->once())
            ->method('getLoginName')
            ->willReturn('test_value');
        $this->assertEquals('test_value', $mokeUser->getUserName());
    }

    public function test_eraseCredentials()
    {
        $user = new SiteUser();
        $user->setPassword('password');
        $user->setSalt('salt');
        $user->setPlainPassword('plain_password');

        $user->eraseCredentials();

        $this->assertNull($user->getPassword());
        $this->assertNull($user->getSalt());
        $this->assertNull($user->getPlainPassword());
    }

    public function test_setPlainPassword()
    {
        $user = new SiteUser();
        $user->setPlainPassword('plain_password');
        $this->assertEquals('plain_password', $this->getObjectAttribute($user, 'plainPassword'));
    }

    public function test_getPlainPassword()
    {
        $user = new SiteUser();
        $user->setPlainPassword('plain_password');
        $this->assertEquals('plain_password', $user->getPlainPassword());
    }

    public function test_getRoles()
    {
        //arrange
        $defaultRoles = array('DEFAULT1', 'DEFAULT2');
        $customRoles = array('CUSTOM1', 'CUSTOM2');
        $group1Roles = array('GROUP1', 'CUSTOM1');
        $group2Roles = array('GROUP2', 'CUSTOM2');
        $expectResult = array('CUSTOM1', 'CUSTOM2', 'DEFAULT1', 'DEFAULT2', 'GROUP1', 'GROUP2');

        $group1 = $this->getMockBuilder(SiteGroup::class)->setMethods(array('getRoles'))->getMock();
        $group1->expects($this->once())
            ->method('getRoles')
            ->willReturn($group1Roles);
        $group2 = $this->getMockBuilder(SiteGroup::class)->setMethods(array('getRoles'))->getMock();
        $group2->expects($this->once())
            ->method('getRoles')
            ->willReturn($group2Roles);
        $user = $this->getMockBuilder(SiteUser::class)
            ->setMethods(array('getDefaultRoles', 'getCustomRoles', 'getSiteGroups'))
            ->getMock();
        $user->expects($this->once())
            ->method('getDefaultRoles')
            ->willReturn($defaultRoles);
        $user->expects($this->once())
            ->method('getCustomRoles')
            ->willReturn($customRoles);
        $user->expects($this->once())
            ->method('getSiteGroups')
            ->willReturn(new \PropelCollection(array($group1, $group2)));

        //act
        $result = $user->getRoles();
        sort($result);

        //assert
        $this->assertEquals($expectResult, $result);
    }

    public function test_getRoles_is_cached()
    {
        //arrange
        $roles = array('test', 'test2');
        $user =  new SiteUser();
        $this->setObjectAttribute($user, 'rolesCache', $roles);

        //act
        $result = $user->getRoles();

        //assert
        $this->assertEquals($roles, $result);
    }

    public function test_reload_clear_roles()
    {
        //arrange
        $roles = array('test', 'test2');
        $user =  new SiteUser();
        $this->setObjectAttribute($user, 'rolesCache', $roles);

        //act
        try {
            $user->reload();
        } catch (\PropelException $e){

        }

        //assert
        $this->assertNull($this->getObjectAttribute($user, 'rolesCache'));
    }

    public function test_setRoles()
    {
        //arrange
        $roles = array('test', 'test2');
        $user =  new SiteUser();

        //act
        $user->setRoles($roles);

        //assert
        $this->assertEquals($roles, $this->getObjectAttribute($user, 'rolesCache'));
    }

    public function test_regenerateSalt()
    {
        //arrange
        $user = new SiteUser();

        //act
        $user->regenerateSalt();

        //assert
        $this->assertNotNull($user->getSalt());
    }

    public function test_generateToken()
    {
        //arrange
        $user = new SiteUser();

        //act
        $user->generateToken();

        //aeeert
        $this->assertNotNull($user->getConfirmToken());
        $this->assertGreaterThan(time()+29*60, $user->getTokenExpiredAt('U'));
    }

}