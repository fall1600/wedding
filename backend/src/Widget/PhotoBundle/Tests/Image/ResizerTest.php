<?php
namespace Widget\PhotoBundle\Tests\Image;

use Backend\BaseBundle\FileStore\FileStoreInterface;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Widget\PhotoBundle\Image\Builder\AbstractBuilder;
use Widget\PhotoBundle\Image\Resizer;
use Widget\PhotoBundle\Image\Bridge\ImageManagerBridge;
use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @group unit
 */
class ResizerTest extends BaseTestCase
{
    public function test___construct()
    {
        //arrange
        $mockFileStore = new MockFileStore();
        $imageManager = $this->getMockBuilder(ImageManagerBridge::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

        //act
        $resizer = new Resizer($mockFileStore, $imageManager);

        //assert
        $this->assertEquals($mockFileStore, $this->getObjectAttribute($resizer, 'fileStore'));
        $this->assertEquals($imageManager, $this->getObjectAttribute($resizer, 'imageManager'));
    }

    public function test_resizeFile()
    {
        //arrange
        $index = 0;
        $resizeDefine = array(
            array('suffix' => 'define1'),
            array('suffix' => 'define2'),
        );
        $resizeDefineResult = array(
            array('suffix' => 'define1'),
            array('suffix' => 'define2'),
            array('suffix' => 'origin', 'type' => 'copy'),
        );
        $file = $this->getMockBuilder(File::class)
            ->setMethods(array('getPathname', 'getMimeType'))
            ->disableOriginalConstructor()
            ->getMock();
        $file
            ->expects($this->any())
            ->method('getPathname')
            ->willReturn('/path/to/file.jpg')
            ;
        $fileStore = new MockFileStore();
        $image = $this->getMockBuilder(ImageCommandBridge::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
        $imageManager = $this->getMockBuilder(ImageManagerBridge::class)
            ->setMethods(array('make'))
            ->disableOriginalConstructor()
            ->getMock();
        $imageManager
            ->expects($this->once())
            ->method('make')
            ->willReturnCallback(function(File $fileForTest, FileStoreInterface $fileStoreForTest, $uid) use($image, $file, $fileStore){
                $this->assertEquals($file, $fileForTest);
                $this->assertEquals($fileStore, $fileStoreForTest);
                return $image;
            });
        $imageBuilder = $this->getMockBuilder(AbstractBuilder::class)
            ->setMethods(array('build'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $imageBuilder
            ->expects($this->atLeastOnce())
            ->method('build')
            ->willReturnCallback(function(ImageCommandBridge $imageForTest, $define) use($image, $resizeDefineResult, &$index){
                $this->assertEquals($resizeDefineResult[$index++], $define);
                $this->assertEquals($imageForTest, $image);
                return 'ok';
            });
        $resizer = $this->getMockBuilder(Resizer::class)
            ->setMethods(array('getImageBuilder'))
            ->disableOriginalConstructor()
            ->getMock();
        $resizer
            ->expects($this->once())
            ->method('getImageBuilder')
            ->willReturn($imageBuilder);
        $this->setObjectAttribute($resizer, 'imageManager', $imageManager);
        $this->setObjectAttribute($resizer, 'fileStore', $fileStore);

        //act
        $result = $resizer->resizeFile($file, $resizeDefine, 'abcdefg');

        //assert
        $this->assertEquals(array(
            'define1' => 'ok',
            'define2' => 'ok',
            'origin' => 'ok',
        ), $result);
    }

    public function test_generateUid()
    {
        //arrange
        $resizer = $this->getMockBuilder(Resizer::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
        $fileStore = $this->getMockBuilder(MockFileStore::class)
            ->setMethods(array('generateUid'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileStore->expects($this->once())->method('generateUid')->willReturn('test_uid');
        $this->setObjectAttribute($resizer, 'fileStore', $fileStore);

        //act
        $result = $resizer->generateUid();

        //assert
        $this->assertEquals('test_uid', $result);
    }
}