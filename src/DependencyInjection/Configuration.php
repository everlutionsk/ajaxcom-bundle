<?php

declare(strict_types=1);

namespace Everlution\AjaxcomBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 *
 * @author Ivan Barlog <ivan.barlog@everlution.sk>
 */
class Configuration implements ConfigurationInterface
{
    const FLASH_TEMPLATE = 'flash_template';
    const FLASH_BLOCK_ID = 'flash_block_id';
    const PERSISTENT_CLASS = 'persistent_class';
    const BLOCKS_TO_RENDER = 'blocks_to_render';
    const CHANGE_URL = 'change_url';
    const ID = 'id';
    const REFRESH = 'refresh';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('everlution_ajaxcom');

        $rootNode
            ->children()
                ->scalarNode(self::FLASH_TEMPLATE)->defaultValue('@EverlutionAjaxcom/flash_message.html.twig')->end()
                ->scalarNode(self::FLASH_BLOCK_ID)->defaultValue('flash_message')->end()
                ->scalarNode(self::PERSISTENT_CLASS)->defaultValue('ajaxcom-persistent')->end()
                ->scalarNode(self::CHANGE_URL)->defaultTrue()->end()
                ->arrayNode(self::BLOCKS_TO_RENDER)
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode(self::ID)->end()
                            ->scalarNode(self::REFRESH)->defaultFalse()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
