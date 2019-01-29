<?php
namespace Backend\BaseBundle\DependencyInjection;

use Backend\BaseBundle\SiteConfig\ModelConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('backend_base');
        $rootNode
            ->children()
            ->scalarNode('serialize_null')->defaultTrue()->end()
            ->scalarNode('site_config_class')->defaultValue(ModelConfig::class)->end()
            ->scalarNode('function_checker_service')->defaultValue('site.function.checker.enabled')->end()
            ->booleanNode('cache_propel_converter')->defaultTrue()->end()
            ->scalarNode('system_config')->defaultValue('backend_base.system_config.setup')->end()
            ->scalarNode('mail_config')->defaultValue('backend_base.mail_config.setup')->end()
            ->scalarNode('token_service')->defaultValue('token_service.sha')->end()
            ->end()
            ;
        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
