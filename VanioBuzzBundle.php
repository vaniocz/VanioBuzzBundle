<?php
namespace Vanio\BuzzBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vanio\BuzzBundle\DependencyInjection\RegisterListenersPass;

class VanioBuzzBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RegisterListenersPass);
    }
}
