<?php
namespace Widget\PhotoBundle\Tests\Controller\API;

use Backend\BaseBundle\Tests\Fixture\BaseWebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Widget\MemberBundle\Event\TokenSignEvent;
use Widget\MemberBundle\Model\MemberQuery;
use Widget\PhotoBundle\Model\PhotoQuery;

class MainControllerMemberAuthTokenTest extends BaseWebTestCase
{

    public function test_uploadAction_not_authtoken()
    {
        //arrange
        $photoConfigName = 'avatar';
        $filePath = realpath(__DIR__.'/../../Fixture/fakeimage.png');
        $originName = 'test.png';
        $issuer = 'http://localhost';
        $origin = 'http://localhost';
        $eventDispatcher = $this->client->getContainer()->get('event_dispatcher');
        $eventDispatcher->addListener(TokenSignEvent::EVENT_NAME, function(TokenSignEvent $event){
            $event->appendData('uploadphoto', true);
            $event->stopPropagation();
        }, 99999);
        $member = MemberQuery::create()->findOneByName('bubble0');
        $token = (string)$this->client->getContainer()->get('widget_member.token.signer')->sign($issuer, $origin, $member);
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
        $eventDispatcher = $this->client->getContainer()->get('event_dispatcher');
        $eventDispatcher->addListener(TokenSignEvent::EVENT_NAME, function(TokenSignEvent $event){
            //$event->appendData('uploadphoto', true);
            $event->stopPropagation();
        }, 99999);
        $member = MemberQuery::create()->findOneByName('bubble0');
        $token = (string)$this->client->getContainer()->get('widget_member.token.signer')->sign($issuer, $origin, $member);
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
        $eventDispatcher = $this->client->getContainer()->get('event_dispatcher');
        $eventDispatcher->addListener(TokenSignEvent::EVENT_NAME, function(TokenSignEvent $event){
            $event->appendData('uploadphoto', true);
            $event->stopPropagation();
        }, 99999);
        $member = MemberQuery::create()->findOneByName('bubble0');
        $token = (string)$this->client->getContainer()->get('widget_member.token.signer')->sign($issuer, $origin, $member);
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
        $eventDispatcher = $this->client->getContainer()->get('event_dispatcher');
        $eventDispatcher->addListener(TokenSignEvent::EVENT_NAME, function(TokenSignEvent $event){
            $event->appendData('uploadphoto', true);
            $event->stopPropagation();
        }, 99999);
        $member = MemberQuery::create()->findOneByName('bubble0');
        $token = (string)$this->client->getContainer()->get('widget_member.token.signer')->sign($issuer, $origin, $member);
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