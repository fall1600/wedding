<?php
namespace Backend\BaseBundle\EventListener\Roles;

use Backend\BaseBundle\Event\TypeRolesEvent;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * 監聽或做任何事件時，需先將本身listener註冊為服務
 * @DI\Service()
 */
class SiteListener
{
    /**
     * 讓帳號管理介面加入權限設定
     * @DI\Observe(TypeRolesEvent::EVENT_TYPE_ROLES)
     */
    public function onTypeRoles(TypeRolesEvent $event)
    {
        $event->addTypeRoles('ROLE_SUPERADMIN', array(
            'ROLE_SUPERADMIN',
        ));
    }
}