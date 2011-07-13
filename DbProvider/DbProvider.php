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

namespace Genouest\Bundle\BlastBundle\DbProvider;

use Symfony\Component\DependencyInjection\ContainerAware;

abstract class DbProvider extends ContainerAware implements DbProviderInterface
{
    
    protected $nucleic_banks;
    protected $proteic_banks;

    /**
     * Get a list of all databanks (nucleic and proteic)
     *
     * @return array An associative array of nucleic databank (key: path, value: label)
     */
    public function getAllDatabanks() {
        
        $all = array();
        
        if (!empty($this->nucleic_banks))
            $all = array_merge($all, $this->nucleic_banks);

        if (!empty($this->proteic_banks))
            $all = array_merge($all, $this->proteic_banks);
        
        return $all;
    }
    
    /**
     * Get a list of nucleic databank
     *
     * @return array An associative array of nucleic databank (key: path, value: label)
     */
    public function getNucleicDatabanks() {
    
        return $this->nucleic_banks;
    }
    
    /**
     * Get a list of proteic databank
     *
     * @return array An associative array of proteic databank (key: path, value: label)
     */
    public function getProteicDatabanks() {
    
        return $this->proteic_banks;
    }
    
    /**
     * Get the widget type name ('choice' for example)
     *
     * @return strign The type name of the field
     */
    public function getWidgetType() {
        return 'choice';
    }
    
    /**
     * Get the widget options array
     *
     * @return array An associative array of widget option
     */
    public function getWidgetOptions() {
        
        if (!empty($this->nucleic_banks))
            return array('choices' => $this->getNucleicDatabanks());
        else
            return array('choices' => $this->getProteicDatabanks());
    }
}

