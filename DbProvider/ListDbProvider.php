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

class ListDbProvider extends DbProvider
{
    
    protected $nucleic_banks;
    protected $proteic_banks;
    
    public function __construct(array $nucleic, array $proteic) {
        $this->nucleic_banks = $nucleic;
        $this->proteic_banks = $proteic;
    }
}

