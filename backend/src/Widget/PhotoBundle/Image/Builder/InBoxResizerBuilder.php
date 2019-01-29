<?php
namespace Widget\PhotoBundle\Image\Builder;

use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

class InBoxResizerBuilder extends AbstractBuilder
{

    public function build(ImageCommandBridge $image, array $resizeDefine)
    {
        $output = array();
        if($resizeDefine['type'] == 'inbox'){
            $output = $this->doResize($image, $resizeDefine);
        }
        return array_merge($output, $this->doNext($image, $resizeDefine));
    }

    protected function doResize(ImageCommandBridge $image, array $resizeDefine)
    {
        $targetSize = $this->getResizedSize($image->getWidth(), $image->getHeight(), $resizeDefine['width'], $resizeDefine['height']);
        if(!$targetSize){
            return  $this->getOriginOutput($image);
        }

        $image->setExt('jpg');
        $image->setSuffix($resizeDefine['suffix']);
        $image->append('resize', $targetSize[0], $targetSize[1]);
        return array(
            'resolution' => $targetSize,
            'mime' => 'image/jpeg',
            'ext' => 'jpg',
            'suffix' => $resizeDefine['suffix'],
        );
    }

    /**
     * 取的縮圖後的尺寸.
     *
     * @param array $source
     * @param array $target
     *
     * @return array ; false means no need to resize
     */
    protected function getResizedSize($sWidth, $sHeight, $tWidth, $tHeight)
    {

        if (($sWidth <= $tWidth) && ($sHeight <= $tHeight)) {
            return false;
        }

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