<?php
namespace Backend\BaseBundle\Service;

use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\InjectParams;

/**
 * @Service("backend_base.system_config.parameters")
 */
class ParametersSystemConfig extends \ArrayObject
{

    /**
     * @InjectParams({
     *    "systemConfig" = @Inject("%backend_base.system_config%", required=false),
     * })
     */
    public function injectConfig($systemConfig = array())
    {
        $this->exchangeArray((array) $systemConfig);
    }

}