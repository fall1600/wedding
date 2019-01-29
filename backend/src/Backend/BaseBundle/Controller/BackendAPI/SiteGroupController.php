<?php
namespace Backend\BaseBundle\Controller\BackendAPI;

use Backend\BaseBundle\Form\Type\APIFormTypeItem;
use Backend\BaseBundle\Form\Type\RelationType;
use Backend\BaseBundle\Model\SiteGroup;
use Backend\BaseBundle\Model\SiteGroupPeer;
use Backend\BaseBundle\Model\SiteGroupQuery;
use Backend\BaseBundle\Model\SiteUserQuery;
use Backend\BaseBundle\Model\SiteUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @Route("/sitegroup")
 * @Security("has_role_or_superadmin('ROLE_SUPERADMIN')")
 */
class SiteGroupController extends BaseBackendAPIController
{
    /**
     * @return APIFormTypeItem[]
     */
    protected function getFormConfig()
    {
        return array(
            (new APIFormTypeItem('name'))->setOptions(array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'error.missing_field',
                    )),
                    new Callback(function($value, ExecutionContextInterface $context){
                        $object = $context->getRoot()->getData();
                        $siteGroup = SiteGroupQuery::create()
                            ->findOneByName($value);
                        if($siteGroup && $siteGroup->getId() != $object->getId()){
                            $context->addViolation('error.site_group.name.duplicate');
                        }
                    })
                ),
            )),
            new APIFormTypeItem('default_roles'),
            new APIFormTypeItem('custom_roles'),
            (new APIFormTypeItem('site_users'))
                ->setFieldType(RelationType::class)
                ->setOptions(array(
                    'class' => SiteUser::class,
                    'multiple' => true,
                    'constraints' => array(
                        new NotBlank(array(
                            'message' => 'error.missing_field',
                        )),
                    ),
                )),
        );
    }

    /**
     * @Route("s")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        return $this->doProcessForm(new SiteGroup(), $request->getContent());
    }

    /**
     * @Route("s")
     * @Method({"GET"})
     */
    public function searchAction(Request $request)
    {
        return $this->doSearch($request->query->all(), SiteGroupQuery::create()->distinct(), SiteGroupPeer::class);
    }

    /**
     * @Route("/{id}")
     * @Method({"GET"})
     */
    public function readAction(SiteGroup $siteGroup)
    {
        return $this->createJsonSerializeResponse($siteGroup, array(
            'detail'
        ));
    }

    /**
     * @Route("s/all")
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        $group = SiteGroupQuery::create()->find();
        if (!$group){
            return $this->createHttpExceptionResponse(Response::HTTP_NOT_FOUND);
        }
        return $this->createJsonSerializeResponse($group, array('detail'));
    }

    /**
     * @Route("/{id}")
     * @Method({"PUT"})
     */
    public function updateAction(Request $request, SiteGroup $siteGroup)
    {
        return $this->doProcessForm($siteGroup, $request->getContent());
    }

    /**
     * @Route("/{id}")
     * @Method({"DELETE"})
     */
    public function deleteAction(SiteGroup $siteGroup)
    {
        $this->deleteObject($siteGroup);
        return $this->createJsonResponse();
    }

    /**
     * 批次管理
     * @Route("s")
     * @Method({"PUT"})
     * @Security("has_role_or_superadmin('ROLE_ARTICLE_WRITE')")
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
        $siteGroups = SiteGroupQuery::create()->findPks($ids, $con);
        foreach ($siteGroups as $siteGroup) {
            $value = $siteGroup->getByName($column, \BasePeer::TYPE_FIELDNAME);
            $siteGroup->setByName($column, !$value, \BasePeer::TYPE_FIELDNAME);
            $siteGroup->save($con);
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
        SiteGroupQuery::create()->findPks($ids, $con)->delete($con);
    }

    /**
     * 設定管理者 管理群組 關聯
     * @Route("/{id}/siteuser")
     * @Method({"POST"})
     */
    public function relateToSiteGroupAction(Request $request, SiteGroup $siteGroup)
    {
        $requestParams = json_decode($request->getContent(), true);
        if (!isset($requestParams['site_users']) || count($requestParams['site_users']) == 0) {
            $emptyUser = SiteUserQuery::create()->findPks(array());
            $siteGroup->setSiteUsers($emptyUser)->save();
            return $this->createJsonSerializeResponse($siteGroup, array('list'));
        }

        $userIds = array();
        foreach ($requestParams['site_users'] as $user){
            $userIds[] = $user;
        }
        $siteUsers = SiteUserQuery::create()->findPks($userIds);
        $siteGroup->setSiteUsers($siteUsers)->save();
        return $this->createJsonSerializeResponse($siteGroup, array('list'));
    }
}