<?php
namespace Widget\PhotoBundle\Image\Builder;

use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

class OutBoxResizerBuilder extends InBoxResizerBuilder
{

    public function build(ImageCommandBridge $image, array $resizeDefine)
    {
        $output = array();
        if($resizeDefine['type'] == 'outbox'){
            $output = $this->doResize($image, $resizeDefine);
        }
        return array_merge($output, $this->doNext($image, $resizeDefine));
    }

    /**
     * 取的縮圖後的尺寸.
     * @return array ; false means no need to resize
     */
    protected function getResizedSize($sWidth, $sHeight, $tWidth, $tHeight)
    {

        if (($sWidth <= $tWidth) && ($sHeight <= $tHeight)) {
            return false;
        }

        $useWidthResize = false;
        if (($tWidth / $sWidth) * $sHeight >= $tHeight) {
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