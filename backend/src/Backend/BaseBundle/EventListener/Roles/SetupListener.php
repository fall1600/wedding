<?php

namespace Backend\BaseBundle\EventListener\Roles;

use Backend\BaseBundle\Event\TypeRolesEvent;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service()
 */
class SetupListener
{
    /**
     * 讓帳號管理介面加入權限設定
     * @DI\Observe(TypeRolesEvent::EVENT_TYPE_ROLES)
     */
    public function onTypeRoles(TypeRolesEvent $event)
    {
        $event->addTypeRoles('ROLE_SETUP', array(
            'ROLE_SETUP'
        ));
    }
}