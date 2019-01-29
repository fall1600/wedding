<?php
namespace Backend\BaseBundle\Service;

use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\InjectParams;

/**
 * @Service("backend_base.mail_config.parameters")
 */
class ParametersMailConfig extends \ArrayObject
{

    /**
     * @InjectParams({
     *    "mailConfig" = @Inject("%backend_base.mail_config%", required=false),
     * })
     */
    public function injectConfig($mailConfig = array())
    {
        $this->exchangeArray((array) $mailConfig);
    }

}