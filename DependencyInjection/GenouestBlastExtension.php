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
        
        // Db providers
        if (count($config['db_provider']) != 1) {
            throw new \InvalidArgumentException('There should exactly one "db_provider"');
        }

        if (isset($config['db_provider']['biomaj'])) { // Use db list from a biomaj server
            
            $allTypes = array_merge($config['db_provider']['biomaj']['type']['nucleic'], $config['db_provider']['biomaj']['type']['proteic']);
            
            $container->setParameter('blast.db.list.widget', 'choice');
            $container->setParameter('blast.db.list.widget.options', array(
                            'choices' => array("" => "Loading, please wait...") // Empty for performance reason. We get the db list using ajax once the page is loaded
                            ));
            
            $container->setDefinition('blast.db.list.constraint.validator',
                new Definition('%blast.db.list.biomaj.constraint.validator.class%', array())
                );
                
            $container->setDefinition('blast.db.list.constraint', 
                new Definition('%blast.db.list.biomaj.constraint.class%', array(
                        array('type' => $allTypes,
                            'format' => $config['db_provider']['biomaj']['format'],
                            'autoload' => $config['db_provider']['biomaj']['autoload'],
                            'cleanup' => $config['db_provider']['biomaj']['cleanup'],
                            'filterall' => $config['db_provider']['biomaj']['filterall'],
                            )
                        )
                    )
                );

            $container->setParameter('blast.db.list.provider', 'biomaj');
            $container->setParameter('blast.db.list.provider.options', array(
                'type_nucleic' => $config['db_provider']['biomaj']['type']['nucleic'],
                'type_proteic' => $config['db_provider']['biomaj']['type']['proteic'],
                'format' => $config['db_provider']['biomaj']['format'],
                'autoload' => $config['db_provider']['biomaj']['autoload'],
                'cleanup' => $config['db_provider']['biomaj']['cleanup'],
                'filterall' => $config['db_provider']['biomaj']['filterall'],
                ));
        }
        else if (isset($config['db_provider']['list'])) { // Use a list of databases
            
            $allTypes = array_merge($config['db_provider']['list']['nucleic'], $config['db_provider']['list']['proteic']);
            
            $container->setParameter('blast.db.list.widget', 'choice');
            $container->setParameter('blast.db.list.widget.options', array('choices' => $config['db_provider']['list']['nucleic']));
            
            $container->setDefinition('blast.db.list.constraint.validator',
                new Definition('%blast.db.list.list.constraint.validator.class%', array())
            );
            
            $container->setDefinition('blast.db.list.constraint', 
                new Definition('%blast.db.list.list.constraint.class%', array(
                    array('choices' => $allTypes))
                    )
            );
            
            $container->setParameter('blast.db.list.provider', 'list');
            $container->setParameter('blast.db.list.provider.options', array(
                'list_nucleic' => $config['db_provider']['list']['nucleic'],
                'list_proteic' => $config['db_provider']['list']['proteic'],
                ));
        }
        else if (isset($config['db_provider']['callback'])) { // Use a provider
            // Same as list, but first get the choices using the callback
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
