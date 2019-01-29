<?php
namespace Backend\BaseBundle\FileStore;

interface FileStoreInterface
{
    /**
     * @param $uid
     * @param $suffix
     * @param $ext
     * @param $pathName
     * @param null $downloadName
     * @return mixed
     */
    public function write($uid, $suffix, $ext, $pathName, $downloadName = null);

    /**
     * @param string $uid
     * @param string $suffix
     * @param string $ext
     * @return bool
     */
    public function delete($uid, $suffix, $ext);

    /**
     * @return string
     */
    public function generateUid();

    public function webPath($uid);
}