<?php
namespace Widget\PhotoBundle\Image\Builder;

use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

class CropResizerBuilder extends AbstractBuilder
{

    public function build(ImageCommandBridge $image, array $resizeDefine)
    {
        if($resizeDefine['type'] != 'crop'){
            return $this->doNext($image, $resizeDefine);
        }

        if(!$this->isSizeValid($image->getWidth(), $image->getHeight(), $resizeDefine['width'], $resizeDefine['height'])) {
            return array_merge($this->getOriginOutput($image), $this->doNext($image, $resizeDefine));
        }

        list($x, $y) = $this->calculateCropPos($image->getWidth(), $image->getHeight(), $resizeDefine['width'], $resizeDefine['height']);
        $image->append('crop', $resizeDefine['width'], $resizeDefine['height'], $x, $y);
        $image->setExt('jpg');
        $image->setSuffix($resizeDefine['suffix']);
        return array_merge(array(
            'resolution' => array($resizeDefine['width'], $resizeDefine['height']),
            'mime' => 'image/jpeg',
            'ext' => 'jpg',
            'suffix' => $resizeDefine['suffix'],
        ), $this->doNext($image, $resizeDefine));
    }

    protected function isSizeValid($sWidth, $sHeight, $tWidth, $tHeight)
    {
        if($sWidth < $tWidth){
            return false;
        }

        if($sHeight < $tHeight){
            return false;
        }

        return true;
    }

    protected function calculateCropPos($sWidth, $sHeight, $tWidth, $tHeight)
    {
        return array(
            round(($sWidth - $tWidth)/2),
            round(($sHeight - $tHeight)/2),
        );
    }
}