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
            "phone" => "0988555666"
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

        var_dump($response);
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

        //teardown
        InvitationQuery::create()->findOneById($result['id'])->delete();
    }

    public function test_createAction_壞參數()
    {
        //arrange
        $parameter = array(
            "baby_seat" => -1,
            "number_of_people" => 0,
        );

        $expected = array(
            'msg' => array(
                'name' => array(
                    '姓名必填'
                ),
                'phone' => array(
                    '聯絡電話必填'
                ),
                'baby_seat' => array(
                    '兒童座椅數量錯誤'
                ),
                'number_of_people' => array(
                    '出席人數錯誤'
                ),
            )
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
        $this->assertEquals($expected, $result);
    }
}
