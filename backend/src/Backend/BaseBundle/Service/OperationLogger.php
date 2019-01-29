<?php
namespace Backend\BaseBundle\Service;

use Backend\BaseBundle\Model\OperationLog;
use Backend\BaseBundle\Model\SiteUser;
use JMS\DiExtraBundle\Annotation\Service;

/**
 * @Service("backend.base_bundle.operationlog")
 *
 */
class OperationLogger
{
    public function log(SiteUser $user, $type, $table, $column = array())
    {
        $operationLog = new OperationLog();
        $operationLog->setSiteUserId($user->getId());
        $operationLog->setModifyType($type);
        $operationLog->setModifyTable($table);
        $operationLog->setModifyColumn($column);
        $operationLog->save();
    }
}