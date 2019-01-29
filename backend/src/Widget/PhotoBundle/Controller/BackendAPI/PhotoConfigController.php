<?php

namespace Widget\PhotoBundle\Controller\BackendAPI;

use Backend\BaseBundle\Controller\BackendAPI\BaseBackendAPIController;
use Backend\BaseBundle\Form\Type\APIFormTypeItem;
use Backend\BaseBundle\Form\Type\BaseFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\Regex;
use Widget\PhotoBundle\Model\PhotoConfig;
use Widget\PhotoBundle\Model\PhotoConfigPeer;
use Widget\PhotoBundle\Model\PhotoConfigQuery;

/**
 * @Route("/photoconfig")
 * @Security("has_role_or_superadmin('ROLE_PHOTO_CONFIG')")
 */
class PhotoConfigController extends BaseBackendAPIController
{

    /**
     * 欄位表單設定
     * 主要是USER送過來的欄位
     * @return APIFormTypeItem[]
     */
    protected function getFormConfig()
    {
        return array(
            (new APIFormTypeItem('name'))->setOptions(array(
                'required' => true,
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'error.missing_field',
                    )),
                )
            )),
            (new APIFormTypeItem('brief'))->setOptions(array(
                'required' => true,
                'constraints' => array(
                    new NotBlank(array(
                        'message' => 'error.missing_field',
                    )),
                )
            )),
            new APIFormTypeItem('crop'),
            (new APIFormTypeItem('config'))->setFieldType(CollectionType::class)->setOptions(array(
                    'type' => new BaseFormType(function(FormBuilderInterface $builder){
                        $builder
                            ->add('type', TextType::class, array(
                                'label' => 'form.label.edit.photo_config_item.type',
                                'constraints' => array(

                                )
                            ))
                            ->add('suffix', TextType::class, array(
                                'label' => 'form.label.edit.photo_config_item.suffix',
                                'constraints' => array(
                                    new NotBlank(),
                                    new NotEqualTo(array(
                                        'value' => 'origin',
                                        'message' => 'validator.photo_config.suffix.value_not_allow',
                                    )),
                                    new Regex(array(
                                        'pattern' => '/^[a-z][0-9a-z]+$/',
                                        'message' => 'validator.photo_config.suffix.value_not_allow',
                                    )),
                                ),
                            ))
                            ->add('width', IntegerType::class, array(
                                'label' => 'form.label.edit.photo_config_item.width',
                                'constraints' => array(
                                    new NotBlank(),
                                    new GreaterThan(array(
                                        'value' => 0,
                                        'message' => 'validator.photo_config.greater_than_0'
                                    )),
                                ),
                            ))
                            ->add('height', IntegerType::class, array(
                                'label' => 'form.label.edit.photo_config_item.height',
                                'constraints' => array(
                                    new NotBlank(),
                                    new GreaterThan(array(
                                        'value' => 0,
                                        'message' => 'validator.photo_config.greater_than_0'
                                    )),
                                ),
                            ))
                        ;
                    }, 'Config'),
                    'allow_add' => true,
                    'allow_delete'  => true,
            ))
        );
    }
    /**
     * 新增
     * @Route("s")
     * @Method({"POST"})
     * @Security("has_role_or_superadmin('ROLE_PHOTO_CONFIG_WRITE')")
     */
    public function createAction(Request $request)
    {
        // 抽離出來做（同樣）動作 參數： 物件、content
        return $this->doProcessForm(new PhotoConfig(), $request->getContent());
    }

    /**
     * 搜尋
     * @Route("s")
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_PHOTO_CONFIG_READ')")
     */
    public function searchAction(Request $request)
    {
        return $this->doSearch($request->query->all(), PhotoConfigQuery::create()->distinct(), PhotoConfigPeer::class);
    }

    /**
     * 表單列表
     * @Route("s/all")
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_PHOTO_CONFIG_READ')")
     */
    public function listAction(Request $request)
    {
        $photoConfig = PhotoConfigQuery::create()->find();
        if (!$photoConfig){
            return new Response('', Response::HTTP_NOT_FOUND);
        }
        return $this->createJsonSerializeResponse($photoConfig, array('list'));
    }

    /**
     * 讀取
     * @Route("/{id}")
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_PHOTO_CONFIG_READ')")
     */
    public function readAction(PhotoConfig $photoConfig)
    {
        return $this->createJsonSerializeResponse($photoConfig, array('detail'));
    }

    /**
     * 更新
     * @Route("/{id}")
     * @Method({"PUT"})
     * @Security("has_role_or_superadmin('ROLE_PHOTO_CONFIG_WRITE')")
     */
    public function updateAction(Request $request, PhotoConfig $photoConfig)
    {
        return $this->doProcessForm($photoConfig, $request->getContent());
    }

    /**
     * 刪除
     * @Route("/{id}")
     * @Method({"DELETE"})
     * @Security("has_role_or_superadmin('ROLE_PHOTO_CONFIG_WRITE')")
     */
    public function deleteAction(PhotoConfig $photoConfig)
    {
        $this->deleteObject($photoConfig);
        return $this->createJsonResponse();
    }

    /**
     * 批次管理
     * @Route("s")
     * @Method({"PUT"})
     * @Security("has_role_or_superadmin('ROLE_PRODUCT_WRITE')")
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
    protected function batchDelete($ids, \PropelPDO $con)
    {
        PhotoConfigQuery::create()->findPks($ids, $con)->delete($con);
    }

    /**
     * 批次切換狀態處理
     * @param $ids
     * @param $con
     * @param $column
     */
    protected function batchSwitch($ids, $column, \PropelPDO $con)
    {
        $photos = PhotoConfigQuery::create()->findPks($ids, $con);
        foreach ($photos as $photo) {
            $value = $photo->getByName($column, \BasePeer::TYPE_FIELDNAME);
            $photo->setByName($column, !$value, \BasePeer::TYPE_FIELDNAME);
            $photo->save($con);
        }
    }
}