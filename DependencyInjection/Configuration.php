<?php
namespace Vanio\BuzzBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder;
        $rootNode = $treeBuilder->root('vanio_buzz');
        $rootNode
            ->children()
                ->scalarNode('client')
                    ->defaultValue('curl')
                    ->end()
                ->end()
                ->scalarNode('client_timeout')
                    ->defaultValue(5)
                    ->end()
                ->end()
                ->scalarNode('throw_exceptions')
                    ->defaultValue(true)
                    ->end()
                ->end()
                ->scalarNode('defer_listeners')
                    ->defaultValue(true)
                    ->end()
                ->end()
                ->scalarNode('json_listener')
                    ->defaultValue(false)
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
