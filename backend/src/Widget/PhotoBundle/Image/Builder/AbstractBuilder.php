<?php
namespace Widget\PhotoBundle\Image\Builder;

use Widget\PhotoBundle\Image\Bridge\ImageCommandBridge;

abstract class AbstractBuilder
{
    /** @var  AbstractBuilder */
    protected $nextBuilder;

    abstract public function build(ImageCommandBridge $image, array $resizeDefine);

    public function __construct(AbstractBuilder $nextBuilder = null)
    {
        $this->nextBuilder = $nextBuilder;
    }

    protected function doNext(ImageCommandBridge $image, array $resizeDefine)
    {
        if($this->nextBuilder === null){
            return array();
        }
        return $this->nextBuilder->build($image, $resizeDefine);
    }

    protected function getOriginOutput(ImageCommandBridge $image)
    {
        $mime = $image->getMime();
        $ext = $this->mimeToExt($mime);
        $image->setExt($ext);
        $image->setSuffix('origin');
        return array(
            'resolution' => array($image->getWidth(), $image->getHeight()),
            'mime' => $mime,
            'ext' => $ext,
            'suffix' => 'origin',
        );
    }

    /**
     * @return self
     */
    static public function make()
    {
        return new InBoxResizerBuilder(
            new OutBoxResizerBuilder(
                new FitResizerBuilder(
                    new CropResizerBuilder(
                        new ResizeCropResizerBuilder(
                            new CopyBuilder(
                                new ImageBuilder()
                            )
                        )
                    )
                )
            )
        );
    }

    protected function mimeToExt($mime) {
        $extDefine = array(
            'image/gif' => 'gif',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/x-bmp' => 'bmp',
        );
        if (!isset($extDefine[$mime])) {
            return false;
        }
        return $extDefine[$mime];
    }
}