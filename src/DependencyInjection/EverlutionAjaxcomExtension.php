<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class EverlutionAjaxcomExtension.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class EverlutionAjaxcomExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('everlution.ajaxcom.flash_template', $config['flash_template']);
        $container->setParameter('everlution.ajaxcom.flash_block_id', $config['flash_block_id']);
        $container->setParameter('everlution.ajaxcom.persistent_class', $config['persistent_class']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
