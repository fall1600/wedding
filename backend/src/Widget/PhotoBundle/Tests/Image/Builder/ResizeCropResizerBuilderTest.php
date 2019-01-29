<?php
namespace Widget\PhotoBundle\Tests\Image\Builder;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;
use Widget\PhotoBundle\Image\Builder\ResizeCropResizerBuilder;

/**
 * @group unit
 */
class ResizeCropResizerBuilderTest extends BaseTestCase
{
    /**
     * @dataProvider dataProvider_test_getResizedSize
     */
    public function test_getResizedSize($sWidth, $sHeight, $tWidth, $tHeight, $aResult)
    {
        //arrange
        $resizer = $this->getMockBuilder(ResizeCropResizerBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        //act
        $result = $this->callObjectMethod($resizer, 'getResizedSize', $sWidth, $sHeight, $tWidth, $tHeight);

        //assert
        $this->assertEquals($aResult, $result);
    }

    public function dataProvider_test_getResizedSize()
    {
        return array(
            array(
                'sWidth' => 200,
                'sHeight' => 400,
                'tWidth' => 200,
                'tHeight' => 400,
                'aResult' => array(200, 400),
            ),
            array(
                'sWidth' => 200,
                'sHeight' => 400,
                'tWidth' => 400,
                'tHeight' => 600,
                'aResult' => array(300, 600),
            ),
            array(
                'sWidth' => 200,
                'sHeight' => 400,
                'tWidth' => 600,
                'tHeight' => 500,
                'aResult' => array(250, 500),
            ),
            array(
                'sWidth' => 400,
                'sHeight' => 200,
                'tWidth' => 400,
                'tHeight' => 200,
                'aResult' => array(400, 200),
            ),
            array(
                'sWidth' => 400,
                'sHeight' => 200,
                'tWidth' => 800,
                'tHeight' => 200,
                'aResult' => array(400, 200),
            ),
            array(
                'sWidth' => 400,
                'sHeight' => 200,
                'tWidth' => 600,
                'tHeight' => 800,
                'aResult' => array(600, 300),
            ),
        );
    }

    public function test_doResize()
    {
        //arrange
        $command = 'resize';
        $width = 300;
        $height = 200;
        $resizeDefine = array(
            'type' => 'resizecrop',
            'width' => $width,
            'height' => $height,
            'suffix' => 'large',
        );

        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods(array('append'))
            ->disableOriginalConstructor()
            ->getMock();
        $image
            ->expects($this->atLeastOnce())
            ->method('append')
            ->willReturnCallback(function($commandForTest, $widthForTest, $heightForTest) use($image, $command, $width, $height){
                $this->assertEquals($command, $commandForTest);
                $this->assertEquals($width, $widthForTest);
                $this->assertEquals($height, $heightForTest);
                return $image;
            });
        $resizer = $this->getMockBuilder(ResizeCropResizerBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        //act
        $result = $this->callObjectMethod($resizer, 'doResize', $image, $resizeDefine);

        //assert
    }

    public function test_doCrop()
    {
        //arrange
        $command = 'crop';
        $width = 300;
        $height = 200;
        $tWidth = 60;
        $tHeight = 70;
        $x = 1;
        $y = 2;

        $resizeDefine = array(
            'type' => 'resizecrop',
            'suffix' => 'suffix',
            'width' => $width,
            'height' => $height,
        );

        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods(array('append', 'getWidth', 'getHeight'))
            ->disableOriginalConstructor()
            ->getMock();
        $image
            ->expects($this->atLeastOnce())
            ->method('append')
            ->willReturnCallback(function($commandForTest, $widthForTest, $heightForTest, $xForTest, $yForTest) use($image, $command, $tWidth, $tHeight, $x, $y){
                $this->assertEquals($command, $commandForTest);
                $this->assertEquals($tWidth, $widthForTest);
                $this->assertEquals($tHeight, $heightForTest);
                $this->assertEquals($x, $xForTest);
                $this->assertEquals($y, $yForTest);
                return $image;
            })
        ;
        $image
            ->expects($this->atLeastOnce())
            ->method('getWidth')
            ->willReturn($width);
        $image
            ->expects($this->atLeastOnce())
            ->method('getHeight')
            ->willReturn($height);
        $resizer = $this->getMockBuilder(ResizeCropResizerBuilder::class)
            ->setMethods(array('getResizedSize', 'calculateCropPos'))
            ->disableOriginalConstructor()
            ->getMock();
        $resizer
            ->expects($this->atLeastOnce())
            ->method('getResizedSize')
            ->willReturnCallback(function($sWidthForTest, $sHeightForTest, $tWidthForTest, $tHeightForTest) use($tWidth, $tHeight, $resizeDefine, $width, $height){
                $this->assertEquals($resizeDefine['width'], $sWidthForTest);
                $this->assertEquals($resizeDefine['height'], $sHeightForTest);
                $this->assertEquals($tWidthForTest, $tWidthForTest);
                $this->assertEquals($tHeightForTest, $tHeightForTest);
                return array($tWidth, $tHeight);
            });
        $resizer
            ->expects($this->atLeastOnce())
            ->method('calculateCropPos')
            ->willReturnCallback(function($sWidthForTest, $sHeightForTest, $tWidthForTest, $tHeightForTest) use($resizeDefine, $tWidth, $tHeight, $x, $y){
                $this->assertEquals($tWidth, $tWidthForTest);
                $this->assertEquals($tHeight, $tHeightForTest);
                $this->assertEquals($resizeDefine['width'], $sWidthForTest);
                $this->assertEquals($resizeDefine['height'], $sHeightForTest);
                return array($x, $y);
            });

        //act
        $result = $this->callObjectMethod($resizer, 'doCrop', $image, $resizeDefine);

        //assert
    }

    public function test_build_invalid_type()
    {
        //arrange
        $nextResizer = $this->getMockBuilder(ResizeCropResizerBuilder::class)
            ->setMethods(array('build'))
            ->getMock();
        $nextResizer
            ->expects($this->once())
            ->method('build')
            ->willReturn(array('nextBuilderReturn' => true));

        $resizer = $this->getMockBuilder(ResizeCropResizerBuilder::class)
            ->setMethods(array('isSizeValid'))
            ->setConstructorArgs(array($nextResizer))
            ->getMock();
        $resizer
            ->expects($this->never())
            ->method('isSizeValid');
        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods()
            ->disableOriginalConstructor()
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
            'type' => 'resizecrop',
            'width' => 10,
            'height' => 20,
            'suffix' => 'large',
        );
        $nextResizer = $this->getMockBuilder(ResizeCropResizerBuilder::class)
            ->setMethods(array('build'))
            ->getMock();
        $nextResizer
            ->expects($this->once())
            ->method('build')
            ->willReturn(array('nextBuilderReturn' => true));

        $resizer = $this->getMockBuilder(ResizeCropResizerBuilder::class)
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
            'type' => 'resizecrop',
            'width' => 10,
            'height' => 20,
            'suffix' => 'large',
        );

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

        $nextResizer = $this->getMockBuilder(ResizeCropResizerBuilder::class)
            ->setMethods(array('build'))
            ->getMock();
        $nextResizer
            ->expects($this->once())
            ->method('build')
            ->willReturn(array('nextBuilderReturn' => true));

        $resizer = $this->getMockBuilder(ResizeCropResizerBuilder::class)
            ->setMethods(array('isSizeValid', 'doCrop', 'doResize'))
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
            ->method('doCrop')
            ->willReturnCallback(function($imageForTest, $resizeDefineForTest) use($image, $resizeDefine){
                $this->assertEquals($image, $imageForTest);
                $this->assertEquals($resizeDefine, $resizeDefineForTest);
            });
        $resizer
            ->expects($this->once())
            ->method('doResize')
            ->willReturnCallback(function($imageForTest, $resizeDefineForTest) use($image, $resizeDefine){
                $this->assertEquals($image, $imageForTest);
                $this->assertEquals($resizeDefine, $resizeDefineForTest);
            });

        //act
        $result = $resizer->build($image, $resizeDefine);

        //assert
        $this->assertEquals(array(
            'nextBuilderReturn' => true,
            'resolution' => array(10, 20),
            'suffix' => 'large',
            'mime' => 'image/jpeg',
            'ext' => 'jpg',
        ), $result);
    }
}