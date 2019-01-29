<?php

namespace Widget\PhotoBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Backend\BaseBundle\DependencyInjection\AbstractExtension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class WidgetPhotoExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->registerDIExtraBundles($container);
        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);

        $container->setAlias('widget_photo.file_store', $processedConfig['file_store_service']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $this->setDefaultConfig($container);
    }

    protected function setDefaultConfig(ContainerBuilder $container)
    {
        if(!$container->hasParameter('widget_photo.default_config')){
            $container->setParameter('widget_photo.default_config', array(
                array(
                    'type' => 'inbox',
                    'suffix' => 'large',
                    'width' => 1600,
                    'height' =>  1200,
                ),
                array(
                    'type' => 'inbox',
                    'suffix' => 'middle',
                    'width' => 800,
                    'height' =>  600,
                ),
                array(
                    'type' => 'inbox',
                    'suffix' => 'small',
                    'width' => 400,
                    'height' =>  300,
                ),
                array(
                    'type' => 'inbox',
                    'suffix' => 'tiny',
                    'width' => 120,
                    'height' =>  90,
                ),
            ));
        }
    }
}
