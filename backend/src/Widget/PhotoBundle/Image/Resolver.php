<?php
namespace Widget\PhotoBundle\Image;

use Backend\BaseBundle\FileStore\FileStoreInterface;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Widget\PhotoBundle\Model\Photo;

/**
 * @Service("widget_photo.image.resolver")
 */
class Resolver
{

    /** @var  FileStoreInterface */
    protected $fileStore;

    protected $pathPrefix;

    /**
     * @InjectParams({
     *     "fileStore" = @Inject("widget_photo.file_store"),
     *     "pathPrefix" = @Inject("%web.upload.prefix%"),
     * })
     */
    public function __construct(FileStoreInterface $fileStore, $pathPrefix)
    {
        $this->fileStore = $fileStore;
        $this->pathPrefix = $pathPrefix;
    }

    public function resolveWebPath(Photo $photo, $suffix)
    {
        $info = $photo->getInfo();
        return $this->resolveWebPathFromUid($photo->getUid(), $info[$suffix]['suffix'], $info[$suffix]['ext']);
    }

    public function resolveWebPathFromUid($uid, $suffix, $ext)
    {
        $basePath = $this->fileStore->webPath($uid);
        return "{$this->pathPrefix}$basePath/$uid.$suffix.$ext";
    }

}
