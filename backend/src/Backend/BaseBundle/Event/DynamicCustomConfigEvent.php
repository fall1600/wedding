<?php
namespace Backend\BaseBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class DynamicCustomConfigEvent extends Event
{
    const EVENT_NAME = 'event.dynamic.assets.config.dump';

    /** @var  string */
    protected $bundleName;

    protected $targetDir;

    protected $originDir;

    public function __construct($bundleName, $targetDir)
    {
        $this->bundleName = $bundleName;
        $this->targetDir = $targetDir;
    }

    /**
     * @return string
     */
    public function getBundleName(): string
    {
        return $this->bundleName;
    }

    /**
     * @return mixed
     */
    public function getTargetDir()
    {
        return $this->targetDir;
    }

}