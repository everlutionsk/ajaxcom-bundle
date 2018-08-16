<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\DependencyInjection;

use Everlution\AjaxcomBundle\DependencyInjection\Configuration as C;
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

        $container->setParameter('everlution.ajaxcom.flash_template', $config[C::FLASH_TEMPLATE]);
        $container->setParameter('everlution.ajaxcom.flash_block_id', $config[C::FLASH_BLOCK_ID]);
        $container->setParameter('everlution.ajaxcom.persistent_class', $config[C::PERSISTENT_CLASS]);
        $container->setParameter('everlution.ajaxcom.blocks_to_render', $config[C::BLOCKS_TO_RENDER]);
        $container->setParameter('everlution.ajaxcom.change_url', $config[C::CHANGE_URL]);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
