<?php
namespace Backend\BaseBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class TypeRolesEvent extends Event
{
    const EVENT_TYPE_ROLES = 'event.controller.type.roles';

    protected $typeRoles = array();

    public function addTypeRoles($type, $roles)
    {
        if(!isset($this->typeRoles[$type])) {
            $this->typeRoles[$type] = array_combine($roles, $roles);
        }
        else{
            $this->typeRoles[$type] = array_merge($this->typeRoles[$type], array_combine($roles, $roles));
        }
    }

    public function getTypeRoles()
    {
        return $this->typeRoles;
    }

    public function getRoles()
    {
        $roles = array();
        $typeRoles = $this->getTypeRoles();
        array_walk_recursive($typeRoles, function($role, $type) use(&$roles){
            $roles[] = $role;
        });
        return $roles;
    }

}