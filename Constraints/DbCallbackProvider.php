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

namespace Genouest\Bundle\BlastBundle\Constraints;

use Symfony\Component\Validator\Constraint;

/** @Annotation */
class DbCallbackProvider extends Constraint
{
    public $message = 'The value you selected is not a valid choice';
    
    public function validatedBy()
    {
        return 'dbcallback';
    }
}
