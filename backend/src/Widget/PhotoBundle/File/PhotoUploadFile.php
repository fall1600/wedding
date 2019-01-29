<?php
namespace Widget\PhotoBundle\File;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Widget\PhotoBundle\Model;
use Widget\PhotoBundle\Image\Resizer;

class PhotoUploadFile extends UploadedFile
{
    /** @var  Model\PhotoConfig */
    protected $photoConfig;

    /** @var  Resizer */
    protected $resizer;

    protected $uid;

    /**
     * @param File $file
     * @return PhotoUploadFile
     */
    static public function createFromUploadFile(File $file, Resizer $resizer, Model\PhotoConfig $photoConfig)
    {
        if($file instanceof UploadedFile){
            $originName = $file->getClientOriginalName();
            $error = $file->getError();
        }
        else{
            $originName = $file->getPathname();
            $error = '';
        }
        $newfile = new static($file->getPathname(), $originName, $file->getMimeType(), $file->getSize(), $error);
        return $newfile->setResizer($resizer)->setPhotoConfig($photoConfig);
    }

    public function setPhotoConfig(Model\PhotoConfig $photoConfig)
    {
        $this->photoConfig = $photoConfig;
        return $this;
    }

    public function setResizer(Resizer $resizer)
    {
        $this->resizer = $resizer;
        $this->uid = $this->resizer->generateUid();
        return $this;
    }

    /**
     * @return Model\Photo
     */
    public function makePhoto()
    {
        if(!$this->resizer){
            throw new \BadMethodCallException('no resizer injected!');
        }
        $info = $this->resizer->resizeFile($this, $this->photoConfig->getConfig(), $this->uid);
        $photo = new Model\Photo();
        $photo->setInfo($info);
        $photo->setUid($this->uid);
        return $photo;
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function __sleep()
    {
        return array_diff(array_keys(get_object_vars($this)), array('photoConfig', 'resizer', 'uid'));
    }
}