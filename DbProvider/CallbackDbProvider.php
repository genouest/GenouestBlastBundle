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

abstract class CallbackDbProvider extends DbProvider
{

    protected $nucleic_banks;
    protected $proteic_banks;
    
    public function __construct() {
        $this->nucleic_banks = null;
        $this->proteic_banks = null;
    }
    
    /**
     * Get a list of proteic databank
     *
     * @return array An associative array of proteic databank (key: path, value: label)
     */
    public function getProteicDatabanks() {
    
        if ($this->proteic_banks == null);
            $this->proteic_banks = $this->computeProteicDatabanks();

        return $this->proteic_banks;
    }
    
    /**
     * Compute a list of nucleic databank
     *
     * @return array An associative array of nucleic databank (key: path, value: label)
     */
    abstract public function computeNucleicDatabanks();
    
    /**
     * Compute a list of proteic databank
     *
     * @return array An associative array of proteic databank (key: path, value: label)
     */
    abstract public function computeProteicDatabanks();
    
    /**
     * Get a list of nucleic databank
     *
     * @return array An associative array of nucleic databank (key: path, value: label)
     */
    public function getNucleicDatabanks() {
    
        if ($this->nucleic_banks == null);
            $this->nucleic_banks = $this->computeNucleicDatabanks();

        return $this->nucleic_banks;
    }
}

