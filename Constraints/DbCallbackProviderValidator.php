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
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DbCallbackProviderValidator extends ConstraintValidator
{
    protected $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        $choices = $this->container->get('blast.db.list.provider')->getAllDatabanks();
        
        // This validator uses in_array() in strict mode which means the type is important.
        // array_keys converts string keys that looks like integer to int in the resulting array.
        // So we're forced to check that the array_keys result contains only strings.
        $choices = array_map(create_function('$value', 'return (string) $value;'), array_keys($choices));

        if (!in_array($value, $choices, true)) {
            $this->context->addViolation($constraint->message, array(
                '{{ value }}' => $value,
            ));
            
            return;
        }
    }
}
