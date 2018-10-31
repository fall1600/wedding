<?php

namespace Widget\InvitationBundle\Tests\Controller\API;

use Backend\BaseBundle\Tests\Fixture\BaseWebTestCase;
use Widget\InvitationBundle\Model\InvitationQuery;

class InvitationControllerTest extends BaseWebTestCase
{
    public function test_createAction()
    {
        //arrange
        $parameter = array(
            "name" => "魏餅餅",
            "phone" => "0988555666",
            "attend" => "taipei",
            "known_from" => "male"
        );

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
        $this->assertArrayHasKey("nickname", $result);
        $this->assertArrayHasKey("phone", $result);
        $this->assertArrayHasKey("number_of_people", $result);
        $this->assertArrayHasKey("address", $result);
        $this->assertArrayHasKey("email", $result);
        $this->assertArrayHasKey("attend", $result);
        $this->assertArrayHasKey("known_from", $result);
        $this->assertArrayHasKey("number_of_vegetarian", $result);
        $this->assertArrayHasKey("number_of_baby_seat", $result);
        $this->assertArrayHasKey("note", $result);
        $this->assertArrayHasKey("created_at", $result);
        $this->assertArrayHasKey("updated_at", $result);

        //teardown
        InvitationQuery::create()->findOneById($result['id'])->delete();
    }
}
