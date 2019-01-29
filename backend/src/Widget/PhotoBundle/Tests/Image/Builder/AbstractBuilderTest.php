<?php
namespace Widget\PhotoBundle\Tests\Image\Builder;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Widget\PhotoBundle\Image\Builder\AbstractBuilder;
use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;
use Widget\PhotoBundle\Image\Builder\CopyBuilder;
use Widget\PhotoBundle\Image\Builder\CropResizerBuilder;
use Widget\PhotoBundle\Image\Builder\FitResizerBuilder;
use Widget\PhotoBundle\Image\Builder\ImageBuilder;
use Widget\PhotoBundle\Image\Builder\InBoxResizerBuilder;
use Widget\PhotoBundle\Image\Builder\OutBoxResizerBuilder;
use Widget\PhotoBundle\Image\Builder\ResizeCropResizerBuilder;

/**
 * @group unit
 */
class AbstractBuilderTest extends BaseTestCase
{
    /**
     * @dataProvider dataProvider_test_mimeToExt
     */
    public function test_mimeToExt($mime, $ext)
    {
        //arrange
        $builder = $this->getMockBuilder(AbstractBuilder::class)
            ->setMethods(array('build'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        //act
        $result = $this->callObjectMethod($builder, 'mimeToExt', $mime);

        //assert
        $this->assertEquals($ext, $result);
    }

    public function dataProvider_test_mimeToExt()
    {
        return array(
            array(
                'mime' => 'image/jpeg',
                'ext' => 'jpg',
            ),
            array(
                'mime' => 'image/png',
                'ext' => 'png',
            ),
            array(
                'mime' => 'image/gif',
                'ext' => 'gif',
            ),
            array(
                'mime' => 'image/x-bmp',
                'ext' => 'bmp',
            ),
            array(
                'mime' => 'image/x-bad',
                'ext' => false,
            ),
        );
    }

    public function test___cnostruct()
    {
        //arrange
        $nextBuilder = $this->getMockBuilder(AbstractBuilder::class)
            ->setMethods(array('build'))
            ->setConstructorArgs(array())
            ->getMockForAbstractClass();

        //act
        $builder = $this->getMockBuilder(AbstractBuilder::class)
            ->setMethods(array('build'))
            ->setConstructorArgs(array($nextBuilder))
            ->getMockForAbstractClass();

        //assert
        $this->assertEquals($nextBuilder, $this->getObjectAttribute($builder, 'nextBuilder'));
    }

    public function test_doNext_no_next_builder()
    {
        //arrange
        $resizeDefine = array();
        $imageCommand = $this->getMockBuilder(ImageCommandBridge::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
        $builder = $this->getMockBuilder(AbstractBuilder::class)
            ->setMethods(array('build'))
            ->setConstructorArgs(array())
            ->getMockForAbstractClass();

        //act
        $result = $this->callObjectMethod($builder, 'doNext', $imageCommand, $resizeDefine);

        //assert
        $this->assertEquals(array(), $result);
    }

    public function test_doNext_with_next_builder()
    {
        //arrange
        $resizeDefine = array(1, 2, 3, 4);
        $imageCommand = $this->getMockBuilder(ImageCommandBridge::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
        $nextBuilder = $this->getMockBuilder(AbstractBuilder::class)
            ->setMethods(array('build'))
            ->setConstructorArgs(array())
            ->getMockForAbstractClass();
        $nextBuilder
            ->expects($this->once())
            ->method('build')
            ->willReturnCallback(function(ImageCommandBridge $image, $define) use($imageCommand, $resizeDefine){
                $this->assertEquals($imageCommand, $image);
                $this->assertEquals($resizeDefine, $define);
                return array('some_test');
            });
        $builder = $this->getMockBuilder(AbstractBuilder::class)
            ->setMethods(array('build'))
            ->setConstructorArgs(array($nextBuilder))
            ->getMockForAbstractClass();

        //act
        $result = $this->callObjectMethod($builder, 'doNext', $imageCommand, $resizeDefine);

        //assert
        $this->assertEquals(array('some_test'), $result);
    }

    public function test_make()
    {
        //arrange
        $builderChain = array(
            InBoxResizerBuilder::class,
            OutBoxResizerBuilder::class,
            FitResizerBuilder::class,
            CropResizerBuilder::class,
            ResizeCropResizerBuilder::class,
            CopyBuilder::class,
            ImageBuilder::class,
        );

        //act
        $result = array();
        $builder = AbstractBuilder::make();
        $result[] = get_class($builder);
        while($builder = $this->getObjectAttribute($builder, 'nextBuilder')){
            $result[] = get_class($builder);
        }

        //assert
        $this->assertEquals($builderChain, $result);
    }

    public function test_getOriginOutput()
    {
        //arrange
        $width = 100;
        $height = 200;
        $mime = 'image/png';
        $ext = 'png';
        $origin = array(
            'resolution' => array($width, $height),
            'mime' => $mime,
            'ext' => $ext,
            'suffix' => 'origin',
        );

        $imageCommand = $this->getMockBuilder(ImageCommandBridge::class)
            ->disableOriginalConstructor()
            ->setMethods(array(
                'getMime',
                'getWidth',
                'getHeight',
            ))
            ->getMock();
        $imageCommand
            ->expects($this->atLeastOnce())
            ->method('getMime')
            ->willReturn($mime)
            ;
        $imageCommand
            ->expects($this->atLeastOnce())
            ->method('getWidth')
            ->willReturn($width)
        ;
        $imageCommand
            ->expects($this->atLeastOnce())
            ->method('getHeight')
            ->willReturn($height)
        ;
        $builder = $this->getMockBuilder(AbstractBuilder::class)
            ->setMethods(array('mimeToExt', 'build'))
            ->getMockForAbstractClass();
        $builder
            ->expects($this->atLeastOnce())
            ->method('mimeToExt')
            ->willReturnCallback(function($mimeForTest) use($mime, $ext){
                $this->assertEquals($mime, $mimeForTest);
                return $ext;
            });

        //act
        $result = $this->callObjectMethod($builder, 'getOriginOutput', $imageCommand);

        //assert
        $this->assertEquals($origin, $result);
    }
}