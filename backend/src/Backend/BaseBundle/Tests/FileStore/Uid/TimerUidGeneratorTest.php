<?php
namespace Backend\BaseBundle\Tests\FileStore\Uid;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Backend\BaseBundle\FileStore\Uid\TimerUidGenerator;

/**
 * @group unit
 */
class TimerUidGeneratorTest extends BaseTestCase
{
    public function test_generate()
    {
        $prevtimer = 0;
        for($i=0; $i < 20; $i++) {
            //arrange
            $generator = new TimerUidGenerator();

            //act
            list($timer, $rand) = explode('-', $generator->generate());

            //assert
            $this->assertGreaterThan(0, preg_match('/^\d+$/i', $timer, $match));
            $this->assertGreaterThan(0, preg_match('/^\d{20}$/i', $rand, $match));
            $this->assertGreaterThan($prevtimer, $timer);
            usleep(1000);
            $prevtimer = $timer;
        }
    }
}