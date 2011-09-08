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

class BiomajDbProvider extends DbProvider
{
    
    protected $nucleic_banks;
    protected $proteic_banks;
    
    protected $nucleicTypes;
    protected $proteicTypes;
    protected $format;
    protected $cleanUp;
    protected $nucleicDefault;
    protected $proteicDefault;
    
    public function __construct(array $nucleicTypes, array $proteicTypes, $format, $cleanUp, $nucleicDefault, $proteicDefault) {
        $this->nucleicTypes = $nucleicTypes;
        $this->proteicTypes = $proteicTypes;
        $this->format = $format;
        $this->cleanUp = $cleanUp;
        $this->nucleicDefault = $nucleicDefault;
        $this->proteicDefault = $proteicDefault;
    
        $this->nucleic_banks = $this->getNucleicDatabanks();
        $this->proteic_banks = $this->getProteicDatabanks();
    }
    
    /**
     * Get the widget options array
     *
     * @return array An associative array of widget option
     */
    public function getWidgetOptions() {
    
        // Don't load the list now to speed up the page loading
        return array('choices' => array("" => "Loading, please wait..."));
    }
    
    /**
     * Get the list of biomaj nucleic bank types
     *
     * @return array The biomaj bank types asked
     */
    public function getNucleicTypes() {
        return $this->nucleicTypes;
    }
    
    /**
     * Get the list of biomaj proteic bank types
     *
     * @return array The biomaj bank types asked
     */
    public function getProteicTypes() {
        return $this->proteicTypes;
    }
    
    /**
     * Get the biomaj bank format
     *
     * @return string The biomaj bank format
     */
    public function getFormat() {
        return $this->format;
    }
    
    /**
     * Should the bank names be cleaned up?
     *
     * @return boolean True if the bank names will be cleaned up
     */
    public function getCleanUp() {
        return $this->cleanUp;
    }
    
    /**
     * Get the default nucleic bank
     *
     * @return string The default nucleic bank
     */
    public function getNucleicDefault() {
        return $this->nucleicDefault;
    }
    
    /**
     * Get the default proteic bank
     *
     * @return string The default proteic bank
     */
    public function getProteicDefault() {
        return $this->proteicDefault;
    }
}

