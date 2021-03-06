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

interface DbProviderInterface
{
    /**
     * Get a list of nucleic databank
     *
     * @return array An associative array of nucleic databank (key: path, value: label)
     */
    public function getNucleicDatabanks();
    
    /**
     * Get a list of proteic databank
     *
     * @return array An associative array of proteic databank (key: path, value: label)
     */
    public function getProteicDatabanks();
    
    /**
     * Get the widget type name ('choice' for example)
     *
     * @return strign The type name of the field
     */
    public function getWidgetType();
    
    /**
     * Get the widget options array
     *
     * @return array An associative array of widget option
     */
    public function getWidgetOptions();
}

