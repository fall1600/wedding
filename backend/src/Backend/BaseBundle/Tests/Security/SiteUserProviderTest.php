<?php
namespace Backend\BaseBundle\Tests\Security;

use Backend\BaseBundle\Security\SiteUserProvider;
use Backend\BaseBundle\Model;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;

/**
 * @group unit
 */
class SiteUserProviderTest extends BaseTestCase
{
    public function test_is_a()
    {
        $userProvider = new SiteUserProvider();
        $this->assertInstanceOf(UserProviderInterface::class, $userProvider);
    }

    public function test_loadUserByUsernameOrEmail_username_find()
    {
        $userProvider = $this->getMockBuilder(SiteUserProvider::class)
            ->setMethods(array(
                'loadUserByLoginName',
                'loadUserByEmail',
            ))
            ->getMock();

        $siteUser = new Model\SiteUser();
        $userProvider
            ->expects($this->once())
            ->method('loadUserByLoginName')
            ->willReturn($siteUser);

        $userProvider
            ->expects($this->never())
            ->method('loadUserByEmail')
            ->willReturn($siteUser);

        $this->assertEquals($siteUser, $userProvider->loadUserByUsernameOrEmail('test'));
    }

    public function test_loadUserByUsernameOrEmail_email_find()
    {
        $userProvider = $this->getMockBuilder(SiteUserProvider::class)
            ->setMethods(array(
                'loadUserByLoginName',
                'loadUserByEmail',
            ))
            ->getMock();

        $siteUser = new Model\SiteUser();
        $userProvider
            ->expects($this->once())
            ->method('loadUserByLoginName')
            ->willReturn(null);

        $userProvider
            ->expects($this->once())
            ->method('loadUserByEmail')
            ->willReturn($siteUser);

        $this->assertEquals($siteUser, $userProvider->loadUserByUsernameOrEmail('test'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_refreshUser_ivalid()
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $userProvider = new SiteUserProvider();
        $userProvider->refreshUser($user);
    }

    public function test_refreshUser()
    {
        $user = $this->getMockBuilder(Model\SiteUser::class)->setMethods(array('reload'))->getMock();
        $user->expects($this->once())
            ->method('reload')
            ->willReturn(null);
        $userProvider = new SiteUserProvider();
        $userProvider->refreshUser($user);
    }

    public function test_supportsClass()
    {
        $userProvider = new SiteUserProvider();
        $this->assertTrue($userProvider->supportsClass(Model\SiteUser::class));
        $this->assertFalse($userProvider->supportsClass(\stdClass::class));
    }

}