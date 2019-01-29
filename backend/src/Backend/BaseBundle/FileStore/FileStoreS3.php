<?php
namespace Backend\BaseBundle\FileStore;
use Aws\S3\S3Client;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @DI\Service("file_store.s3")
 */
class FileStoreS3 implements FileStoreInterface
{

    protected $bucket;

    /** @var  S3Client */
    protected $client;
    protected $uidGenerator;
    protected $webUploadPrefix;

    public function __construct()
    {
        $this->uidGenerator = new Uid\RandomUidGenerator(static::class);
    }

    /**
     * @DI\InjectParams({
     *     "bucket" = @DI\Inject("%aws_s3_bucket%")
     * })
     */
    public function injectBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * @DI\InjectParams({
     *     "webUploadPrefix" = @DI\Inject("%web.upload.prefix%")
     * })
     */
    public function injectWebUploadPrefix($webUploadPrefix)
    {
        if($webUploadPrefix == ''){
            $this->webUploadPrefix = '';
            return;
        }
        $this->webUploadPrefix = str_replace(array('//'), array('/'), $webUploadPrefix.'/');
    }

    /**
     * @DI\InjectParams({
     *     "client" =  @DI\Inject("aws.s3", required = false)
     * })
     */
    public function injectS3Client(S3Client $client = null)
    {
        $this->client = $client;
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
        $this->createFolder();
        $fileKey = ($suffix == null) ? "$uid.$ext" : "$uid.$suffix.$ext";
        $file = new File($pathName);
        $options = array(
            'Bucket' => $this->bucket,
            'Key' => "{$this->webUploadPrefix}{$fileKey}",
            'Body' => fopen($pathName, 'r+'),
            'ContentType' => $file->getMimeType(),
            'CacheControl' => 'max-age=604800',
            'ACL' => 'public-read',
        );

        if($downloadName !== null){
            unset($options['ContentType']);
            $options["ContentDisposition"] = sprintf("attachment; filename*=UTF-8''%s", urlencode($downloadName));
        }

        $this->client->putObject($options);
    }

    /**
     * @param string $uid
     * @param string $suffix
     * @param string $ext
     * @return bool
     */
    public function delete($uid, $suffix, $ext)
    {
        $this->client->deleteObject(array(
            'Bucket' => $this->bucket,
            'Key' => ($suffix == null) ? "$uid.$ext" : "$uid.$suffix.$ext",
        ));
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
        return null;
    }

    protected function createFolder()
    {
        if($this->webUploadPrefix == ''){
            return;
        }
        if($this->client->doesObjectExist($this->bucket, $this->webUploadPrefix)){
            return;
        }
        $this->client->putObject(array(
            'Bucket' => $this->bucket,
            'Key' => $this->webUploadPrefix,
            'Body' => '',
            'ACL' => 'public-read',
        ));
    }
}
