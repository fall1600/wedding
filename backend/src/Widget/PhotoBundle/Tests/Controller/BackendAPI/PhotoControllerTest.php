<?php
namespace Widget\PhotoBundle\Tests\Controller\API;

use Backend\BaseBundle\Model\SiteUserQuery;
use Backend\BaseBundle\Tests\Fixture\BackendWebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Widget\MemberBundle\Event\TokenSignEvent;
use Widget\MemberBundle\Model\MemberQuery;
use Widget\PhotoBundle\Model\PhotoQuery;

class PhotoControllerTest extends BackendWebTestCase
{

    public function test_uploadAction_not_authtoken()
    {
        //arrange
        $photoConfigName = 'default';
        $filePath = realpath(__DIR__.'/../../Fixture/fakeimage.png');
        $originName = 'test.png';
        $uploadFile = new UploadedFile($filePath, $originName);

        $loginName = 'admin';
        $origin = 'http://localhost';
        $user = SiteUserQuery::create()->findOneByLoginName($loginName);
        $token = $this->createToken($user, $origin);

        //act
        $this->client->request(
            'POST',
            $this->generateUrl('widget_photo_backendapi_photo_upload', array('name' => $photoConfigName)),
            array(),
            array(
                'upload' => $uploadFile,
            )
        );
        $response = $this->client->getResponse();

        //assert
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_uploadAction_with_authtoken_but_no_permisssion()
    {
        //arrange
        $photoConfigName = 'default';
        $filePath = realpath(__DIR__.'/../../Fixture/fakeimage.png');
        $originName = 'test.png';
        $uploadFile = new UploadedFile($filePath, $originName);

        $loginName = 'admin';
        $origin = 'http://localhost';
        $user = SiteUserQuery::create()->findOneByLoginName($loginName);
        $user->setRoles(array());
        $token = $this->createToken($user, $origin);

        //act
        $this->client->request(
            'POST',
            $this->generateUrl('widget_photo_backendapi_photo_upload', array('name' => $photoConfigName)),
            array(),
            array(
                'upload' => $uploadFile,
            ),
            array(
                'HTTP_ORIGIN' => $origin,
                'HTTP_AUTHORIZATION' => "Bearer $token",
            )
        );
        $response = $this->client->getResponse();

        //assert
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function test_uploadAction()
    {
        //arrange
        $photoConfigName = 'default';
        $filePath = realpath(__DIR__.'/../../Fixture/fakeimage.png');
        $originName = 'test.png';
        $uploadFile = new UploadedFile($filePath, $originName);

        $loginName = 'admin';
        $origin = 'http://localhost';
        $user = SiteUserQuery::create()->findOneByLoginName($loginName);
        $token = $this->createToken($user, $origin);

        //act
        $this->client->request(
            'POST',
            $this->generateUrl('widget_photo_backendapi_photo_upload', array('name' => $photoConfigName)),
            array(),
            array(
                'upload' => $uploadFile,
            ),
            array(
                'HTTP_ORIGIN' => $origin,
                'HTTP_AUTHORIZATION' => "Bearer $token",
            )
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertTrue($response->isOk());
        $this->assertArrayHasKey('_uid', $result);
        $photo = PhotoQuery::create()->findOneByUid($result['_uid']);
        $this->assertNotEmpty($photo);
        $photo->delete();
    }
}