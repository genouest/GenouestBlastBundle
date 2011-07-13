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
    
    public function isValid($value, Constraint $constraint)
    {
        if (null === $value) {
            return true;
        }

        $choices = $this->container->get('blast.db.list.provider')->getAllDatabanks();
        $choices = array_keys($choices);

        if (!in_array($value, $choices, true)) {
            $this->setMessage($constraint->message, array('{{ value }}' => $value));

            return false;
        }

        return true;
    }
}
