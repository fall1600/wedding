<?php
namespace Widget\PhotoBundle\Tests\Controller\API;

use Backend\BaseBundle\Tests\Fixture\BaseWebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Widget\MemberBundle\Event\TokenSignEvent;
use Widget\MemberBundle\Model\MemberQuery;
use Widget\MemberBundle\Token\MemberTokenRequest;
use Widget\PhotoBundle\Model\PhotoQuery;
use Widget\PhotoBundle\Token\PhotoUploadTokenRequest;

class MainControllerTokenServiceTest extends BaseWebTestCase
{

    public function test_uploadAction_not_authtoken()
    {
        //arrange
        $photoConfigName = 'avatar';
        $filePath = realpath(__DIR__.'/../../Fixture/fakeimage.png');
        $originName = 'test.png';
        $issuer = 'http://localhost';
        $origin = 'http://localhost';
        $member = MemberQuery::create()->findOneByName('bubble0');
        $token = $this->client->getContainer()->get('token_service')->sign(new PhotoUploadTokenRequest(new MemberTokenRequest($member)));
        $uploadFile = new UploadedFile($filePath, $originName);

        //act
        $this->client->request(
            'POST',
            $this->generateUrl('widget_photo_api_main_upload', array('name' => $photoConfigName)),
            array(),
            array(
                'upload' => $uploadFile,
            )
        );
        $response = $this->client->getResponse();

        //assert
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_uploadAction_with_authtoken_but_no_upload_permisssion()
    {
        //arrange
        $photoConfigName = 'avatar';
        $filePath = realpath(__DIR__.'/../../Fixture/fakeimage.png');
        $originName = 'test.png';
        $issuer = 'http://localhost';
        $origin = 'http://localhost';
        $member = MemberQuery::create()->findOneByName('bubble0');
        $token = $this->client->getContainer()->get('token_service')->sign(new MemberTokenRequest($member));
        $uploadFile = new UploadedFile($filePath, $originName);

        //act
        $this->client->request(
            'POST',
            $this->generateUrl('widget_photo_api_main_upload', array('name' => $photoConfigName)),
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

    public function test_uploadAction_with_authtoken_but_more_than_one_files()
    {
        //arrange
        $photoConfigName = 'avatar';
        $filePath = realpath(__DIR__.'/../../Fixture/fakeimage.png');
        $originName = 'test.png';
        $issuer = 'http://localhost';
        $origin = 'http://localhost';
        $member = MemberQuery::create()->findOneByName('bubble0');
        $token = $this->client->getContainer()->get('token_service')->sign(new PhotoUploadTokenRequest(new MemberTokenRequest($member)));
        $uploadFile = new UploadedFile($filePath, $originName);

        //act
        $this->client->request(
            'POST',
            $this->generateUrl('widget_photo_api_main_upload', array('name' => $photoConfigName)),
            array(),
            array(
                'upload1' => $uploadFile,
                'upload2' => $uploadFile,
            ),
            array(
                'HTTP_ORIGIN' => $origin,
                'HTTP_AUTHORIZATION' => "Bearer $token",
            )
        );
        $response = $this->client->getResponse();

        //assert
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function test_uploadAction()
    {
        //arrange
        $photoConfigName = 'avatar';
        $filePath = realpath(__DIR__.'/../../Fixture/fakeimage.png');
        $originName = 'test.png';
        $issuer = 'http://localhost';
        $origin = 'http://localhost';
        $member = MemberQuery::create()->findOneByName('bubble0');
        $token = $this->client->getContainer()->get('token_service')->sign(new PhotoUploadTokenRequest(new MemberTokenRequest($member)));
        $uploadFile = new UploadedFile($filePath, $originName);

        //act
        $this->client->request(
            'POST',
            $this->generateUrl('widget_photo_api_main_upload', array('name' => $photoConfigName)),
            array(),
            array(
                'upload1' => $uploadFile,
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
        $this->assertEquals($photo->getMemberId(), $member->getId());
        $photo->delete();
    }
}