<?php

namespace Widget\CategoryBundle\Tests\Controller\BackendAPI;

use Backend\BaseBundle\Model\SiteUserQuery;
use Backend\BaseBundle\Tests\Fixture\BackendWebTestCase;
use JMS\Serializer\SerializationContext;
use Widget\CategoryBundle\Event\CategoryEvent;
use Widget\CategoryBundle\Model\Category;
use Widget\CategoryBundle\Model\CategoryQuery;
use Widget\CategoryBundle\Model\CategoryThreadQuery;

class CategoryControllerTest extends BackendWebTestCase
{
    public function test_indexAction()
    {
        //arrange
        $loginName = 'admin';
        $origin = 'http://localhost';
        $token = $this->createToken(SiteUserQuery::create()->findOneByLoginName($loginName), $origin);
        $thread = CategoryThreadQuery::create()->findOneByThread('menu');
        $eventDispatcher = $this->client->getContainer()->get('event_dispatcher');
        $eventDispatcher->addListener(CategoryEvent::EVENT_CATEGORY, function(CategoryEvent $event) use($thread){
            $event->setThreadExist(true);
            $event->setMaxDepth(1);
            $event->stopPropagation();
        }, 999999);

        // 預計會抓到的 category
        $category = CategoryQuery::create()->filterByCategoryThread($thread)->filterByTreeLevel(0)->findOne();

        //act
        $this->client->request(
            'GET',
            $this->generateUrl('widget_category_backendapi_category_index', array('thread' => $thread->getThread())),
            array(),
            array(),
            array(
                'HTTP_ORIGIN' => $origin,
                'HTTP_AUTHORIZATION' => "Bearer $token",
            )
        );

        $response = $this->client->getResponse();
        $responseArray = json_decode($response->getContent(), true);

        // assert
        $this->assertTrue($response->isOk());
        $this->assertArrayHasKey('id', $responseArray);
        $this->assertArrayHasKey('name', $responseArray);
        $this->assertArrayHasKey('status', $responseArray);
        $this->assertArrayHasKey('level', $responseArray);
        $this->assertArrayHasKey('is_leaf', $responseArray);
        $this->assertEquals($responseArray['id'], $category->getId());
    }
    
    public function test_createCategoryAction()
    {
        //arrange
        $loginName = 'admin';
        $origin = 'http://localhost';
        $token = $this->createToken(SiteUserQuery::create()->findOneByLoginName($loginName), $origin);
        $category = CategoryQuery::create()->findOneByTreeLevel(0);
        $params = array(
            'name' => 'Roots',
            'status' => true
        );

        //act
        $this->client->request(
            'POST',
            $this->generateUrl('widget_category_backendapi_category_createcategory', array('id' => $category->getId())),
            array(),
            array(),
            array(
                'HTTP_ORIGIN' => $origin,
                'HTTP_AUTHORIZATION' => "Bearer $token",
            ),
            json_encode($params)
        );
        $response = $this->client->getResponse();
        $responseArray = json_decode($response->getContent(), true);

        // assert
        $newCategory = CategoryQuery::create()->findOneById($responseArray['id']);
        
        $this->assertTrue($response->isOk());
        $this->assertEquals($params['name'], $responseArray['name']);

        $newCategory->delete();
    }

    public function test_readCategoryAction()
    {
        //arrange
        $loginName = 'admin';
        $origin = 'http://localhost';
        $token = $this->createToken(SiteUserQuery::create()->findOneByLoginName($loginName), $origin);
        $category = CategoryQuery::create()->findOneByTreeLevel(0);

        //act
        $this->client->request(
            'GET',
            $this->generateUrl('widget_category_backendapi_category_readcategory', array('id' => $category->getId())),
            array(),
            array(),
            array(
                'HTTP_ORIGIN' => $origin,
                'HTTP_AUTHORIZATION' => "Bearer $token",
            )
        );
        
        $response = $this->client->getResponse();
        $responseArray = json_decode($response->getContent(), true);

        // assert
        $this->assertTrue($response->isOk());
        $this->assertEquals($category->getChildren()->count(), count($responseArray));

    }

    public function test_updateCategoryAction()
    {
        //arrange
        $loginName = 'admin';
        $origin = 'http://localhost';
        $token = $this->createToken(SiteUserQuery::create()->findOneByLoginName($loginName), $origin);
        $category = CategoryQuery::create()->findOneByTreeLevel(0);
        $params = array(
            'name' => 'Re name Root',
            'status' => true,
        );

        //act
        $this->client->request(
            'PUT',
            $this->generateUrl('widget_category_backendapi_category_updatecategory', array('id' => $category->getId())),
            array(),
            array(),
            array(
                'HTTP_ORIGIN' => $origin,
                'HTTP_AUTHORIZATION' => "Bearer $token",
            ),
            json_encode($params)
        );
        $response = $this->client->getResponse();

        $responseArray = json_decode($response->getContent(), true);

        // assert
        $this->assertTrue($response->isOk());
        $this->assertEquals($params['name'], $responseArray['name']);

        $category = CategoryQuery::create()->findOneById($responseArray['id']);
        $category->setName('root');
        $category->save();
    }

    public function test_deleteCategoryAction()
    {
        //arrange
        $loginName = 'admin';
        $origin = 'http://localhost';
        $token = $this->createToken(SiteUserQuery::create()->findOneByLoginName($loginName), $origin);
        $thread = CategoryThreadQuery::create()->findOneByThread('menu');
        $newCategory = new Category();
        $newCategory->setName('testDelete');
        $newCategory->setStatus(true);
        $newCategory->setCategoryThread($thread);
        $newCategory->save();

        //act
        $this->client->request(
            'DELETE',
            $this->generateUrl('widget_category_backendapi_category_deletecategory', array('id' => $newCategory->getId())),
            array(),
            array(),
            array(
                'HTTP_ORIGIN' => $origin,
                'HTTP_AUTHORIZATION' => "Bearer $token",
            )
        );

        $response = $this->client->getResponse();
        $responseArray = json_decode($response->getContent(), true);

        // assert
        $category = CategoryQuery::create()->findOneById($newCategory->getId());
        $this->assertTrue($response->isOk());
        $this->assertNull($category);
    }

    public function test_movetoCategoryAction()
    {
        //arrange
        $con = \Propel::getConnection();
        $con->beginTransaction();
        $loginName = 'admin';
        $origin = 'http://localhost';
        $token = $this->createToken(SiteUserQuery::create()->findOneByLoginName($loginName), $origin);
        $rootCategory = CategoryQuery::create()->findOneByTreeLevel(0);

        $moveCategory = $rootCategory->getLastChild();
        $lastCategoryId = $moveCategory->getId();

        $params = array(
            'target_id' => $rootCategory->getId(),
            'position' => 'inside'
        );
        //act
        $this->client->request(
            'PUT',
            $this->generateUrl('widget_category_backendapi_category_movetocategory', array('id' => $moveCategory->getId())),
            array(),
            array(),
            array(
                'HTTP_ORIGIN' => $origin,
                'HTTP_AUTHORIZATION' => "Bearer $token",
            ),
            json_encode($params)
        );

        $response = $this->client->getResponse();
        $category = CategoryQuery::create()->findOneByTreeLevel(0);
        $moveAfterCategory = CategoryQuery::create()->findOneById($lastCategoryId);

        // assert
        $this->assertTrue($response->isOk());
        $this->assertEquals($moveAfterCategory, $category->getFirstChild());

        $con->rollBack();
    }
}