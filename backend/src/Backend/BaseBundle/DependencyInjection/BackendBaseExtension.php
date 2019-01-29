<?php
namespace Backend\BaseBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

class BackendBaseExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->registerDIExtraBundles($container);
        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);
        $container->setParameter('serialize_null', $processedConfig['serialize_null']);
        $container->setParameter('site_config_class', $processedConfig['site_config_class']);
        $container->setParameter('cache_propel_converter', $processedConfig['cache_propel_converter']);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $container->setAlias('backend_base.system_config', $processedConfig['system_config']);
        $container->setAlias('backend_base.mail_config', $processedConfig['mail_config']);
        $container->setAlias('token_service', $processedConfig['token_service']);
        $container->setParameter('token_issuer', md5(($_SERVER['SERVER_NAME']??'localhost').__FILE__));
    }
}
