<?php
namespace Backend\BaseBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
abstract class AbstractExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    protected function registerDIExtraBundles(ContainerBuilder $container)
    {
        preg_match('/.*\\\\(\w*)Extension$/i', static::class, $match);
        $bundleName = "{$match['1']}Bundle";
        $bundles = $container->getParameterBag()->get('jms_di_extra.bundles');
        $bundles[] = $bundleName;
        $container->getParameterBag()->set('jms_di_extra.bundles', $bundles);
    }
}
