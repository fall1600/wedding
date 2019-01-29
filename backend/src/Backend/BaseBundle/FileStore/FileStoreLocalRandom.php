<?php
namespace Backend\BaseBundle\FileStore;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;

/**
 * @Service("file_store.local.random")
 */
class FileStoreLocalRandom implements FileStoreInterface
{
    protected $uidGenerator;
    protected $pathLevel;
    protected $storePath;
    /** @var Filesystem  */
    protected $filesystem;

    /**
     * @InjectParams({
     *    "storePath" = @Inject("%file.store.path%"),
     *    "pathLevel" = @Inject("%file.store.path_level%")
     * })
     */
    public function __construct($storePath, $pathLevel, Filesystem $filesystem)
    {
        $this->uidGenerator = new Uid\RandomUidGenerator(static::class);
        $this->pathLevel = $pathLevel;
        $this->storePath = $storePath;
        $this->filesystem = $filesystem;
    }

    /**
     * @param $uid
     * @param $suffix
     * @param $ext
     * @param $pathName
     * @param null $downloadName
     * @return mixed|void
     */
    public function write($uid, $suffix, $ext, $pathName, $downloadName = null)
    {
        $dir = $this->makeDir($uid);
        if(!$this->filesystem->exists($dir)) {
            $this->filesystem->mkdir($dir, 0755);
        }
        $targetPathname = ($suffix == null) ? "$dir/$uid.$ext" : "$dir/$uid.$suffix.$ext";
        $this->filesystem->copy($pathName, $targetPathname);
    }

    public function delete($uid, $suffix, $ext)
    {
        $dir = $this->makeDir($uid);
        $pathname = ($suffix == null) ? "$dir/$uid.$ext" : "$dir/$uid.$suffix.$ext";
        if(!$this->filesystem->exists($pathname)){
            return false;
        }
        $this->filesystem->remove($pathname);
        return true;
    }

    protected function makeDir($uid)
    {
        $dir = $this->storePath;
        for ($i = 0; $i < $this->pathLevel; $i++) {
            $dir.="/{$uid[$i]}";
        }
        return $dir;
    }

    /**
     * @return string
     */
    public function generateUid()
    {
        return $this->uidGenerator->generate();
    }

    public function webPath($uid)
    {
        $dir = $this->makeDir($uid);
        return substr($dir, strlen($this->storePath));
    }
}