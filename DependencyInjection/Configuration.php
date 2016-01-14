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
                ->enumNode('client')
                    ->values(['curl', 'file_get_contents', 'multi_curl'])
                    ->defaultValue('curl')
                ->end()
                ->integerNode('client_timeout')->defaultValue(5)->end()
                ->booleanNode('client_verify_peer')->defaultTrue()->end()
                ->booleanNode('throw_exceptions')->defaultTrue()->end()
                ->booleanNode('json_listener')->defaultFalse()->end()
            ->end();

        return $treeBuilder;
    }
}
