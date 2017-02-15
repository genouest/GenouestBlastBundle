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
                ->scalarNode('title')->end()
                ->scalarNode('form_type')
                    ->cannotBeEmpty()
                    ->defaultValue('Genouest\Bundle\BlastBundle\Form\BlastType')
                ->end()
                ->scalarNode('request_class')
                    ->cannotBeEmpty()
                    ->defaultValue('Genouest\Bundle\BlastBundle\Entity\BlastRequest')
                ->end()
                ->scalarNode('scheduler_name')
                    ->cannotBeEmpty()
                    ->defaultValue('blast')
                ->end()
                ->scalarNode('cdd_delta_path')
                    ->defaultValue('')
                ->end()
                ->scalarNode('pre_command')
                    ->defaultValue('')
                ->end()
                ->scalarNode('link_command')
                    ->defaultValue('')
                ->end()
                ->arrayNode('db_provider')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->children()
                        ->arrayNode('biomaj')
                            ->cannotBeEmpty()
                            ->children()
                                ->arrayNode('type')
                                    ->isRequired()
                                    ->children()
                                        ->arrayNode('nucleic')
                                            ->isRequired()
                                            ->requiresAtLeastOneElement()
                                            ->cannotBeEmpty()
                                            ->prototype('scalar')->end()
                                        ->end()
                                        ->arrayNode('proteic')
                                            ->isRequired()
                                            ->requiresAtLeastOneElement()
                                            ->cannotBeEmpty()
                                            ->prototype('scalar')->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('default')
                                    ->cannotBeEmpty()
                                    ->children()
                                        ->scalarNode('nucleic')
                                            ->cannotBeEmpty()
                                            ->defaultValue('')
                                        ->end()
                                        ->scalarNode('proteic')
                                            ->cannotBeEmpty()
                                            ->defaultValue('')
                                        ->end()
                                    ->end()
                                ->end()
                                ->scalarNode('format')
                                    ->cannotBeEmpty()
                                    ->defaultValue('blast')
                                ->end()
                                ->booleanNode('cleanup')->defaultTrue()->end()
                                ->scalarNode('prefix')
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('list')
                            ->children()
                                ->arrayNode('nucleic')
                                    ->isRequired()
                                    ->requiresAtLeastOneElement()
                                    ->cannotBeEmpty()
                                    ->useAttributeAsKey('name')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('proteic')
                                    ->isRequired()
                                    ->requiresAtLeastOneElement()
                                    ->cannotBeEmpty()
                                    ->useAttributeAsKey('name')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('callback')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
