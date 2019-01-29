<?php
namespace Backend\BaseBundle\EventListener;

use Backend\BaseBundle\Event\DynamicCustomConfigEvent;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @DI\Service()
 */
class BuildBackendApiBaseConfigListener
{
    /** @var KernelInterface */
    protected $kernel;

    /**
     * @DI\InjectParams()
     */
    public function injectKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    protected function getApiBaseConfigDir()
    {
        return realpath($this->kernel->getRootDir()."/../").'/vue.cm4/static';
    }

    protected function getApiBaseConfigPath()
    {
        return "{$this->getApiBaseConfigDir()}/apibase.json";
    }

    protected function getApiBaseConfig()
    {
        return json_decode(@file_get_contents($this->getApiBaseConfigPath()), true);
    }

    protected function isValidApiBaseConfig()
    {
        if(!is_file($this->getApiBaseConfigPath())){
            return false;
        }

        $config = $this->getApiBaseConfig();

        return isset($config['apibase']);
    }

    /**
     * @DI\Observe(DynamicCustomConfigEvent::EVENT_NAME)
     */
    public function onDynamicCustomConfigEvent(DynamicCustomConfigEvent $event)
    {
        if($this->isValidApiBaseConfig()){
            return;
        }

        $this->rebuildApiBaseConfig();
    }

    protected function rebuildApiBaseConfig()
    {
        $config = $this->getApiBaseConfig();

        if(!is_array($config)){
            $config = array();
        }

        if(!isset($config['apibase'])){
            $config['apibase'] = 'http://localhost:8000/admin/api';
        }

        @mkdir($this->getApiBaseConfigDir(), 0755, true);
        $file = $this->getApiBaseConfigPath();
        file_put_contents($file, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}