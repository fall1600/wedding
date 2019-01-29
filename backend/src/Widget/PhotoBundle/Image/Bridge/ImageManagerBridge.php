<?php
namespace Widget\PhotoBundle\Image\Bridge;

use Intervention\Image\ImageManager;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Symfony\Component\HttpFoundation\File\File;
use Backend\BaseBundle\FileStore\FileStoreInterface;
use Widget\PhotoBundle\File\PhotoUploadCropFile;

/**
 * @Service("widget_photo.image.manager")
 */
class ImageManagerBridge
{

    /** @var ImageManager  */
    protected $imageManager;

    /**
     * @InjectParams({
     *    "driver" = @Inject("%image.manager.driver%")
     * })
     */
    public function __construct($driver)
    {
        $this->imageManager = new ImageManager(array(
            'driver' => $driver,
        ));
    }

    /**
     * @return ImageCommand
     */
    public function make(File $file, FileStoreInterface $fileStore, $uid)
    {
        $image = $this->imageManager->make($file->getPathname());
        if($file instanceof PhotoUploadCropFile){
            $image->crop($file->getWidth(), $file->getHeight(), $file->getX(), $file->getY());
        }
        $imageCommand = new ImageCommandBridge($image, $fileStore, $uid);
        return $imageCommand;
    }
}