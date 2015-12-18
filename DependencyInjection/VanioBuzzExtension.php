<?php
namespace Vanio\BuzzBundle\DependencyInjection;

use Buzz\Client\Curl;
use Buzz\Client\FileGetContents;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Vanio\BuzzBundle\Buzz\MultiCurl;

class VanioBuzzExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('config.xml');

        $container->setParameter('buzz.client.class', $this->determineClientClass($config['client']));
        $container->setParameter('buzz.client.timeout', $config['client_timeout']);

        if ($config['client'] === 'multi_curl' && $config['defer_listeners']) {
            $container->getDefinition('vanio_buzz.buzz.exception_listener')->addTag('vanio_buzz.listener');
        }

        if ($config['throw_exceptions']) {
            $container->getDefinition('vanio_buzz.buzz.exception_listener')->addTag('vanio_buzz.listener');
        }

        if ($config['json_listener']) {
            $container->getDefinition('vanio_buzz.buzz.json_listener')->addTag('vanio_buzz.listener');
        }
    }

    private function determineClientClass($client)
    {
        switch ($client) {
            case 'file_get_contents':
                return FileGetContents::class;
            case 'multi_curl':
                return MultiCurl::class;
            case 'curl':
            default:
                return Curl::class;
        }
    }
}
