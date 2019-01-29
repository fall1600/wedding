<?php
namespace Widget\PhotoBundle\Tests\Image\Bridge;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;
use Intervention\Image\Image;
use Widget\PhotoBundle\Tests\Image\MockFileStore;

/**
 * @group unit
 */
class ImageCommandBridgeTest extends BaseTestCase
{
    public function test___construct()
    {
        //arrange
        $img = $this->getMockBuilder(Image::class)
            ->setMethods(array('backup'))
            ->disableOriginalConstructor()
            ->getMock();
        $img->expects($this->once())->method('backup')->willReturn(null);
        $fileStore = new MockFileStore();
        $uid = 'some_uid';

        //act
        $imageCommand = new ImageCommandBridge($img, $fileStore, $uid);

        //assert
        $this->assertEquals($img, $this->getObjectAttribute($imageCommand, 'image'));
        $this->assertEquals($fileStore, $this->getObjectAttribute($imageCommand, 'fileStore'));
        $this->assertEquals($uid, $this->getObjectAttribute($imageCommand, 'uid'));
    }

    public function test_setSuffix()
    {
        //arrange
        $imageCommand = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods()
            ->disableOriginalConstructor()
            ->getMock();
        $suffix = md5(microtime().rand());

        //act
        $imageCommand->setSuffix($suffix);

        //assert
        $this->assertEquals($suffix, $this->getObjectAttribute($imageCommand, 'suffix'));
    }

    public function test_setExt()
    {
        //arrange
        $imageCommand = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods()
            ->disableOriginalConstructor()
            ->getMock();
        $ext = md5(microtime().rand());

        //act
        $imageCommand->setExt($ext);

        //assert
        $this->assertEquals($ext, $this->getObjectAttribute($imageCommand, 'ext'));
    }

    public function test_reset()
    {
        //arrange
        $img = $this->getMockBuilder(Image::class)
            ->setMethods(array('reset'))
            ->getMock();
        $img->expects($this->once())->method('reset')->willReturn(null);
        $imageCommand = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods(array('setSuffix', 'setExt'))
            ->disableOriginalConstructor()
            ->getMock();

        $imageCommand->expects($this->once())->method('setSuffix')->willReturnCallback(function($suffix){
            $this->assertNull($suffix);
        });

        $imageCommand->expects($this->once())->method('setExt')->willReturnCallback(function($ext){
            $this->assertNull($ext);
        });

        $this->setObjectAttribute($imageCommand, 'image', $img);

        //act
        $imageCommand->reset();

    }

    public function test_append()
    {
        //arrange
        $imageCommand = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods()
            ->disableOriginalConstructor()
            ->getMock();

        //act
        $imageCommand->append('test1', 1, 2, 3, 4, 5);
        $imageCommand->append('test2', 'foo', 'bar');
        $imageCommand->append('test3');

        //assert
        $this->assertEquals(
            array(
                array('test1', 1, 2, 3, 4, 5),
                array('test2', 'foo', 'bar'),
                array('test3'),
            ),
            $this->getObjectAttribute($imageCommand, 'commands')
        );
    }

    public function test_getWidth()
    {
        //arrange
        $img = $this->getMockBuilder(Image::class)
            ->setMethods(array('getWidth'))
            ->getMock();
        $img->expects($this->once())->method('getWidth')->willReturn(100);
        $imageCommand = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods()
            ->disableOriginalConstructor()
            ->getMock();

        $this->setObjectAttribute($imageCommand, 'image', $img);

        //act
        $result = $imageCommand->getWidth();

        //assert
        $this->assertEquals(100, $result);
    }

    public function test_getHeight()
    {
        //arrange
        $img = $this->getMockBuilder(Image::class)
            ->setMethods(array('getHeight'))
            ->getMock();
        $img->expects($this->once())->method('getHeight')->willReturn(200);
        $imageCommand = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods()
            ->disableOriginalConstructor()
            ->getMock();

        $this->setObjectAttribute($imageCommand, 'image', $img);

        //act
        $result = $imageCommand->getHeight();

        //assert
        $this->assertEquals(200, $result);
    }

    public function test_getMime()
    {
        //arrange
        $img = $this->getMockBuilder(Image::class)
            ->setMethods(array('mime'))
            ->getMock();
        $img->expects($this->once())->method('mime')->willReturn('image/jpeg');
        $imageCommand = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods()
            ->disableOriginalConstructor()
            ->getMock();

        $this->setObjectAttribute($imageCommand, 'image', $img);

        //act
        $result = $imageCommand->getMime();

        //assert
        $this->assertEquals('image/jpeg', $result);
    }
}