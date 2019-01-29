<?php
namespace Widget\PhotoBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\File;
use Widget\PhotoBundle\File\PhotoUploadCropFile;
use Widget\PhotoBundle\File\PhotoUploadFile;
use Widget\PhotoBundle\Image\Resizer;
use Widget\PhotoBundle\Model;

class ViewPhotoTransformer implements DataTransformerInterface
{

    /** @var Model\PhotoConfig  */
    protected $config;

    /** @var Resizer  */
    protected $resizer;

    protected $crop;

    public function __construct(Resizer $resizer, Model\PhotoConfig $config = null, $crop = false)
    {
        $this->resizer = $resizer;
        $this->config = $config;
        $this->crop = $crop;
    }

    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        try {
            if (!$value['file']) {
                return isset($value['uid'])?$this->findPhoto($value['uid']):null;
            }
            if ($this->crop) {
                return  PhotoUploadCropFile::createFromUploadFile($value['file'], $this->resizer, $this->config, $value['x'], $value['y'], $value['w'], $value['h']);
            }
            return  PhotoUploadFile::createFromUploadFile($value['file'], $this->resizer, $this->config);
        } catch (\Exception $e){
            return null;
        }
    }

    protected function findPhoto($uid)
    {
        return Model\PhotoQuery::create()
            ->findOneByUid($uid);
    }
}