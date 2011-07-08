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
            
            if (!empty($config['db_provider']['biomaj']['prefix'])) {
                $container->setAlias('blast.db.list.constraint.validator', 'biomaj.prefix.constraint.validator');
                    
                $container->setDefinition('blast.db.list.constraint', 
                    new Definition('%biomaj.prefix.constraint.class%', array(
                            array('prefix' => $config['db_provider']['biomaj']['prefix']
                                )
                            )
                        )
                    );
            }
            else {
                $container->setAlias('blast.db.list.constraint.validator', 'biomaj.constraint.validator');
                    
                $container->setDefinition('blast.db.list.constraint', 
                    new Definition('%biomaj.constraint.class%', array(
                            array('type' => $allTypes,
                                'format' => $config['db_provider']['biomaj']['format'],
                                'cleanup' => $config['db_provider']['biomaj']['cleanup'],
                                )
                            )
                        )
                    );
            }

            $container->setParameter('blast.db.list.provider', 'biomaj');
            $container->setParameter('blast.db.list.provider.options', array(
                'type_nucleic' => $config['db_provider']['biomaj']['type']['nucleic'],
                'type_proteic' => $config['db_provider']['biomaj']['type']['proteic'],
                'format' => $config['db_provider']['biomaj']['format'],
                'cleanup' => $config['db_provider']['biomaj']['cleanup'],
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
                    array('choices' => array_keys($allTypes)))
                    )
            );
            
            $container->setParameter('blast.db.list.provider', 'list');
            $container->setParameter('blast.db.list.provider.options', array(
                'list_nucleic' => $config['db_provider']['list']['nucleic'],
                'list_proteic' => $config['db_provider']['list']['proteic'],
                ));
        }
        else if (isset($config['db_provider']['callback'])) { // Use a provider
            
            $container->setDefinition('blast.db.list.callback',
                new Definition($config['db_provider']['callback'], array())
            );
            
            $nucType = $container->get('blast.db.list.callback')->getNucleicDatabanks();
            $protType = $container->get('blast.db.list.callback')->getProteicDatabanks();
            $allTypes = array_merge($nucType, $protType);
            
            $container->setParameter('blast.db.list.widget', 'choice');
            $container->setParameter('blast.db.list.widget.options', array('choices' => $nucType));
            
            $container->setDefinition('blast.db.list.constraint.validator',
                new Definition('%blast.db.list.list.constraint.validator.class%', array())
            );
            
            $container->setDefinition('blast.db.list.constraint', 
                new Definition('%blast.db.list.list.constraint.class%', array(
                    array('choices' => array_keys($allTypes)))
                    )
            );
            
            $container->setParameter('blast.db.list.provider', 'list');
            $container->setParameter('blast.db.list.provider.options', array(
                'list_nucleic' => $nucType,
                'list_proteic' => $protType,
                ));
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
