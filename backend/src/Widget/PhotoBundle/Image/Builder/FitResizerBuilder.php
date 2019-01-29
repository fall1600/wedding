<?php
namespace Widget\PhotoBundle\Image\Builder;

use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

class FitResizerBuilder extends AbstractBuilder
{

    public function build(ImageCommandBridge $image, array $resizeDefine)
    {
        $output = array();
        if($resizeDefine['type'] == 'fit'){
            $image->setExt('jpg');
            $image->setSuffix($resizeDefine['suffix']);
            $image->append('resize', $resizeDefine['width'], $resizeDefine['height']);
            $output = array(
                'resolution' => array($resizeDefine['width'], $resizeDefine['height']),
                'mime' => 'image/jpeg',
                'ext' => 'jpg',
                'suffix' => $resizeDefine['suffix'],
            );
        }
        return array_merge($output, $this->doNext($image, $resizeDefine));
    }

}