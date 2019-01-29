<?php
namespace Backend\BaseBundle\SiteConfig;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;

/**
 * @Service("backend_base.site_config_builder")
 */
class SiteConfigBuilder
{
    protected $configClass;

    /**
     * @InjectParams({
     *    "configClass" = @Inject("%site_config_class%"),
     * })
     */
    public function injectConfigClass($configClass)
    {
        $this->configClass = $configClass;
    }

    /**
     * @return SiteConfigInterface
     */
    public function build()
    {
        $class = $this->configClass;
        return new $class();
    }
}