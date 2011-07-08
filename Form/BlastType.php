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
use Symfony\Component\DependencyInjection\ContainerInterface;

use Genouest\Bundle\BlastBundle\Entity\BlastRequest;

class BlastType extends AbstractType
{
    protected $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('title');
        $builder->add('email');
        $builder->add('program', 'choice', array('choices' => BlastRequest::getProgramLabels()));
        $builder->add('blastnType', 'choice', array('choices' => BlastRequest::getBlastnTypeLabels()));
        $builder->add('blastpType', 'choice', array('choices' => BlastRequest::getBlastpTypeLabels()));
        $builder->add('pastedSeq', 'textarea', array('required' => false));
        $builder->add('fileSeq', 'file', array('required' => false));
        $builder->add('bankTypeNuc', 'choice', array('choices' => BlastRequest::getNucBankTypeLabels()));
        $builder->add('bankTypeProt', 'choice', array('choices' => BlastRequest::getProtBankTypeLabels()));
        $builder->add('persoBankFile', 'file', array('required' => false));
        $builder->add('dbPath', $this->container->getParameter('blast.db.list.widget'), $this->container->getParameter('blast.db.list.widget.options'));
        $builder->add('maxTargetSequences', 'choice', array('choices' => BlastRequest::getMaxTargetSequenceLabels()));
        $builder->add('expect', 'choice', array('choices' => BlastRequest::getExpectLabels()));
        $builder->add('wordSizesProt', 'choice', array('choices' => BlastRequest::getProtWordSizeLabels()));
        $builder->add('wordSizesBlastn', 'choice', array('choices' => BlastRequest::getBlastnWordSizeLabels()));
        $builder->add('wordSizesMegablast', 'choice', array('choices' => BlastRequest::getMegablastWordSizeLabels()));
        $builder->add('wordSizesDcMegablast', 'choice', array('choices' => BlastRequest::getDcMegablastWordSizeLabels()));
        $builder->add('matricesNuc', 'choice', array('choices' => BlastRequest::getNucMatriceLabels()));
        $builder->add('matricesMegablast', 'choice', array('choices' => BlastRequest::getMegablastMatriceLabels()));
        $builder->add('matricesProt', 'choice', array('choices' => BlastRequest::getProtMatriceLabels()));
        $builder->add('queryCode', 'choice', array('choices' => BlastRequest::getGeneticCodeLabels()));
        $builder->add('dbCode', 'choice', array('choices' => BlastRequest::getGeneticCodeLabels()));
        $builder->add('gapCostsBlastn', 'choice', array('choices' => BlastRequest::getBlastnGapCostLabels()));
        $builder->add('gapCostsMegablast', 'choice', array('choices' => BlastRequest::getMegablastGapCostLabels()));
        $builder->add('gapCostsProt', 'choice', array('choices' => BlastRequest::getProtGapCostLabels()));
        $builder->add('compositionalAdjustments', 'choice', array('choices' => BlastRequest::getCompositionalAdjustmentLabels()));
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
            'csrf_protection' => false
        );
    }

}

