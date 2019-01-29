<?php
namespace Backend\BaseBundle\Tests\Token\Service;


use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Backend\BaseBundle\Token\Service\Token;

class TokenTest extends BaseTestCase
{
    public function test_isType_match()
    {
        //arrange
        $type = 'test';
        $token = new Token(array(
            'type' => $type,
            'data' => array(),
            'id' => 'id',
        ));

        //act
        $result = $token->isType($type);

        //assert
        $this->assertTrue($result);
    }

    public function test_isType_not_match()
    {
        //arrange
        $type = 'test';
        $token = new Token(array(
            'type' => $type,
            'data' => array(),
            'id' => 'id',
        ));

        //act
        $result = $token->isType('badtype');

        //assert
        $this->assertFalse($result);
    }

}