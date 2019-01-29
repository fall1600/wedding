<?php
namespace Backend\BaseBundle\Tests\Model;

use Backend\BaseBundle\Model\SiteGroup;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;

/**
 * @group unit
 */
class SiteGroupTest extends BaseTestCase
{
    public function test_getRoles()
    {
        //arrange
        $defaultRoles = array('DEFAULT1', 'DEFAULT2');
        $customRoles = array('CUSTOM1', 'CUSTOM2');
        $group = $this->getMockBuilder(SiteGroup::class)
            ->setMethods(array('getDefaultRoles', 'getCustomRoles'))
            ->getMock();
        $group->expects($this->once())
            ->method('getDefaultRoles')
            ->willReturn($defaultRoles);
        $group->expects($this->once())
            ->method('getCustomRoles')
            ->willReturn($customRoles);

        //act
        $result = $group->getRoles();

        //assert
        $this->assertEquals(array_merge($defaultRoles, $customRoles), $result);
    }
}