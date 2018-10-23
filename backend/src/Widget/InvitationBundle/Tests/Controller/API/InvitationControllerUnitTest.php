<?php

namespace Widget\InvitationBundle\Tests\Controller\API;

use Backend\BaseBundle\Tests\Fixture\BaseWebTestCase;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\Response;
use Widget\InvitationBundle\Model\Invitation;
use Widget\InvitationBundle\Service\InvitationService;

class InvitationControllerUnitTest extends BaseWebTestCase
{

    public function test_createAction()
    {
        //arrange
        $parameter = array(
            "baby_seat" => -1,
            "number_of_people" => 0,
        );

        $service = $this->getMockBuilder(InvitationService::class)
            ->setMethods(array("create"))
            ->getMock();

        $service->expects($this->once())
            ->method("create")
            ->with($parameter)
            ->willReturn(new Invitation());

        $this->client->getContainer()->set('invitation_service', $service);

        //act
        $this->client->request(
            'POST',
            $this->generateUrl('widget_invitation_api_invitation_create'),
            array(),
            array(),
            array(),
            json_encode($parameter)
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertTrue($response->isOk());
        $this->assertArrayHasKey("id", $result);
        $this->assertArrayHasKey("name", $result);
        $this->assertArrayHasKey("nick_name", $result);
        $this->assertArrayHasKey("phone", $result);
        $this->assertArrayHasKey("number_of_people", $result);
        $this->assertArrayHasKey("address", $result);
        $this->assertArrayHasKey("email", $result);
        $this->assertArrayHasKey("attend", $result);
        $this->assertArrayHasKey("known_from", $result);
        $this->assertArrayHasKey("is_vegetarian", $result);
        $this->assertArrayHasKey("baby_seat", $result);
        $this->assertArrayHasKey("note", $result);
        $this->assertArrayHasKey("created_at", $result);
        $this->assertArrayHasKey("updated_at", $result);
    }

    public function test_createAction_unit_壞參數()
    {
        //arrange
        $parameter = array(
            "baby_seat" => -1,
            "number_of_people" => 0,
        );

        $mockErrorIter = $this->getMockBuilder(FormErrorIterator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service = $this->getMockBuilder(InvitationService::class)
            ->setMethods(array("create"))
            ->getMock();

        $service->expects($this->once())
            ->method("create")
            ->with($parameter)
            ->willReturn($mockErrorIter);

        $this->client->getContainer()->set('invitation_service', $service);

        //act
        $this->client->request(
            'POST',
            $this->generateUrl('widget_invitation_api_invitation_create'),
            array(),
            array(),
            array(),
            json_encode($parameter)
        );
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        //assert
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
