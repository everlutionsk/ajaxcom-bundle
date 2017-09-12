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
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('everlution_ajaxcom');

        $rootNode
            ->children()
                ->scalarNode('flash_template')->defaultValue('@EverlutionAjaxcom/flash_message.html.twig')->end()
                ->scalarNode('flash_block_id')->defaultValue('flash_message')->end()
                ->scalarNode('persistent_class')->defaultValue('ajaxcom-persistent')->end()
                ->arrayNode('blocks_to_render')
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
