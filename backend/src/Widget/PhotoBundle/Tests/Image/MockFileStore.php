<?php
namespace Widget\PhotoBundle\Tests\Image;


use Backend\BaseBundle\FileStore\FileStoreInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @group unit
 */
class MockFileStore implements FileStoreInterface
{


    /**
     * @param string $uid
     * @param string $suffix
     * @param string $ext
     * @param string $pathName
     * @return bool
     */
    public function write($uid, $suffix, $ext, $pathName, $downloadName = null)
    {
        return false;
    }

    /**
     * @return string
     */
    public function generateUid()
    {
        return 'mock';
    }


    public function webPath($uid)
    {
        return "/$uid";
    }
}