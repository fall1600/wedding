<?php
namespace Widget\PhotoBundle\File;

use Symfony\Component\HttpFoundation\File\File;
use Widget\PhotoBundle\Model;
use Widget\PhotoBundle\Image\Resizer;

class PhotoUploadCropFile extends PhotoUploadFile
{
    protected $x;
    protected $y;
    protected $width;
    protected $height;

    /**
     * @param File $file
     * @param $x
     * @param $y
     * @param $width
     * @param $height
     * @return PhotoUploadCropFile
     */
    static public function createFromUploadFile(File $file, Resizer $resizer, Model\PhotoConfig $photoConfig, $x = null, $y = null, $width = null, $height = null)
    {
        $file = parent::createFromUploadFile($file, $resizer, $photoConfig);
        $file->setX(round($x));
        $file->setY(round($y));
        $file->setWidth(round($width));
        $file->setHeight(round($height));
        return $file;
    }

    public function setX($x)
    {
        $this->x = $x;
    }

    public function getX()
    {
        return $this->x;
    }

    public function setY($y)
    {
        $this->y = $y;
    }

    public function getY()
    {
        return $this->y;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getHeight()
    {
        return $this->height;
    }
}