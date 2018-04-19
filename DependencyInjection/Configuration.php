<?php

namespace JK\MoneyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 * @author Jakub Kucharovic <jakub@kucharovic.cz>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('jk_money');

        $rootNode
            ->children()
            ->scalarNode('currency')->defaultValue('USD')->end()
            ->end();

        return $treeBuilder;
    }
}
