<?php
/**
 * Created by PhpStorm.
 * User: bubble
 * Date: 2016/12/13
 * Time: 下午 3:23
 */

namespace Widget\PhotoBundle\Form\Transformer;


use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Form\DataTransformerInterface;
use Widget\PhotoBundle\Image\PhotoList;
use Widget\PhotoBundle\Model\Photo;
use Widget\PhotoBundle\Model\PhotoQuery;

/**
 * @DI\Service("widget_photo.photolist.transformer")
 */
class ModelPhotoListTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value;
    }

    /**
     * @param mixed $array
     * @return PhotoList
     */
    public function reverseTransform($array)
    {
        $photoList = new PhotoList();

        if(!isset($array['photos']) || !is_array($array['photos'])){
            return $photoList;
        }

        foreach ($array['photos'] as $photoDefine){
            if($photo = $this->findPhoto($photoDefine)){
                $photoList->append($photo);
            }
        }
        return $photoList;
    }

    /**
     * @param $photoDefine
     * @return Photo|null
     */
    protected function findPhoto($photoDefine)
    {
        if(!isset($photoDefine['_uid'])){
            return null;
        }
        return PhotoQuery::create()->findOneByUid($photoDefine['_uid']);
    }
}