<?php
namespace Widget\PhotoBundle\Tests\Image\Builder;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Widget\PhotoBundle\Image\Builder\OutBoxResizerBuilder;
use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

/**
 * @group unit
 */
class OutBoxResizerBuilderTest extends BaseTestCase
{
    /**
     * @dataProvider dataProvider_test_getResizedSize
     */
    public function test_getResizedSize($sWidth, $sHeight, $tWidth, $tHeight, $aResult)
    {
        //arrange
        $resizer = new OutBoxResizerBuilder();

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
                'aResult' => array(200, 400),
            ),
            array(
                'sWidth' => 200,
                'sHeight' => 400,
                'tWidth' => 100,
                'tHeight' => 50,
                'aResult' => array(100, 200),
            ),
            array(
                'sWidth' => 200,
                'sHeight' => 400,
                'tWidth' => 30,
                'tHeight' => 200,
                'aResult' => array(100, 200),
            ),
        );
    }


    public function test_build_no_process()
    {
        //arrange
        $nextBuilder = $this->getMockBuilder(OutBoxResizerBuilder::class)
            ->setMethods(array('build'))
            ->getMock();
        $nextBuilder
            ->expects($this->once())
            ->method('build')
            ->willReturn(array('nextBuilderReturn' => true));
        $builder = $this->getMockBuilder(OutBoxResizerBuilder::class)
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
            'type' => 'not-outbox',
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
        $nextBuilder = $this->getMockBuilder(OutBoxResizerBuilder::class)
            ->setMethods(array('build'))
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
            'type' => 'outbox',
            'suffix' => 'suffix',
            'width' => 300,
            'height' => 400,
        );
        $builder = $this->getMockBuilder(OutBoxResizerBuilder::class)
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