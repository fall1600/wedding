<?php

namespace Widget\CategoryBundle\Controller\BackendAPI;

use Backend\BaseBundle\Controller\BackendAPI\BaseBackendAPIController;
use Backend\BaseBundle\Form\Type\APIFormTypeItem;
use Backend\BaseBundle\Propel\I18n;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Widget\CategoryBundle\Event\CategoryEvent;
use Widget\CategoryBundle\Model;


/**
 * @Route("/category")
 * @Security("has_role_or_superadmin('ROLE_CATEGORY_READ')")
 */
class CategoryController extends BaseBackendAPIController
{
    /**
     * 欄位表單設定
     * 主要是USER送過來的欄位
     * @return APIFormTypeItem[]
     */
    protected function getFormConfig()
    {
        return array(
            new APIFormTypeItem('name'),
            (new APIFormTypeItem('status'))->setFieldType(ChoiceType::class)->setOptions(array(
                'choices' => array(
                    true => 1,
                    false => 0,
                    null => 0,
                )
            )),
        );
    }

    /**
     * @Route("/{thread}", requirements={"thread" = "[a-z_]+"})
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_CATEGORY_READ')")
     */
    public function indexAction($thread)
    {
        return $this->createJsonSerializeResponse($this->makeRootTypeCategory($thread), array('detail'));
    }

    /**
     *
     * @Route("/{id}", requirements={"id" = "\d+"})
     * @Method({"POST"})
     * @Security("has_role_or_superadmin('ROLE_CATEGORY_WRITE')")
     */
    public function createCategoryAction(Request $request, Model\Category $category)
    {
        $newCategory = new Model\Category();
        if ($newCategory instanceof I18n){
            $locale = $request->query->get('_locale');
            $newCategory->setLocale($locale);
        }
        $newCategory->insertAsLastChildOf($category);

        return $this->doProcessForm($newCategory, $request->getContent());
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"})
     * @Method({"GET"})
     * @Security("has_role_or_superadmin('ROLE_CATEGORY_READ')")
     */
    public function readCategoryAction(Request $request, Model\Category $category)
    {
        if ($category instanceof I18n){
            $locale = $request->query->get('_locale');
            $category->setLocale($locale);
        }
        return $this->createJsonSerializeResponse($category->getChildren(), array('detail'));
    }
    
    /**
     * @Route("/{id}", requirements={"id" = "\d+"})
     * @Method({"PUT"})
     * @Security("has_role_or_superadmin('ROLE_CATEGORY_WRITE')")
     */
    public function updateCategoryAction(Request $request, Model\Category $category)
    {
        if ($category instanceof I18n){
            $locale = $request->query->get('_locale');
            $category->setLocale($locale);
        }
        return $this->doProcessForm($category, $request->getContent());
    }    
    
    /**
     * @Route("/{id}", requirements={"id" = "\d+"})
     * @Method({"DELETE"})
     * @Security("has_role_or_superadmin('ROLE_CATEGORY_WRITE')")
     */
    public function deleteCategoryAction(Request $request, Model\Category $category)
    {
        $this->deleteObject($category);
        return $this->createJsonResponse();
    }

    /**
     * @Route("/{id}/moveto", requirements={"id" = "\d+"})
     * @Method({"PUT"})
     * @Security("has_role_or_superadmin('ROLE_CATEGORY_WRITE')")
     */
    public function movetoCategoryAction(Request $request, Model\Category $category)
    {
        $param = json_decode($request->getContent(), true);
        $targetCategory = Model\CategoryQuery::create()->findPk($param['target_id']);
        $isInsert = $param['position'] == 'inside';

        if($targetCategory->getThreadId() != $category->getThreadId()){
            throw new AccessDeniedException();
        }

        if($isInsert){
            $category->moveToFirstChildOf($targetCategory);
        }
        else{
            $category->moveToNextSiblingOf($targetCategory);
        }
        $this->logOperation($category);
        $category->save();
        return new JsonResponse(array(
            'id' => $category->getId(),
        ));
    }


    /**
     * @return Model\Category
     */
    protected function makeRootTypeCategory($thread)
    {
        $rootCategory = Model\CategoryQuery::create()
            ->filterByTreeLevel(0)
            ->useCategoryThreadQuery()
            ->filterByThread($thread)
            ->endUse()
            ->findOne()
        ;
        
        if(!$rootCategory){
            $categoryThread = new Model\CategoryThread();
            $categoryThread->setThread($thread);
            $rootCategory = new Model\Category();
            $rootCategory->setCategoryThread($categoryThread);
            $rootCategory->makeRoot();
            $rootCategory->save();
            return $rootCategory;
        }
        return $rootCategory;
    }
}