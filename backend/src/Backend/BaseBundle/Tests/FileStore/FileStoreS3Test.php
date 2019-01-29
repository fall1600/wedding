<?php
namespace Backend\BaseBundle\Tests\FileStore;

use Backend\BaseBundle\FileStore\FileStoreLocalTimer;
use Backend\BaseBundle\FileStore\FileStoreS3;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Backend\BaseBundle\FileStore\Uid\UidGeneratorInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @group unit
 */
class S3Test extends BaseTestCase
{
    public function test_webPath()
    {
        //arrange
        $uid = 'some_uid';
        $bucket = 'some_bucket';

        $fileStore = new FileStoreS3();
        $this->setObjectAttribute($fileStore, 'bucket', $bucket);

        //act
        $result = $fileStore->webPath($uid);

        //assert
        $this->assertNull($result);
    }

}