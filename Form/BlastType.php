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

namespace Genouest\Bundle\BlastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Genouest\Bundle\BlastBundle\Entity\BlastRequest;

class BlastType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('title');
        $builder->add('email');
        $builder->add('program', 'choice', array('choices' => BlastRequest::getProgramLabels()));
        $builder->add('blastnType', 'choice', array('choices' => BlastRequest::getBlastnTypeLabels()));
        $builder->add('blastpType', 'choice', array('choices' => BlastRequest::getBlastpTypeLabels()));
        $builder->add('pastedSeq', 'textarea', array('required' => false));
        $builder->add('fileSeq', 'file', array('required' => false));
        $builder->add('bankTypeNuc', 'choice', array('choices' => BlastRequest::getNucBankTypeLabels(true)));
        $builder->add('bankTypeProt', 'choice', array('choices' => BlastRequest::getProtBankTypeLabels(true)));
        $builder->add('persoBankFile', 'file', array('required' => false));
        $builder->add('dbPath', 'biomaj', array('dbtype' => array('nucleic', 'proteic', 'genome/procaryotic', 'genome/eucaryotic'), 'dbformat' => 'blast', 'autoload' => false));
        $builder->add('maxTargetSequences', 'choice', array('choices' => BlastRequest::getMaxTargetSequenceLabels(true)));
        $builder->add('expect', 'choice', array('choices' => BlastRequest::getExpectLabels(true)));
        $builder->add('wordSizesProt', 'choice', array('choices' => BlastRequest::getProtWordSizeLabels(true)));
        $builder->add('wordSizesBlastn', 'choice', array('choices' => BlastRequest::getBlastnWordSizeLabels(true)));
        $builder->add('wordSizesMegablast', 'choice', array('choices' => BlastRequest::getMegablastWordSizeLabels(true)));
        $builder->add('wordSizesDcMegablast', 'choice', array('choices' => BlastRequest::getDcMegablastWordSizeLabels(true)));
        $builder->add('matricesNuc', 'choice', array('choices' => BlastRequest::getNucMatriceLabels(true)));
        $builder->add('matricesMegablast', 'choice', array('choices' => BlastRequest::getMegablastMatriceLabels(true)));
        $builder->add('matricesProt', 'choice', array('choices' => BlastRequest::getProtMatriceLabels(true)));
        $builder->add('queryCode', 'choice', array('choices' => BlastRequest::getGeneticCodeLabels(true)));
        $builder->add('dbCode', 'choice', array('choices' => BlastRequest::getGeneticCodeLabels(true)));
        $builder->add('gapCostsBlastn', 'choice', array('choices' => BlastRequest::getBlastnGapCostLabels(true)));
        $builder->add('gapCostsMegablast', 'choice', array('choices' => BlastRequest::getMegablastGapCostLabels(true)));
        $builder->add('gapCostsProt', 'choice', array('choices' => BlastRequest::getProtGapCostLabels(true)));
        $builder->add('compositionalAdjustments', 'choice', array('choices' => BlastRequest::getCompositionalAdjustmentLabels(true)));
        $builder->add('lowComplex');
        $builder->add('softMasking');
        $builder->add('lowerCase');
        $builder->add('psiThreshold', 'number', array('precision' => 10));
        $builder->add('psiIterationNb', 'integer');
        $builder->add('psiPseudoCount', 'integer');
        $builder->add('psiPSSM', 'file', array('required' => false));
        $builder->add('phiPattern', 'file', array('required' => false));
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Genouest\Bundle\BlastBundle\Entity\BlastRequest',
            'csrf_protection' => false
        );
    }

}

