<?php
namespace Widget\PhotoBundle\EventListener\Roles;

use Backend\BaseBundle\Event\TypeRolesEvent;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * 監聽或做任何事件時，需先將本身listener註冊為服務
 * @DI\Service()
 */
class PhotoConfigListener
{
    /**
     * 讓帳號管理介面加入權限設定
     * @DI\Observe(TypeRolesEvent::EVENT_TYPE_ROLES)
     */
    public function onTypeRoles(TypeRolesEvent $event)
    {
        $event->addTypeRoles('ROLE_PHOTO_CONFIG', array(
            'ROLE_PHOTO_CONFIG_READ',
            'ROLE_PHOTO_CONFIG_WRITE',
            'ROLE_PHOTO_WRITE',
            'ROLE_PHOTO_READ'
        ));
    }
}