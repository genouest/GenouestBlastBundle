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

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

/**
 * BlastExtension is an extension for the Blast bundle.
 */
class GenouestBlastExtension extends Extension
{
    /**
     * Loads the Blast configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);        
        
        if (isset($config['version']))
            $container->setParameter('blast.version', $config['version']);
        
        $container->setParameter('blast.form.type.class', $config['form_type']);
        
        $container->setParameter('blast.request.class', $config['request_class']);
        
        $container->setParameter('blast.scheduler.name', $config['scheduler_name']);
        
        // Db providers
        if (count($config['db_provider']) != 1) {
            throw new \InvalidArgumentException('There should exactly one "db_provider"');
        }

        if (isset($config['db_provider']['biomaj'])) { // Use db list from a biomaj server
            
            $container->setParameter('blast.db.list.provider.name', 'biomaj');
            
            $container->setDefinition('blast.db.list.provider',
                new Definition('%blast.db.list.provider.biomaj.class%', array($config['db_provider']['biomaj']['type']['nucleic'], $config['db_provider']['biomaj']['type']['proteic'],  $config['db_provider']['biomaj']['format'], $config['db_provider']['biomaj']['cleanup'], $config['db_provider']['biomaj']['default']['nucleic'], $config['db_provider']['biomaj']['default']['proteic']))
            )->addMethodCall('setContainer', array(new Reference('service_container')));
            
            // Validation
            if (!empty($config['db_provider']['biomaj']['prefix'])) {
                $container->setDefinition('blast.db.list.constraint', 
                    new Definition('%biomaj.prefix.constraint.class%', array(
                            array('prefix' => $config['db_provider']['biomaj']['prefix']
                                )
                            )
                        )
                    );

                $container->setAlias('blast.db.list.constraint.validator', 'biomaj.prefix.constraint.validator');
            }
            else {
                $allTypes = array_merge($config['db_provider']['biomaj']['type']['nucleic'], $config['db_provider']['biomaj']['type']['proteic']);
                $container->setDefinition('blast.db.list.constraint', 
                    new Definition('%biomaj.constraint.class%', array(
                            array('type' => $allTypes,
                                'format' => $config['db_provider']['biomaj']['format'],
                                'cleanup' => $config['db_provider']['biomaj']['cleanup'],
                                )
                            )
                        )
                    );

                $container->setAlias('blast.db.list.constraint.validator', 'biomaj.constraint.validator');
            }
        }
        else if (isset($config['db_provider']['list'])) { // Use a list of databases
            
            $container->setParameter('blast.db.list.provider.name', 'list');
            
            $container->setDefinition('blast.db.list.provider',
                new Definition('%blast.db.list.provider.list.class%', array($config['db_provider']['list']['nucleic'], $config['db_provider']['list']['proteic']))
            )->addMethodCall('setContainer', array(new Reference('service_container')));
            
            // Validation
            $allTypes = array_merge($config['db_provider']['list']['nucleic'], $config['db_provider']['list']['proteic']);
            
            $container->setDefinition('blast.db.list.constraint', 
                new Definition('%blast.db.list.list.constraint.class%', array(
                    array('choices' => array_keys($allTypes)))
                    )
            );
            
            $container->setDefinition('blast.db.list.constraint.validator',
                new Definition('%blast.db.list.list.constraint.validator.class%', array())
            );
        }
        else if (isset($config['db_provider']['callback'])) { // Use a provider
            
            $container->setParameter('blast.db.list.provider.name', 'callback');
            
            $container->setDefinition('blast.db.list.provider',
                new Definition($config['db_provider']['callback'], array())
            )->addMethodCall('setContainer', array(new Reference('service_container')));
            
            // Validation
            $container->setDefinition('blast.db.list.constraint', 
                new Definition('%blast.db.list.callback.constraint.class%', array()));
            
            $container->setAlias('blast.db.list.constraint.validator','blast.db.list.callback.constraint.validator');
        }
        
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('blast.xml');
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     */
    public function getNamespace()
    {
        return 'http://www.genouest.org/schema/blast';
    }
}
