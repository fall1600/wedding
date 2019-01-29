<?php
namespace Backend\BaseBundle\Controller\BackendAPI;

use Backend\BaseBundle\Form\Type\APIFormTypeItem;
use Backend\BaseBundle\Form\Type\RelationType;
use Backend\BaseBundle\Model\SiteGroup;
use Backend\BaseBundle\Model\SiteGroupQuery;
use Backend\BaseBundle\Model\SiteUser;
use Backend\BaseBundle\Model\SiteUserPeer;
use Backend\BaseBundle\Model\SiteUserQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @Route("/siteuser")
 * @Security("has_role_or_superadmin('ROLE_SUPERADMIN')")
 */
class SiteUserController extends BaseBackendAPIController
{
    /**
     * @return APIFormTypeItem[]
     */
    protected function getFormConfig()
    {
        return array(
            (new APIFormTypeItem('login_name'))->setOptions(array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'error.missing_field',
                    )),
                    new Regex(array(
                        'pattern' => '/^[0-9a-z]+$/',
                        'message' => 'fos_user.loginName.pattern.error'
                    )),
                    new Callback(function($value, ExecutionContextInterface $context){
                        $object = $context->getRoot()->getData();
                        $siteUser = SiteUserQuery::create()
                            ->filterByLoginName($value)
                            ->findOne();
                        if($siteUser && $siteUser->getId() != $object->getId()){
                            $context->addViolation('fos_user.loginName.duplicate');
                        }
                    })
                )
            )),
            (new APIFormTypeItem('email'))->setOptions(array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'error.email.missing_field',
                    )),
                    new Regex(array(
                        'pattern' => '/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/',
                        'message' => 'error.email.format_incorrect_field'
                    )),
                    new Callback(function($value, ExecutionContextInterface $context){
                        $object = $context->getRoot()->getData();
                        $siteUser = SiteUserQuery::create()
                            ->filterByEmail($value)
                            ->findOne();
                        if($siteUser && $siteUser->getId() != $object->getId()){
                            $context->addViolation('fos_user.email.already_used');
                        }
                    }),
                ),
            )),
            (new APIFormTypeItem('first_name'))->setOptions(array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'error.missing_field',
                    )),
                ),
            )),
            (new APIFormTypeItem('last_name'))->setOptions(array(
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'error.missing_field',
                    )),
                ),
            )),
            new APIFormTypeItem('enabled'),
            new APIFormTypeItem('plain_password'),
            new APIFormTypeItem('default_roles'),
            new APIFormTypeItem('custom_roles'),
            //new APIFormTypeItem('site_groups')
        );
    }

    /**
     * @Route("/")
     * @Method({"POST"})
     * @Security("has_role_or_superadmin('ROLE_SUPERADMIN')")
     */
    public function createAction(Request $request)
    {
        $siteUser = new SiteUser();
        $form = $this->bindObject($siteUser, $request->getContent());

        if(!($form->isValid())) {
            return $this->createFormErrorJsonResponse($form->getErrors(true));
        }

        $this->get('site_user_manager')->updateUser($siteUser);

        return $this->createJsonSerializeResponse($siteUser, array(
            'detail'
        ));
    }

    /**
     * @Route("/")
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_SUPERADMIN')")
     */
    public function searchAction(Request $request)
    {
        return $this->doSearch($request->query->all(), SiteUserQuery::create()->distinct(), SiteUserPeer::class);
    }

    /**
     * @Route("/{id}")
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_SUPERADMIN')")
     */
    public function readAction(SiteUser $siteUser)
    {
        return $this->createJsonSerializeResponse($siteUser, array(
            'detail'
        ));
    }

    /**
     * 表單列表
     * @Route("s/all")
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_SUPERADMIN')")
     */
    public function listAction(Request $request)
    {
        $siteUsers = SiteUserQuery::create()->find();
        if (!$siteUsers){
            return $this->createHttpExceptionResponse(Response::HTTP_NOT_FOUND);
        }
        return $this->createJsonSerializeResponse($siteUsers, array('list'));
    }

    /**
     * @Route("/{id}")
     * @Method({"PUT"})
     * @Security("has_role_or_superadmin('ROLE_SUPERADMIN')")
     */
    public function updateAction(Request $request, SiteUser $siteUser)
    {
        $form = $this->bindObject($siteUser, $request->getContent());

        if(!($form->isValid())) {
            return $this->createFormErrorJsonResponse($form->getErrors(true));
        }

        $this->get('site_user_manager')->updateUser($siteUser);
        $siteUser->save();
        return $this->createJsonSerializeResponse($siteUser, array(
            'detail'
        ));
    }

    /**
     * @Route("/{id}")
     * @Method({"DELETE"})
     * @Security("has_role_or_superadmin('ROLE_SUPERADMIN')")
     */
    public function deleteAction(SiteUser $siteUser)
    {
        $this->deleteObject($siteUser);
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
     * 批次設定值
     * @param $ids
     * @param $column
     * @param $value
     * @param \PropelPDO $con
     * @return \Symfony\Component\HttpFoundation\JsonResponse|void
     */
    protected function batchValue($ids, $column, $value, \PropelPDO $con)
    {
        $siteUsers = SiteUserQuery::create()->findPks($ids, $con);
        foreach ($siteUsers as $siteUser) {
            $siteUser->setByName($column, $value, \BasePeer::TYPE_FIELDNAME);
            $siteUser->save($con);
        }
    }

    /**
     * 批次切換狀態處理
     * @param $ids
     * @param $con
     * @param $column
     */
    protected function batchSwitch($ids, $column, \PropelPDO $con)
    {
        $siteUsers = SiteUserQuery::create()->findPks($ids, $con);
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
        SiteUserQuery::create()->findPks($ids, $con)->delete($con);
    }

    /**
     * 設定管理者 管理群組 關聯
     * @Route("/{id}/sitegroup")
     * @Method({"POST"})
     */
    public function relateToSiteGroupAction(Request $request, SiteUser $siteUser)
    {
        $requestParams = json_decode($request->getContent(), true);
        if (!isset($requestParams['site_groups']) || count($requestParams['site_groups']) == 0) {
            $emptyGroup = SiteGroupQuery::create()->findPks(array());
            $siteUser->setSiteGroups($emptyGroup)->save();
            return $this->createJsonSerializeResponse($siteUser, array('list'));
        }

        $groupIds = array();
        foreach ($requestParams['site_groups'] as $group){
            $groupIds[] = $group;
        }
        $siteGroups = SiteGroupQuery::create()->findPks($groupIds);
        $siteUser->setSiteGroups($siteGroups)->save();
        return $this->createJsonSerializeResponse($siteUser, array('list'));
    }

    /**
     * @Route("/check/bundles")
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_SUPERADMIN')")
     */
    public function checkBundlesAction()
    {
        $bundles = $this->container->getParameter('kernel.bundles');
        return $this->createJsonResponse($bundles);
    }
}