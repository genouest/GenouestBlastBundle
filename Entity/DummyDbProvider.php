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

namespace Genouest\Bundle\BlastBundle\Entity;

class DummyDbProvider implements DbProviderInterface
{
    /**
     * Get a list of nucleic databank
     *
     * @return array An associative array of nucleic databank (key: path, value: label)
     */
    public function getNucleicDatabanks() {
    
        return array('/some/path' => 'Nucleic db 1',
                     '/some/other/path' => 'Nucleic db 2');
    }
    
    /**
     * Get a list of proteic databank
     *
     * @return array An associative array of proteic databank (key: path, value: label)
     */
    public function getProteicDatabanks() {
    
        return array('/db/some/path' => 'Proteic db 1',
                     '/db/some/other/path' => 'Proteic db 2');
    }
}

