<?php
namespace Widget\CategoryBundle\EventListener\Roles;

use Backend\BaseBundle\Event\TypeRolesEvent;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * 監聽或做任何事件時，需先將本身listener註冊為服務
 * @DI\Service()
 */
class CategoryListener
{
    /**
     * 讓帳號管理介面加入權限設定
     * @DI\Observe(TypeRolesEvent::EVENT_TYPE_ROLES)
     */
    public function onTypeRoles(TypeRolesEvent $event)
    {
        $event->addTypeRoles('ROLE_CATEGORY', array(
            'ROLE_CATEGORY_READ',
            'ROLE_CATEGORY_WRITE',
        ));
    }
}