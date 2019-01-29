<?php
namespace Backend\BaseBundle\Tests\FileStore\Uid;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Backend\BaseBundle\FileStore\Uid\RandomUidGenerator;

/**
 * @group unit
 */
class RandomUidGeneratorTest extends BaseTestCase
{
    public function test_generate()
    {
        for($i=0; $i<20; $i++) {
            //arrange
            $generator = new RandomUidGenerator();

            //act
            $uid = $generator->generate();

            //assert
            $this->assertTrue(preg_match('/^[0-9a-z,_]+$/i', $uid, $match) > 0);
        }
    }
}