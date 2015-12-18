<?php
namespace Vanio\BuzzBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterListenersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('buzz');

        foreach ($container->findTaggedServiceIds('vanio_buzz.listener') as $id => $tags) {
            $definition->addMethodCall('addListener', [new Reference($id)]);
        }
    }
}
