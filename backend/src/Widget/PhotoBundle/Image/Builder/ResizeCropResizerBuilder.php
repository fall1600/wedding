<?php
namespace Widget\PhotoBundle\Image\Builder;

use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

class ResizeCropResizerBuilder extends CropResizerBuilder
{

    public function build(ImageCommandBridge $image, array $resizeDefine)
    {
        if($resizeDefine['type'] != 'resizecrop'){
            return $this->doNext($image, $resizeDefine);
        }

        if(!$this->isSizeValid($image->getWidth(), $image->getHeight(), $resizeDefine['width'], $resizeDefine['height'])) {
            return array_merge($this->getOriginOutput($image), $this->doNext($image, $resizeDefine));
        }
        
        $this->doCrop($image, $resizeDefine);
        $this->doResize($image, $resizeDefine);
        $image->setSuffix($resizeDefine['suffix']);
        $image->setExt('jpg');
        return array_merge(array(
            'resolution' => array($resizeDefine['width'], $resizeDefine['height']),
            'mime' => 'image/jpeg',
            'ext' => 'jpg',
            'suffix' => $resizeDefine['suffix'],
        ), $this->doNext($image, $resizeDefine));
    }

    protected function doCrop(ImageCommandBridge $image, array $resizeDefine)
    {
        list($width, $height) = $this->getResizedSize($resizeDefine['width'], $resizeDefine['height'], $image->getWidth(), $image->getHeight());
        list($x, $y) = $this->calculateCropPos($image->getWidth(), $image->getHeight(), $width, $height);
        $image->append('crop', $width, $height, $x, $y);
    }

    protected function doResize(ImageCommandBridge $image, array $resizeDefine)
    {
        $image->append('resize', $resizeDefine['width'], $resizeDefine['height']);
    }

    /**
     * 取的放大後的尺寸.
     * @return array ; false means no need to resize
     */
    protected function getResizedSize($sWidth, $sHeight, $tWidth, $tHeight)
    {
        $useWidthResize = false;
        if (($tWidth / $sWidth) * $sHeight <= $tHeight) {
            $useWidthResize = true;
        }

        if ($useWidthResize) {
            //依據寬邊 Resize
            return array($tWidth, round(($tWidth / $sWidth) * $sHeight));
        }

        //依據高 Resize
        return array(round(($tHeight / $sHeight) * $sWidth), $tHeight);
    }
    
}