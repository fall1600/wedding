<?php
namespace Widget\PhotoBundle\Tests\Image\Builder;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Widget\PhotoBundle\Image\Builder\CropResizerBuilder;
use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

/**
 * @group unit
 */
class CropResizerBuilderTest extends BaseTestCase
{
    public function test_calculateCropPos()
    {
        //arrange
        $resizer = new CropResizerBuilder();
        $sWidth = 100;
        $sHeight = 80;
        $tWidth = 50;
        $tHeight = 40;

        //act
        $result = $this->callObjectMethod($resizer, 'calculateCropPos', $sWidth, $sHeight, $tWidth, $tHeight);

        //assert
        $this->assertEquals(array(25, 20), $result);
    }

    public function test_isSizeValid_over_width()
    {
        //arrange
        $resizer = new CropResizerBuilder();
        $sWidth = 100;
        $sHeight = 100;
        $tWidth = 200;
        $tHeight = 50;

        //act
        $result = $this->callObjectMethod($resizer, 'isSizeValid', $sWidth, $sHeight, $tWidth, $tHeight);

        //assert
        $this->assertFalse($result);
    }

    public function test_isSizeValid_over_height()
    {
        //arrange
        $resizer = new CropResizerBuilder();
        $sWidth = 100;
        $sHeight = 100;
        $tWidth = 50;
        $tHeight = 200;

        //act
        $result = $this->callObjectMethod($resizer, 'isSizeValid', $sWidth, $sHeight, $tWidth, $tHeight);

        //assert
        $this->assertFalse($result);
    }

    public function test_isSizeValid_valid()
    {
        //arrange
        $resizer = new CropResizerBuilder();
        $sWidth = 100;
        $sHeight = 100;
        $tWidth = 50;
        $tHeight = 50;

        //act
        $result = $this->callObjectMethod($resizer, 'isSizeValid', $sWidth, $sHeight, $tWidth, $tHeight);

        //assert
        $this->assertTrue($result);
    }

    public function test_isSizeValid_equal()
    {
        //arrange
        $resizer = new CropResizerBuilder();
        $sWidth = 100;
        $sHeight = 100;
        $tWidth = 100;
        $tHeight = 100;

        //act
        $result = $this->callObjectMethod($resizer, 'isSizeValid', $sWidth, $sHeight, $tWidth, $tHeight);

        //assert
        $this->assertTrue($result);
    }

    public function test_build_invalid_type()
    {
        //arrange
        $nextResizer = $this->getMockBuilder(CropResizerBuilder::class)
            ->setMethods(array('build'))
            ->getMock();
        $nextResizer
            ->expects($this->once())
            ->method('build')
            ->willReturn(array('nextBuilderReturn' => true));

        $resizer = $this->getMockBuilder(CropResizerBuilder::class)
            ->setMethods(array('isSizeValid'))
            ->setConstructorArgs(array($nextResizer))
            ->getMock();
        $resizer
            ->expects($this->never())
            ->method('isSizeValid');
        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        //act
        $result = $resizer->build($image, array('type' => 'badtype'));

        //assert
        $this->assertEquals(array('nextBuilderReturn' => true), $result);
    }

    public function test_build_valid_type_invalid_size()
    {
        //arrange
        $isSizeValid = false;
        $resizeDefine = array(
            'type' => 'crop',
            'width' => 10,
            'height' => 20,
            'suffix' => 'large',
        );
        $nextResizer = $this->getMockBuilder(CropResizerBuilder::class)
            ->setMethods(array('build'))
            ->getMock();
        $nextResizer
            ->expects($this->once())
            ->method('build')
            ->willReturn(array('nextBuilderReturn' => true));

        $resizer = $this->getMockBuilder(CropResizerBuilder::class)
            ->setMethods(array('isSizeValid', 'getOriginOutput'))
            ->setConstructorArgs(array($nextResizer))
            ->getMock();
        $resizer
            ->expects($this->once())
            ->method('isSizeValid')
            ->willReturnCallback(function($sWidth, $sHeight, $tWidth, $tHeight) use($isSizeValid){
                $this->assertEquals(100, $sWidth);
                $this->assertEquals(200, $sHeight);
                $this->assertEquals(10, $tWidth);
                $this->assertEquals(20, $tHeight);
                return $isSizeValid;
            });
        $resizer
            ->expects($this->once())
            ->method('getOriginOutput')
            ->willReturn(array(
                'resolution' => array(100, 200),
                'mime' => 'image/jpeg',
                'ext' => 'jpg',
                'suffix' => 'origin',
            ))
        ;

        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods(array('getWidth', 'getHeight'))
            ->disableOriginalConstructor()
            ->getMock();
        $image
            ->expects($this->atLeastOnce())
            ->method('getWidth')
            ->willReturn(100);
        $image
            ->expects($this->atLeastOnce())
            ->method('getHeight')
            ->willReturn(200);

        //act
        $result = $resizer->build($image, $resizeDefine);

        //assert
        $this->assertEquals(array(
            'nextBuilderReturn' => true,
            'resolution' => array(100, 200),
            'mime' => 'image/jpeg',
            'ext' => 'jpg',
            'suffix' => 'origin',
        ), $result);
    }

    public function test_build_valid_type_valid_size()
    {
        //arrange
        $isSizeValid = true;
        $resizeDefine = array(
            'type' => 'crop',
            'width' => 10,
            'height' => 20,
            'suffix' => 'large',
        );
        $cropPosition = array(5, 10);
        $nextResizer = $this->getMockBuilder(CropResizerBuilder::class)
            ->setMethods(array('build'))
            ->disableOriginalConstructor()
            ->getMock();
        $nextResizer
            ->expects($this->once())
            ->method('build')
            ->willReturn(array('nextBuilderReturn' => true));

        $resizer = $this->getMockBuilder(CropResizerBuilder::class)
            ->setMethods(array('isSizeValid', 'crop', 'calculateCropPos'))
            ->setConstructorArgs(array($nextResizer))
            ->getMock();
        $resizer
            ->expects($this->once())
            ->method('isSizeValid')
            ->willReturnCallback(function($sWidth, $sHeight, $tWidth, $tHeight) use($isSizeValid){
                $this->assertEquals(100, $sWidth);
                $this->assertEquals(200, $sHeight);
                $this->assertEquals(10, $tWidth);
                $this->assertEquals(20, $tHeight);
                return $isSizeValid;
            });
        $resizer
            ->expects($this->once())
            ->method('calculateCropPos')
            ->willReturnCallback(function($sWidth, $sHeight, $tWidth, $tHeight) use($cropPosition, $resizeDefine){
                $this->assertEquals(100, $sWidth);
                $this->assertEquals(200, $sHeight);
                $this->assertEquals(10, $resizeDefine['width']);
                $this->assertEquals(20, $resizeDefine['height']);
                return $cropPosition;
            });

        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods(array('getWidth', 'getHeight', 'append'))
            ->disableOriginalConstructor()
            ->getMock();
        $image
            ->expects($this->once())
            ->method('append')
            ->willReturnCallback(function($type, $width, $height, $x, $y) use($resizeDefine, $cropPosition){
                $this->assertEquals('crop', $type);
                $this->assertEquals($resizeDefine['width'], $width);
                $this->assertEquals($resizeDefine['height'], $height);
                $this->assertEquals($cropPosition, array($x, $y));
            });
        $image
            ->expects($this->atLeastOnce())
            ->method('getWidth')
            ->willReturn(100);
        $image
            ->expects($this->atLeastOnce())
            ->method('getHeight')
            ->willReturn(200);

        //act
        $result = $resizer->build($image, $resizeDefine);

        //assert
        $this->assertEquals(array(
            'nextBuilderReturn' => true,
            'resolution' => array(10, 20),
            'mime' => 'image/jpeg',
            'ext' => 'jpg',
            'suffix' => 'large',
        ), $result);
    }
}