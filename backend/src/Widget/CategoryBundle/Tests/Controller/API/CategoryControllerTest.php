<?php
/**
 * Created by PhpStorm.
 * User: bubble
 * Date: 2017/10/13
 * Time: 上午10:30
 */
namespace Widget\CategoryBundle\Tests\Controller\API;

use Backend\BaseBundle\Tests\Fixture\BaseWebTestCase;
use Widget\CategoryBundle\Model\Category;
use Widget\CategoryBundle\Model\CategoryPeer;
use Widget\CategoryBundle\Model\CategoryThread;

class CategoryControllerTest extends BaseWebTestCase
{
    /**
     * assert 要看到status為true的有兩筆
     */
    public function test_getRootCategoryAction_status_is_true()
    {
        //arrange
        $categoryThread = new CategoryThread();
        $categoryThread
            ->setThread('test')
            ->save();
        $category = new Category();
        $category
            ->setCategoryThread($categoryThread)
            ->setStatus(true)
            ->makeRoot()
            ->save();
        $category1 = new Category();
        $category1
            ->setName('category1')
            ->insertAsLastChildOf($category)->setStatus(true)->save();
        $category2 = new Category();
        $category2
            ->setName('category2')
            ->insertAsLastChildOf($category)->setStatus(true)->save();
        $category3 = new Category();
        $category3
            ->setName('category3')
            ->insertAsLastChildOf($category)->setStatus(false)->save();

        //act
        $this->client->request(
            'GET',
            $this->generateUrl('widget_category_api_category_getrootcategory', array('thread' => $categoryThread->getThread()))
        );

        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertTrue($response->isOk());
        $this->assertCount(2, $result);
        $this->assertTrue($result[0]['status']);
        $this->assertTrue($result[1]['status']);

        $category1->delete();
        $category2->delete();
        $category3->delete();
        CategoryPeer::deleteTree($categoryThread->getId());
        $categoryThread->delete();
    }
}