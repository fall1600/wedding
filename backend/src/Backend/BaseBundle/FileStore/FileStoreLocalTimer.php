<?php
namespace Backend\BaseBundle\FileStore;

use Symfony\Component\Filesystem\Filesystem;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;

/**
 * @Service("file_store.local.timer")
 */
class FileStoreLocalTimer extends FileStoreLocalRandom
{
    /**
     * @InjectParams({
     *    "storePath" = @Inject("%file.store.path%"),
     *    "pathLevel" = @Inject("%file.store.path_level%")
     * })
     */
    public function __construct($storePath, $pathLevel, Filesystem $filesystem)
    {
        $this->uidGenerator = new Uid\TimerUidGenerator();
        $this->storePath = $storePath;
        $this->filesystem = $filesystem;
    }

    protected function makeDir($uid)
    {
        list($timer, $rand) = explode('-', $uid);
        $dir = strftime("{$this->storePath}/%Y/%m/%d", floor($timer/1000));
        return $dir;
    }
}