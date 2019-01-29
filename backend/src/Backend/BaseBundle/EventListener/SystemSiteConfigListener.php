<?php
namespace Backend\BaseBundle\EventListener;

use Backend\BaseBundle\Event\SystemSiteConfigEvent;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service()
 */
class SystemSiteConfigListener
{
    /**
     * @DI\Observe(SystemSiteConfigEvent::EVENT_NAME)
     */
    public function onGoogleTagManager(SystemSiteConfigEvent $event)
    {
        if($event->getOption() == 'google_tag_manager'){
            $event->setAllow(true)->stopPropagation();
        }
    }

    /**
     * @DI\Observe(SystemSiteConfigEvent::EVENT_NAME)
     */
    public function onRecaptchaSiteKey(SystemSiteConfigEvent $event)
    {
        if($event->getOption() == 'recaptcha_site_key'){
            $event->setAllow(true)->stopPropagation();
        }
    }

}