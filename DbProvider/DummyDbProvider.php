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

class DummyDbProvider extends CallbackDbProvider
{
    /**
     * Compute a list of nucleic databank
     *
     * @return array An associative array of nucleic databank (key: path, value: label)
     */
    public function computeNucleicDatabanks() {
    
        return array('/some/path' => 'Nucleic db 1',
                     '/some/other/path' => 'Nucleic db 2');
    }
    
    /**
     * Compute a list of proteic databank
     *
     * @return array An associative array of proteic databank (key: path, value: label)
     */
    public function computeProteicDatabanks() {
    
        return array('/db/some/path' => 'Proteic db 1',
                     '/db/some/other/path' => 'Proteic db 2');
    }
}

