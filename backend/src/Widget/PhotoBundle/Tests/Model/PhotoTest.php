<?php
namespace Widget\PhotoBundle\Tests\Model;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Widget\PhotoBundle\Model\Photo;

class PhotoTest extends BaseTestCase
{
    public function test_setInfo()
    {
        //arrange
        $info = array('test_info');
        $photo = $this->getMockBuilder(Photo::class)
            ->setMethods(array('updateSizeFromInfo'))
            ->getMock();
        $photo
            ->expects($this->once())
            ->method('updateSizeFromInfo')
            ->willReturnCallback(function($infoForTest) use($info){
                $this->assertEquals($info, $infoForTest);
            });

        //act
        $photo->setInfo($info);

        //assert
        $this->assertEquals(serialize($info), $this->getObjectAttribute($photo, 'info'));
    }

    public function test_updateSizeFromInfo()
    {
        //arrange
        $info = array(
            'large' => array(
                'filesize' => 123,
                'suffix' => 'large',
            ),
            'small' => array(
                'filesize' => 456,
                'suffix' => 'large',
            ),
            'tiny' => array(
                'filesize' => 789,
                'suffix' => 'tiny',
            ),
        );
        $totalsize = 123 + 789;
        $photo = $this->getMockBuilder(Photo::class)
            ->setMethods(array('setSize'))
            ->getMock();
        $photo
            ->expects($this->once())
            ->method('setSize')
            ->willReturnCallback(function($size) use($totalsize){
                $this->assertEquals($totalsize, $size);
            });

        //act
        $this->callObjectMethod($photo, 'updateSizeFromInfo', $info);

        //assert
    }
}