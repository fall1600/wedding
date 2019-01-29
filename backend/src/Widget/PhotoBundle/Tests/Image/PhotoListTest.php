<?php
namespace Widget\PhotoBundle\Tests\Image;

use Backend\BaseBundle\Tests\Fixture\BaseWebTestCase;
use Widget\PhotoBundle\File\PhotoUploadFile;
use Widget\PhotoBundle\Image\PhotoList;
use Widget\PhotoBundle\Model\Photo;

class PhotoListTest extends BaseWebTestCase
{
    public function test___construct_is_a_ArrayObject()
    {
        //arrange
        $testArray = array(
            'a',
            'b',
            'c',
        );

        //act
        $photoList = new PhotoList($testArray);

        //assert
        $this->assertInstanceOf(\ArrayAccess::class, $photoList);
    }

    public function test___construct_setter()
    {
        //arrange
        $testArray = array(
            'a',
            'b',
            'c',
        );

        //act
        $photoList = new PhotoList($testArray);

        //assert
        $this->assertEquals($testArray, iterator_to_array($photoList));
    }

    public function test___construct_isModify()
    {
        //arrange
        $testArray = array(
            'a',
            'b',
            'c',
        );

        //act
        $photoList = new PhotoList($testArray);

        //assert
        $this->assertTrue($this->getObjectAttribute($photoList, 'modified'));
    }

    public function test_offsetSet_not_photo_object()
    {
        //arrange
        $photoList = new PhotoList(array());
        $this->setObjectAttribute($photoList, 'modified', false);

        //act
        $photoList->offsetSet(0, 'a');

        //assert
        $this->assertFalse($this->getObjectAttribute($photoList, 'modified'));
        $this->assertEquals(array(), iterator_to_array($photoList));
    }

    public function test_offsetSet_is_photo_object()
    {
        //arrange
        $photoList = new PhotoList(array());
        $photo = new Photo();
        $this->setObjectAttribute($photoList, 'modified', false);

        //act
        $photoList->offsetSet(0, $photo);

        //assert
        $this->assertTrue($this->getObjectAttribute($photoList, 'modified'));
        $this->assertEquals(array($photo), iterator_to_array($photoList));
    }

    public function test_offsetSet_is_photoupload_object()
    {
        //arrange
        $photoList = new PhotoList(array());
        $photo = new PhotoUploadFile(realpath(__DIR__.'/../Fixture/fakeimage.png'), 'testfile');
        $this->setObjectAttribute($photoList, 'modified', false);

        //act
        $photoList->offsetSet(0, $photo);

        //assert
        $this->assertTrue($this->getObjectAttribute($photoList, 'modified'));
        $this->assertEquals(array($photo), iterator_to_array($photoList));
    }

    public function test_offsetUnset()
    {
        //arrange
        $testArray = array('a', 'b', 'c');
        $photoList = new PhotoList($testArray);
        $this->setObjectAttribute($photoList, 'modified', false);
        unset($testArray[0]);

        //act
        $photoList->offsetUnset(0);

        //assert
        $this->assertTrue($this->getObjectAttribute($photoList, 'modified'));
        $this->assertEquals($testArray, iterator_to_array($photoList));
    }

    public function test_append_not_photo_object()
    {
        //arrange
        $photoList = new PhotoList(array());
        $this->setObjectAttribute($photoList, 'modified', false);

        //act
        $photoList->append('a');

        //assert
        $this->assertFalse($this->getObjectAttribute($photoList, 'modified'));
        $this->assertEquals(array(), iterator_to_array($photoList));
    }

    public function test_append_is_photo_object()
    {
        //arrange
        $photoList = new PhotoList(array());
        $photo = new Photo();
        $this->setObjectAttribute($photoList, 'modified', false);

        //act
        $photoList->append($photo);

        //assert
        $this->assertTrue($this->getObjectAttribute($photoList, 'modified'));
        $this->assertEquals(array($photo), iterator_to_array($photoList));
    }

    public function test_append_is_photoupload_object()
    {
        //arrange
        $photoList = new PhotoList(array());
        $photo = new PhotoUploadFile(realpath(__DIR__.'/../Fixture/fakeimage.png'), 'testfile');
        $this->setObjectAttribute($photoList, 'modified', false);

        //act
        $photoList->append($photo);

        //assert
        $this->assertTrue($this->getObjectAttribute($photoList, 'modified'));
        $this->assertEquals(array($photo), iterator_to_array($photoList));
    }

    public function test_isModified_true()
    {
        //arrange
        $photoList = new PhotoList(array());
        $this->setObjectAttribute($photoList, 'modified', true);

        //act
        $result = $photoList->isModified();

        //assert
        $this->assertTrue($result);
    }

    public function test_isModified_false()
    {
        //arrange
        $photoList = new PhotoList(array());
        $this->setObjectAttribute($photoList, 'modified', false);

        //act
        $result = $photoList->isModified();

        //assert
        $this->assertFalse($result);
    }

    public function test_save_not_modify()
    {
        //arrange
        $mockPhotoUpload = $this->getMockBuilder(PhotoUploadFile::class)
            ->setMethods(array('makePhoto'))
            ->disableOriginalConstructor()
            ->getMock();
        $mockPhotoUpload
            ->expects($this->never())
            ->method('makePhoto')
            ->willReturn(null)
            ;
        $photoList = new PhotoList(array($mockPhotoUpload));
        $this->setObjectAttribute($photoList, 'modified', false);

        //act
        $photoList->save();

        //assert
        $this->assertEquals(array($mockPhotoUpload), iterator_to_array($photoList));
    }

    public function test_save_is_modify_con_is_not_null()
    {
        //arrange
        $con = \Propel::getConnection();
        $mockPhoto = $this->getMockBuilder(Photo::class)
            ->setMethods(array('save'))
            ->getMock();
        $mockPhoto
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function($conForTest) use($con){
                $this->assertEquals($con, $conForTest);
            });
        $mockPhotoUpload = $this->getMockBuilder(PhotoUploadFile::class)
            ->setMethods(array('makePhoto'))
            ->disableOriginalConstructor()
            ->getMock();
        $mockPhotoUpload
            ->expects($this->once())
            ->method('makePhoto')
            ->willReturn($mockPhoto)
        ;
        $photoList = new PhotoList(array($mockPhotoUpload));
        $this->setObjectAttribute($photoList, 'modified', true);

        //act
        $photoList->save($con);

        //assert
        $this->assertEquals(array($mockPhoto), iterator_to_array($photoList));
    }

    public function test_save_is_modify_con_is_null()
    {
        //arrange
        $mockPhoto = $this->getMockBuilder(Photo::class)
            ->setMethods(array('save'))
            ->getMock();
        $mockPhoto
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(function($conForTest){
                $this->assertNull($conForTest);
            });
        $mockPhotoUpload = $this->getMockBuilder(PhotoUploadFile::class)
            ->setMethods(array('makePhoto'))
            ->disableOriginalConstructor()
            ->getMock();
        $mockPhotoUpload
            ->expects($this->once())
            ->method('makePhoto')
            ->willReturn($mockPhoto)
        ;
        $photoList = new PhotoList(array($mockPhotoUpload));
        $this->setObjectAttribute($photoList, 'modified', true);

        //act
        $photoList->save();

        //assert
        $this->assertEquals(array($mockPhoto), iterator_to_array($photoList));
    }

    public function test_getMasterPhoto_is_not_exists()
    {
        //arrange
        $testArray = array(
            new Photo(),
            new Photo(),
            new Photo(),
        );
        $photoList = new PhotoList($testArray);
        $this->setObjectAttribute($photoList, 'masterIndex', 3);

        //act
        $masterPhoto = $photoList->getMasterPhoto();

        //assert
        $this->assertNull($masterPhoto);
    }

    public function test_getMasterPhoto_is_exist()
    {
        //arrange
        $photo = new Photo();
        $testArray = array(
            new Photo(),
            $photo,
            new Photo(),
        );
        $photoList = new PhotoList($testArray);
        $this->setObjectAttribute($photoList, 'masterIndex', 1);

        //act
        $masterPhoto = $photoList->getMasterPhoto();

        //assert
        $this->assertTrue($photo === $masterPhoto);
    }

}