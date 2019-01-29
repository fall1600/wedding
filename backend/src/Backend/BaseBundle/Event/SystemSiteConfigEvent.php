<?php
namespace Backend\BaseBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class SystemSiteConfigEvent extends Event
{
    const EVENT_NAME = 'event.system.site_config';

    protected $option;
    protected $allow = false;

    public function __construct($option)
    {
        $this->option = $option;
    }

    /**
     * @return mixed
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * @return bool
     */
    public function isAllow(): bool
    {
        return $this->allow;
    }

    /**
     * @param bool $allow
     * @return $this
     */
    public function setAllow(bool $allow)
    {
        $this->allow = $allow;
        return $this;
    }

}