<?php

namespace Pipirima\PimcoreFtpExportBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class PimcoreFtpExportExtension
 * @package Pipirima\PimcoreFtpExportBundle\DependencyInjection
 */
class PimcoreFtpExportExtension extends Extension
{
    /**
     * @inheritdoc
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // use this to load your custom configurations
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter($this->getAlias() . '.exports', $config['exports'] ?? []);
        $container->setParameter($this->getAlias() . '.debug', $config['debug'] ?? false);
    }
}
