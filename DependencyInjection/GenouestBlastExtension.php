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
        
        // Unset list if empty
        if (empty($config['db_provider']['list'])) {
            unset($config['db_provider']['list']);
        }
        
        if (count($config['db_provider']) != 1) {
            throw new \InvalidArgumentException('There should only be one "db_provider"');
        }

        if (isset($config['db_provider']['biomaj'])) { // Use db list from a biomaj server
        }
        else if (isset($config['db_provider']['list'])) { // Use a list of databases
        }
        else if (isset($config['db_provider']['custom'])) { // Use a provider
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
