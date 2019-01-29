<?php
namespace Widget\PhotoBundle\Tests\Image\Builder;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Widget\PhotoBundle\Image\Builder\InBoxResizerBuilder;
use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

/**
 * @group unit
 */
class InBoxResizerBuilderTest extends BaseTestCase
{

    /**
     * @dataProvider dataProvider_test_getResizedSize
     */
    public function test_getResizedSize($sWidth, $sHeight, $tWidth, $tHeight, $aResult)
    {
        //arrange
        $resizer = $this->getMockBuilder(InBoxResizerBuilder::class)
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
                'aResult' => false,
            ),
            array(
                'sWidth' => 200,
                'sHeight' => 400,
                'tWidth' => 200,
                'tHeight' => 200,
                'aResult' => array(100, 200),
            ),
            array(
                'sWidth' => 200,
                'sHeight' => 400,
                'tWidth' => 100,
                'tHeight' => 50,
                'aResult' => array(25, 50),
            ),
            array(
                'sWidth' => 200,
                'sHeight' => 400,
                'tWidth' => 20,
                'tHeight' => 200,
                'aResult' => array(20, 40),
            ),
        );
    }

    public function test_build_need_resize()
    {
        //arrange
        $builder = $this->getMockBuilder(InBoxResizerBuilder::class)
            ->setMethods(array('getResizedSize', 'mimeToExt'))
            ->disableOriginalConstructor()
            ->getMock();
        $builder
            ->expects($this->once())
            ->method('getResizedSize')
            ->willReturnCallback(function($sWidth, $sHeight, $tWidth, $tHeight) {
                $this->assertEquals(array(700, 800), array($sWidth, $sHeight));
                $this->assertEquals(array(300, 400), array($tWidth, $tHeight));
                return array(100, 200);
            });
        $builder
            ->expects($this->never())
            ->method('mimeToExt')
            ->willReturn('specialext');
        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods(array(
                'getWidth',
                'getHeight',
                'getMime',
                'setExt',
                'setSuffix',
            ))
            ->disableOriginalConstructor()
            ->getMock();
        $image->expects($this->once())->method('getWidth')->willReturn(700);
        $image->expects($this->once())->method('getHeight')->willReturn(800);
        $image->expects($this->never())->method('getMime')->willReturn('image/png');
        $image->expects($this->once())->method('setExt')->willReturnCallback(function($ext){
            $this->assertEquals('jpg', $ext);
        });
        $image->expects($this->once())->method('setSuffix')->willReturnCallback(function($suffix){
            $this->assertEquals('suffix', $suffix);
        });
        $resizeDefine = array(
            'type' => 'inbox',
            'suffix' => 'suffix',
            'width' => 300,
            'height' => 400,
        );

        //act
        $result = $this->callObjectMethod($builder, 'doResize', $image, $resizeDefine);

        //assert
        $this->assertEquals(array(
            'resolution' => array(100, 200),
            'mime' => 'image/jpeg',
            'ext' => 'jpg',
            'suffix' => 'suffix',
        ), $result);
    }

    public function test_build_no_resize()
    {
        //arrange
        $origin = array(
            'resolution' => array(700, 800),
            'mime' => 'image/png',
            'ext' => 'specialext',
            'suffix' => 'origin',
        );

        $builder = $this->getMockBuilder(InBoxResizerBuilder::class)
            ->setMethods(array('getResizedSize', 'mimeToExt', 'getOriginOutput'))
            ->getMock();
        $builder
            ->expects($this->once())
            ->method('getResizedSize')
            ->willReturnCallback(function($sWidth, $sHeight, $tWidth, $tHeight) {
                $this->assertEquals(array(700, 800), array($sWidth, $sHeight));
                $this->assertEquals(array(300, 400), array($tWidth, $tHeight));
                return false;
            });
        $builder
            ->expects($this->never())
            ->method('mimeToExt')
            ->willReturn(null);
        $builder
            ->expects($this->once())
            ->method('getOriginOutput')
            ->willReturn($origin);
        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods(array(
                'getWidth',
                'getHeight',
            ))
            ->disableOriginalConstructor()
            ->getMock();

        $image->expects($this->atLeastOnce())->method('getWidth')->willReturn(700);
        $image->expects($this->atLeastOnce())->method('getHeight')->willReturn(800);

        $resizeDefine = array(
            'type' => 'inbox',
            'suffix' => 'suffix',
            'width' => 300,
            'height' => 400,
        );

        //act
        $result = $this->callObjectMethod($builder, 'doResize', $image, $resizeDefine);

        //assert
        $this->assertEquals($origin, $result);
    }

    public function test_build_no_process()
    {
        //arrange
        $nextBuilder = $this->getMockBuilder(InBoxResizerBuilder::class)
            ->setMethods(array('build'))
            ->getMock();
        $nextBuilder
            ->expects($this->once())
            ->method('build')
            ->willReturn(array('nextBuilderReturn' => true));
        $builder = $this->getMockBuilder(InBoxResizerBuilder::class)
            ->setMethods(array('doResize'))
            ->setConstructorArgs(array($nextBuilder))
            ->getMock();
        $builder
            ->expects($this->never())
            ->method('doResize');
        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        $resizeDefine = array(
            'type' => 'not-inbox',
            'suffix' => 'suffix',
            'width' => 300,
            'height' => 400,
        );

        //act
        $result = $builder->build($image, $resizeDefine);

        //assert
        $this->assertEquals(array(
            'nextBuilderReturn' => true,
        ), $result);
    }

    public function test_build_do_process()
    {
        //arrange
        $nextBuilder = $this->getMockBuilder(InBoxResizerBuilder::class)
            ->setMethods(array('build'))
            ->disableOriginalConstructor()
            ->getMock();
        $nextBuilder
            ->expects($this->once())
            ->method('build')
            ->willReturn(array('nextBuilderReturn' => true));
        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        $resizeDefine = array(
            'type' => 'inbox',
            'suffix' => 'suffix',
            'width' => 300,
            'height' => 400,
        );
        $builder = $this->getMockBuilder(InBoxResizerBuilder::class)
            ->setMethods(array('doResize'))
            ->setConstructorArgs(array($nextBuilder))
            ->getMock();
        $builder
            ->expects($this->once())
            ->method('doResize')
            ->willReturnCallback(function($rImage, $rResizeDefine) use($image, $resizeDefine){
                $this->assertEquals($image, $rImage);
                $this->assertEquals($resizeDefine, $rResizeDefine);
                return array(
                    'a' => 'a',
                    'b' => 'b',
                    'c' => 'c',
                );
            });

        //act
        $result = $builder->build($image, $resizeDefine);

        //assert
        $this->assertEquals(array(
            'a' => 'a',
            'b' => 'b',
            'c' => 'c',
            'nextBuilderReturn' => true,
        ), $result);
    }

}