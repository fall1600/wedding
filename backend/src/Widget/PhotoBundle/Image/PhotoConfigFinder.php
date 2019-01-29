<?php
namespace Widget\PhotoBundle\Image;

use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Widget\PhotoBundle\Model\PhotoConfig;
use Widget\PhotoBundle\Model\PhotoConfigQuery;

/**
 * @DI\Service("widget.photo_bundle.config_finder")
 */
class PhotoConfigFinder
{
    protected $defaultPhotoConfig;

    /**
     * @InjectParams({
     *     "config" = @Inject("%widget_photo.default_config%"),
     * })
     */
    public function injectDefaultPhotoConfig($config)
    {
        $this->defaultPhotoConfig = $config;
    }

    public function findConfig($name, $autoCreateDefault = true)
    {
        $config = PhotoConfigQuery::create()
            ->filterByName($name)
            ->findOne();
        if($config){
            return $config;
        }
        if($autoCreateDefault) {
            return $this->findDefaultConfig();
        }
        return null;
    }

    protected function findDefaultConfig()
    {
        if($defaultConfig = $this->findConfig('default', false)){
            return $defaultConfig;
        }

        return $this->createDefaultConfig();
    }

    protected function createDefaultConfig()
    {
        $config = new PhotoConfig();
        $config->setConfig($this->defaultPhotoConfig);
        $config->setName('default');
        $config->setBrief('default');
        $config->save();
        return $config;
    }
}