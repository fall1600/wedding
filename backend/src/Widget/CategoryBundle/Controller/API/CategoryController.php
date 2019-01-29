<?php
namespace Widget\CategoryBundle\Controller\API;

use Backend\BaseBundle\Controller\API\BaseController;
use Backend\BaseBundle\Model\Site;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Widget\CategoryBundle\Model\Category;
use Widget\CategoryBundle\Model\CategoryQuery;

/**
 * @Route("/category")
 */
class CategoryController extends BaseController
{
    /**
     * 取得Root Category的API
     * @Route("/root/{thread}")
     * @Method({"GET"})
     */
    public function getRootCategoryAction($thread)
    {
        /** @var Category $rootCategory */
        if(!$rootCategory = CategoryQuery::create()
            ->filterByTreeLevel(0)
            ->filterByStatus(true)
            ->useCategoryThreadQuery()
            ->filterByThread($thread)
            ->endUse()
            ->findOne()){
            return $this->createHttpExceptionResponse(Response::HTTP_NOT_FOUND);
        }

        return $this->createJsonSerializeResponse(
            $rootCategory->getChildren(CategoryQuery::create()->filterByStatus(true)),
            array('detail')
        );
    }

    /**
     * 取得Category的API(下層分支)
     * @Route("/{id}")
     * @ParamConverter("site", options={"exclude": {"id"}})
     * @Method({"GET"})
     */
    public function getCategoryAction(Category $category)
    {
        if (!$children = $category->getChildren(CategoryQuery::create()->filterByStatus(true))){
            return $this->createHttpExceptionResponse(Response::HTTP_NOT_FOUND);
        }
        return $this->createJsonSerializeResponse($children, array('list'));
    }

    /**
     * 取得Category Ancestors的API(上層分支)
     * @Route("/{id}/ancestors")
     * @ParamConverter("site", options={"exclude": {"id"}})
     * @Method({"GET"})
     */
    public function getCategoryAncestorsAction(Category $category)
    {
        if (!$ancestors = CategoryQuery::create()
            ->filterByStatus(true)
            ->inTree($category->getScopeValue())
            ->filterByTreeLeft($category->getLeftValue(), \Criteria::LESS_EQUAL)
            ->filterByTreeRight($category->getRightValue(), \Criteria::GREATER_EQUAL)
            ->orderByBranch()
            ->find()){
            return $this->createHttpExceptionResponse(Response::HTTP_NOT_FOUND);
        }

        return $this->createJsonSerializeResponse($ancestors, array('list'));
    }
}