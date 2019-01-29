<?php
namespace Widget\CategoryBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class CategoryEvent extends Event
{
    const EVENT_CATEGORY = 'event.category';

    protected $isThreadAllowed = false;
    protected $thread;
    protected $depth;
    protected $extendTemplate;

    public function __construct($thread)
    {
        $this->thread = $thread;
    }

    public function hasThread()
    {
        return $this->isThreadAllowed;
    }

    public function setExtendTemplate($template)
    {
        $this->extendTemplate = $template;
    }

    public function getExtendTemplate()
    {
        return $this->extendTemplate;
    }

    public function setThreadExist($isThreadAllowed)
    {
        $this->isThreadAllowed = $isThreadAllowed;
    }

    public function getThread()
    {
        return $this->thread;
    }

    public function setMaxDepth($depth)
    {
        $this->depth = $depth;
    }

    public function getMaxDepth()
    {
        return $this->depth;
    }
}