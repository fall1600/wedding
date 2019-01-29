<?php
namespace Backend\BaseBundle\Service;

use Backend\BaseBundle\SiteConfig\SiteConfigBuilder;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\DiExtraBundle\Annotation\Service;

/**
 * @Service("backend_base.mail_config.setup")
 */
class SetupMailConfig extends \ArrayObject
{

    /**
     * @DI\InjectParams({
     *    "siteConfigBuilder" = @DI\Inject("backend_base.site_config_builder"),
     * })
     */
    public function injectConfig(SiteConfigBuilder $siteConfigBuilder)
    {
        $this->exchangeArray((array) $siteConfigBuilder->build()->get('backend_base'));
    }

}