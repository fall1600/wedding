<?php
namespace Widget\PhotoBundle\Image;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\File\File;
use Backend\BaseBundle\FileStore\FileStoreInterface;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Widget\PhotoBundle\Image\Builder\AbstractBuilder;

/**
 * @Service("widget_photo.image.resizer")
 */
class Resizer
{

    /** @var  FileStoreInterface */
    protected $fileStore;

    /** @var  Bridge\ImageManagerBridge */
    protected $imageManager;

    /**
     * @InjectParams({
     *     "fileStore" = @Inject("widget_photo.file_store"),
     *     "imageManager" = @Inject("widget_photo.image.manager"),
     * })
     */
    public function __construct(FileStoreInterface $fileStore, Bridge\ImageManagerBridge $imageManager)
    {
        $this->fileStore = $fileStore;
        $this->imageManager = $imageManager;
    }

    /**
     * @param File $file
     * @param array $resizeDefine
     * @return array
     */
    public function resizeFile(File $file, array $resizeDefine, $uid)
    {
        $output = array();

        $image = $this->imageManager->make($file, $this->fileStore, $uid);

        $imageBuilder = $this->getImageBuilder();

        foreach ($resizeDefine as $define) {
            $output[$define['suffix']] = $imageBuilder->build($image, $define);
        }

        $output['origin'] = $imageBuilder->build($image, array(
            'type' => 'copy',
            'suffix' => 'origin',
        ));

        return $output;
    }

    public function generateUid()
    {
        return $this->fileStore->generateUid();
    }

    /**
     * @return AbstractBuilder
     */
    protected function getImageBuilder()
    {
        return AbstractBuilder::make();
    }

}
