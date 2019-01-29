<?php
namespace Widget\PhotoBundle\Image\Bridge;

use Intervention\Image\Image;
use Backend\BaseBundle\FileStore\FileStoreInterface;

class ImageCommandBridge
{
    /** @var  Image */
    protected $image;

    /** @var  FileStoreInterface */
    protected $fileStore;

    /** @var  string */
    protected $uid;

    protected $suffix;

    protected $ext;

    protected $commands = array();

    public function __construct(Image $image, FileStoreInterface $fileStore, $uid)
    {
        $this->fileStore = $fileStore;
        $this->image = $image;
        $this->uid = $uid;
        $image->backup();
    }

    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    public function setExt($ext)
    {
        $this->ext = $ext;
    }

    public function reset()
    {
        $this->setSuffix(null);
        $this->setExt(null);
        $this->image->reset();
    }

    public function append($command)
    {
        $args = func_get_args();
        $this->commands[] = $args;
        return $this;
    }

    public function execute()
    {
        foreach($this->commands as $args){
            $command = array_shift($args);
            call_user_func_array(array($this->image, $command), $args);
        }
        $this->commands = array();
        
        $tempName = tempnam(sys_get_temp_dir(), "image-command-");
        $this->image->save($tempName);
        $this->fileStore->write($this->uid, $this->suffix, $this->ext, $tempName);
        $size = filesize($tempName);
        @unlink($tempName);
        $this->reset();
        return $size;
    }

    public function getWidth()
    {
        return $this->image->getWidth();
    }

    public function getHeight()
    {
        return $this->image->getHeight();
    }

    public function getMime()
    {
        return $this->image->mime();
    }
}