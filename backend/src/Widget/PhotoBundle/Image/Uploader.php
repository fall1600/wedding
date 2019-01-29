<?php
namespace Widget\PhotoBundle\Image;


use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\File\File;
use Widget\PhotoBundle\File\PhotoUploadFile;

/**
 * @DI\Service("widget_photo.image.uploader")
 */
class Uploader
{
    /** @var  Resizer */
    protected $resizer;

    /** @var  PhotoConfigFinder */
    protected $photoConfigFinder;

    /**
     * @DI\InjectParams({
     *     "resizer" = @DI\Inject("widget_photo.image.resizer")
     * })
     */
    public function injectResizer(Resizer $resizer)
    {
        $this->resizer = $resizer;
    }

    /**
     * @DI\InjectParams({
     *    "photoConfigFinder" = @DI\Inject("widget.photo_bundle.config_finder")
     * })
     */
    public function injectPhotoConfigFinder(PhotoConfigFinder $photoConfigFinder)
    {
        $this->photoConfigFinder = $photoConfigFinder;
    }

    /**
     * @param File $uploadFile
     * @return \Widget\PhotoBundle\Model\Photo
     */
    public function makePhoto(File $uploadFile, $name)
    {
        $photoConfig = $this->photoConfigFinder->findConfig($name);
        $photoUploadFile = PhotoUploadFile::createFromUploadFile($uploadFile, $this->resizer, $photoConfig);
        return $photoUploadFile->makePhoto();
    }
}