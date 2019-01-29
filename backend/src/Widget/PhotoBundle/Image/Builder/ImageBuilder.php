<?php
namespace Widget\PhotoBundle\Image\Builder;

use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

class ImageBuilder extends AbstractBuilder
{

    public function build(ImageCommandBridge $image, array $resizeDefine)
    {
        return array_merge(array('filesize' => $image->execute()), $this->doNext($image, $resizeDefine));
    }

}