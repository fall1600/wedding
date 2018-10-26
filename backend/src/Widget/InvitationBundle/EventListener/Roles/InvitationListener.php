<?php

namespace Widget\InvitationBundle\EventListener\Roles;

use Backend\BaseBundle\Event\TypeRolesEvent;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service()
 */
class InvitationListener
{
    /**
     * @DI\Observe(TypeRolesEvent::EVENT_TYPE_ROLES)
     */
    public function onTypeRoles(TypeRolesEvent $event)
    {
        $event->addTypeRoles('ROLE_INVITATION', array(
            'ROLE_INVITATION_READ',
            'ROLE_INVITATION_WRITE'
        ));
    }
}
