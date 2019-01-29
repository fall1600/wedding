<?php
namespace Backend\BaseBundle\SiteConfig;

interface SiteConfigInterface
{
    public function __construct();
    public function set($name, $config);
    public function get($name);
    public function delete($name);
}