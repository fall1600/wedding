<?php
namespace Widget\PhotoBundle\Tests\Image\Builder;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Widget\PhotoBundle\Image\Builder\CopyBuilder;
use Widget\PhotoBundle\Image\Builder\CropResizerBuilder;
use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

/**
 * @group unit
 */
class CopyBuilderTest extends BaseTestCase
{

    public function test_build_invalid_type()
    {
        //arrange
        $nextResizer = $this->getMockBuilder(CopyBuilder::class)
            ->setMethods(array('build'))
            ->getMock();
        $nextResizer
            ->expects($this->once())
            ->method('build')
            ->willReturn(array('nextBuilderReturn' => true));

        $resizer = $this->getMockBuilder(CopyBuilder::class)
            ->setMethods()
            ->setConstructorArgs(array($nextResizer))
            ->getMock();
        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        //act
        $result = $resizer->build($image, array('type' => 'badtype'));

        //assert
        $this->assertEquals(array('nextBuilderReturn' => true), $result);
    }

    public function test_build_valid_type()
    {
        //arrange
        $resizeDefine = array(
            'type' => 'copy',
            'width' => 10,
            'height' => 20,
            'suffix' => 'large',
        );
        $nextResizer = $this->getMockBuilder(CopyBuilder::class)
            ->setMethods(array('build'))
            ->disableOriginalConstructor()
            ->getMock();
        $nextResizer
            ->expects($this->once())
            ->method('build')
            ->willReturn(array('nextBuilderReturn' => true));

        $resizer = $this->getMockBuilder(CopyBuilder::class)
            ->setMethods(array('getOriginOutput'))
            ->setConstructorArgs(array($nextResizer))
            ->getMock();
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
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

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

}