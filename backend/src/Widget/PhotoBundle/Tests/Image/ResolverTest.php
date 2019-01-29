<?php
namespace Widget\PhotoBundle\Tests\Image;

use Backend\BaseBundle\FileStore\FileStoreInterface;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Widget\PhotoBundle\Image\Resolver;
use Widget\PhotoBundle\Model\Photo;

class MockFileStore implements FileStoreInterface
{
    public function write($uid, $suffix, $ext, $pathName, $downloadName = null)
    {
    }

    public function generateUid()
    {
    }

    public function webPath($uid)
    {
    }

    public function delete($uid, $suffix, $ext)
    {
    }
}

/**
 * @group unit
 */
class ResoverTest extends BaseTestCase
{
    public function test___construct()
    {
        //arrange
        $fileStore = new MockFileStore();
        $pathPrefix = 'test_prefix';

        //act
        $resolver = new Resolver($fileStore, $pathPrefix);

        //assert
        $this->assertEquals($fileStore, $this->getObjectAttribute($resolver, 'fileStore'));
        $this->assertEquals($pathPrefix, $this->getObjectAttribute($resolver, 'pathPrefix'));
    }

    public function test_resolveWebPath()
    {
        //arrange
        $info = array(
            'small' => array(
                'ext' => 'jpg',
                'suffix' => 'origin',
            ),
        );
        $uid = 'test_uid';
        $photo = $this->getMockBuilder(Photo::class)
            ->setMethods(array('getInfo', 'getUid'))
            ->disableOriginalConstructor()
            ->getMock();
        $photo
            ->expects($this->once())
            ->method('getInfo')
            ->willReturn($info);
        $photo
            ->expects($this->once())
            ->method('getUid')
            ->willReturn($uid);
        $resolver = $this->getMockBuilder(Resolver::class)
            ->setMethods(array('resolveWebPathFromUid'))
            ->disableOriginalConstructor()
            ->getMock();
        $resolver
            ->expects($this->once())
            ->method('resolveWebPathFromUid')
            ->willReturnCallback(function($uidForTest, $suffix, $ext) use($uid){
                $this->assertEquals($uid, $uidForTest);
                $this->assertEquals('origin', $suffix);
                $this->assertEquals('jpg', $ext);
                return 'test_web_path';
            });

        //act
        $result = $resolver->resolveWebPath($photo, 'small');

        //arrange
        $this->assertEquals('test_web_path', $result);
    }

    public function test_resolveWebPathFromUid()
    {
        //arrange
        $uid = 'test_uid';
        $suffix = 'small';
        $ext = 'png';
        $basePath = 'test_base_path';
        $pathPrefix = 'test_path_prefix';
        $fileStore = $this->getMockBuilder(MockFileStore::class)
            ->setMethods(array('webPath'))
            ->disableOriginalConstructor()
            ->getMock();
        $fileStore
            ->expects($this->once())
            ->method('webPath')
            ->willReturn($basePath);
        $resolver = $this->getMockBuilder(Resolver::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
        $this->setObjectAttribute($resolver, 'fileStore', $fileStore);
        $this->setObjectAttribute($resolver, 'pathPrefix', $pathPrefix);

        //act
        $result = $resolver->resolveWebPathFromUid($uid, $suffix, $ext);

        //assert
        $this->assertEquals("$pathPrefix$basePath/$uid.$suffix.$ext", $result);
    }
}