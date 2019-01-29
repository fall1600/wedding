<?php

namespace Backend\BaseBundle\Controller\BackendAPI;

use Backend\BaseBundle\Form\Type\APIFormTypeItem;
use Backend\BaseBundle\Model\OperationLog;
use Backend\BaseBundle\Model\OperationLogPeer;
use Backend\BaseBundle\Model\OperationLogQuery;
use Backend\BaseBundle\Model\SiteUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * 操作紀錄管理
 * @Route("/log")
 * @Security("has_role_or_superadmin('ROLE_OPERATIONLOG_READ')") 
 */
class OperationLogController extends BaseBackendAPIController
{

    /**
     * @return APIFormTypeItem[]
     */
    protected function getFormConfig()
    {
        return array();
    }

    /**
     * @Route("s")
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_OPERATIONLOG_READ')")
     */
    public function searchAction(Request $request)
    {
        return $this->doSearch($request->query->all(), OperationLogQuery::create()->distinct(), OperationLogPeer::class);
    }

    /**
     * @Route("/{id}")
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_OPERATIONLOG_READ')")
     */
    public function readAction(OperationLog $operationLog)
    {
        return $this->createJsonSerializeResponse($operationLog, array('detail'));
    }

    /**
     * @Route("/{id}")
     * @Method({"DELETE"})
     * @Security("has_role_or_superadmin('ROLE_OPERATIONLOG_WRITE')")
     */
    public function deleteAction(OperationLog $operationLog)
    {
        $operationLog->delete();
        return $this->createJsonResponse();
    }

    /**
     * 批次管理
     * @Route("s")
     * @Method({"PUT"})
     * @Security("has_role_or_superadmin('ROLE_SUPERADMIN')")
     */
    public function batchAction(Request $request)
    {
        return parent::batchAction($request);
    }

    /**
     * 批次切換狀態處理
     * @param $ids
     * @param $con
     * @param $column
     */
    protected function batchSwitch($ids, $column, \PropelPDO $con)
    {
        $siteUsers = OperationLogQuery::create()->findPks($ids, $con);
        foreach ($siteUsers as $siteUser) {
            $value = $siteUser->getByName($column, \BasePeer::TYPE_FIELDNAME);
            $siteUser->setByName($column, !$value, \BasePeer::TYPE_FIELDNAME);
            $siteUser->save($con);
        }
    }

    /**
     * 批次切換狀態處理
     * @param $ids
     * @param $con
     * @param $column
     */
    protected function batchDelete($ids, \PropelPDO $con)
    {
        OperationLogQuery::create()->findPks($ids, $con)->delete($con);
    }
}