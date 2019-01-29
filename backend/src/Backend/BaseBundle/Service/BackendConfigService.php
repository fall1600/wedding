<?php
namespace Backend\BaseBundle\Service;

use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Tag;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Backend\BaseBundle\Event\Controller\ConfigEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @Service("backend_base_config")
 */
class BackendConfigService
{

    /** @var  EventDispatcher */
    protected $eventDispatcher;

    /**
     * @InjectParams()
     */
    public function __construct($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getControllerConfig($controllerName)
    {
        $event = new ConfigEvent($controllerName);
        $this->eventDispatcher->dispatch(ConfigEvent::EVENT_CONFIG, $event);
        return $event->getConfig();
    }
}