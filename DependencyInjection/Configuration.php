<?php

/*
 * Copyright 2011 Anthony Bretaudeau <abretaud@irisa.fr>
 *
 * Licensed under the CeCILL License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt
 *
 */

namespace Genouest\Bundle\BlastBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
* SchedulerExtension configuration structure.
*/
class Configuration implements ConfigurationInterface
{
    /**
    * Generates the configuration tree builder.
    *
    * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
    */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('blast');

        $rootNode
            ->children()
                ->scalarNode('version')->end()
                ->scalarNode('form_type')
                    ->cannotBeEmpty()
                    ->defaultValue('Genouest\Bundle\BlastBundle\Form\BlastType')
                ->end()
                ->scalarNode('request_class')
                    ->cannotBeEmpty()
                    ->defaultValue('Genouest\Bundle\BlastBundle\Entity\BlastRequest')
                ->end()
                ->arrayNode('db_provider')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->children()
                        ->arrayNode('biomaj')
                            ->requiresAtLeastOneElement()
                            ->cannotBeEmpty()
                            ->children()
                                ->arrayNode('type')
                                    ->cannotBeEmpty()
                                    ->requiresAtLeastOneElement()
                                    ->defaultValue(array('all'))
                                    ->prototype('scalar')->end()
                                ->end()
                                ->scalarNode('format')
                                    ->cannotBeEmpty()
                                    ->defaultValue('blast')
                                ->end()
                                ->booleanNode('autoload')->defaultTrue()->end()
                                ->booleanNode('cleanup')->defaultTrue()->end()
                                ->booleanNode('filterall')->defaultFalse()->end()
                            ->end()
                        ->end()
                        ->arrayNode('list')
                            ->requiresAtLeastOneElement()
                            ->cannotBeEmpty()
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('custom')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
