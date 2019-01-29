<?php
namespace Widget\PhotoBundle\Tests\Image\Builder;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Widget\PhotoBundle\Image\Builder\FitResizerBuilder;
use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

/**
 * @group unit
 */
class FitResizerBuilderTest extends BaseTestCase
{

    public function test_build_need_resize()
    {
        //arrange
        $nextBuilder = $this->getMockBuilder(FitResizerBuilder::class)
            ->setMethods(array('build'))
            ->getMock();
        $nextBuilder
            ->expects($this->once())
            ->method('build')
            ->willReturn(array('nextBuilderReturn' => true));
        $builder = $this->getMockBuilder(FitResizerBuilder::class)
            ->setMethods()
            ->setConstructorArgs(array($nextBuilder))
            ->getMock();
        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->setMethods(array(
                'setExt',
                'setSuffix',
                'append',
            ))
            ->disableOriginalConstructor()
            ->getMock();
        $image->expects($this->once())->method('setExt')->willReturnCallback(function($ext){
            $this->assertEquals('jpg', $ext);
        });
        $image->expects($this->once())->method('setSuffix')->willReturnCallback(function($suffix){
            $this->assertEquals('suffix', $suffix);
        });
        $image->expects($this->once())->method('append')->willReturnCallback(function($command, $width, $height){
            $this->assertEquals('resize', $command);
            $this->assertEquals(300, $width);
            $this->assertEquals(400, $height);
        });

        $resizeDefine = array(
            'type' => 'fit',
            'suffix' => 'suffix',
            'width' => 300,
            'height' => 400,
        );

        //act
        $result = $builder->build($image, $resizeDefine);

        //assert
        $this->assertEquals(array(
            'resolution' => array(300, 400),
            'mime' => 'image/jpeg',
            'ext' => 'jpg',
            'suffix' => 'suffix',
            'nextBuilderReturn' => true,
        ), $result);
    }

}