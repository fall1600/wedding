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
 * @DI\Service("widget_photo.photo.transformer")
 */
class ModelPhotoTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return $value;
    }

    /**
     * @param mixed $photoDefine
     * @return Photo
     */
    public function reverseTransform($photoDefine)
    {

        if(!isset($photoDefine['_uid'])){
            return null;
        }
        return PhotoQuery::create()->findOneByUid($photoDefine['_uid']);
    }

}