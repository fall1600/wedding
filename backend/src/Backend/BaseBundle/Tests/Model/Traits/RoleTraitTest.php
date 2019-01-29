<?php
namespace Backend\BaseBundle\Tests\Model\Traits;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Backend\BaseBundle\Model\Traits\RoleTrait;

/**
 * @group unit
 */
class RoleTraitTest extends BaseTestCase
{
    public function test_addCustomRole_if_has_custom_role()
    {
        //arrange
        $roleTrait = $this->getMockBuilder(RoleTrait::class)
            ->setMethods(array('hasCustomRole', 'getCustomRoles'))
            ->getMockForTrait();
        $roleTrait->expects($this->once())
            ->method('hasCustomRole')
            ->willReturn(true);
        $roleTrait->expects($this->never())
            ->method('getCustomRoles')
            ->willReturn(null);
        //act
        $result = $roleTrait->addCustomRole('SOME_ROLE');
        //assert
        $this->assertEquals($roleTrait, $result);
    }

    public function test_addCustomRole_if_has_no_custom_role()
    {
        //arrange
        $role_for_add = 'CUSTOM_ROLE0';
        $roles = array('CUSTOM_ROLE1', 'CUSTOM_ROLE2');
        $setRoles = array('CUSTOM_ROLE1', 'CUSTOM_ROLE2', 'CUSTOM_ROLE0');
        $roleTrait = $this->getMockBuilder(RoleTrait::class)
            ->setMethods(array('hasCustomRole', 'getCustomRoles', 'setCustomRoles'))
            ->getMockForTrait();
        $roleTrait->expects($this->once())
            ->method('hasCustomRole')
            ->willReturnCallback(function($role) use($role_for_add){
                $this->assertEquals($role_for_add, $role);
                return false;
            });
        $roleTrait->expects($this->once())
            ->method('getCustomRoles')
            ->willReturn($roles);
        $roleTrait->expects($this->once())
            ->method('setCustomRoles')
            ->willReturnCallback(function($roles) use($setRoles, $roleTrait){
                $this->assertEquals($setRoles, $roles);
                return $roleTrait;
            });
        //act
        $result = $roleTrait->addCustomRole($role_for_add);
        //assert
        $this->assertEquals($roleTrait, $result);
    }

    public function test_addDefaultRole_if_has_custom_role()
    {
        //arrange
        $roleTrait = $this->getMockBuilder(RoleTrait::class)
            ->setMethods(array('hasDefaultRole', 'getDefaultRoles'))
            ->getMockForTrait();
        $roleTrait->expects($this->once())
            ->method('hasDefaultRole')
            ->willReturn(true);
        $roleTrait->expects($this->never())
            ->method('getDefaultRoles')
            ->willReturn(null);
        //act
        $result = $roleTrait->addDefaultRole('SOME_ROLE');
        //assert
        $this->assertEquals($roleTrait, $result);
    }

    public function test_addDefaultRole_if_has_no_custom_role()
    {
        //arrange
        $role_for_add = 'DEFAULT_ROLE0';
        $roles = array('DEFAULT_ROLE1', 'DEFAULT_ROLE2');
        $setRoles = array('DEFAULT_ROLE1', 'DEFAULT_ROLE2', 'DEFAULT_ROLE0');
        $roleTrait = $this->getMockBuilder(RoleTrait::class)
            ->setMethods(array('hasDefaultRole', 'getDefaultRoles', 'setDefaultRoles'))
            ->getMockForTrait();
        $roleTrait->expects($this->once())
            ->method('hasDefaultRole')
            ->willReturnCallback(function($role) use($role_for_add){
                $this->assertEquals($role_for_add, $role);
                return false;
            });
        $roleTrait->expects($this->once())
            ->method('getDefaultRoles')
            ->willReturn($roles);
        $roleTrait->expects($this->once())
            ->method('setDefaultRoles')
            ->willReturnCallback(function($roles) use($setRoles, $roleTrait){
                $this->assertEquals($setRoles, $roles);
                return $roleTrait;
            });
        //act
        $result = $roleTrait->addDefaultRole($role_for_add);
        //assert
        $this->assertEquals($roleTrait, $result);
    }
}