<?php

namespace Widget\InvitationBundle\Tests\Controller\API;

use Backend\BaseBundle\Tests\Fixture\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;
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
            "known_from" => "male",
            "number_of_people" => 1
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

    public function test_createAction_來亂的那種()
    {
        //arrange
        $parameter = array();
        $expects = array(
            'name' => array("姓名必填"),
            'phone' => array("聯絡電話必填"),
            'attend' => array("出席意願必填", "出席意願不要給我亂亂填 (╯°▽°)╯ ┻━┻ "),
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
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals($expects, $result);
    }
}
