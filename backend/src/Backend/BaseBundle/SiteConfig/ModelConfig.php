<?php
namespace Backend\BaseBundle\SiteConfig;

use Backend\BaseBundle\Model;

class ModelConfig implements SiteConfigInterface
{
    public function __construct()
    {
    }

    /**
     * @return Model\SiteConfig
     */
    protected function findSiteConfig($name)
    {
        if(!($siteConfig = Model\SiteConfigQuery::create()->findPk($name))){
            $siteConfig = new Model\SiteConfig();
            $siteConfig->setName($name);
        }
        return $siteConfig;
    }

    public function set($name, $config)
    {
        $siteConfig = $this->findSiteConfig($name);
        $siteConfig->setConfig($config);
        $siteConfig->save();
    }

    public function get($name)
    {
        $siteConfig = $this->findSiteConfig($name);
        return $siteConfig->getConfig($name);
    }

    public function delete($name)
    {
        $siteConfig = $this->findSiteConfig($name);
        if($siteConfig->isNew()){
            return;
        }
        $siteConfig->delete();
    }
}