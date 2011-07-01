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
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class FastaValidator extends ConstraintValidator
{
    static protected $seqTypes = array('ADN' => SequenceUtils::CHECK_ADN, 'PROT' => SequenceUtils::CHECK_PROTEIC, 'PROT_OR_ADN' => SequenceUtils::CHECK_PROTEIC_OR_ADN, 'PROSITE' => SequenceUtils::CHECK_PROSITE);

    public function isValid($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return true;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString()'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string)$value;
        
        $qualifiedSeqType = self::$seqTypes[$constraint->seqType];
        
        $seqUtils = new SequenceUtils();
        $seqError = $seqUtils->checkSequence($value, SequenceUtils::CHECK_WORD | $qualifiedSeqType, true);
        
        if (!empty($seqError)) {
            $this->setMessage($constraint->message);
            
            return false;
        }

        return true;
    }
}
