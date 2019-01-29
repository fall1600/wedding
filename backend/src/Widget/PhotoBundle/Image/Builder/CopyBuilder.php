<?php
namespace Widget\PhotoBundle\Image\Builder;

use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

class CopyBuilder extends AbstractBuilder
{

    public function build(ImageCommandBridge $image, array $resizeDefine)
    {
        if($resizeDefine['type'] != 'copy'){
            return $this->doNext($image, $resizeDefine);
        }
        return array_merge($this->getOriginOutput($image), $this->doNext($image, $resizeDefine));
    }

}