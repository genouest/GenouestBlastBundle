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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Genouest\Bundle\BlastBundle\Entity\BlastRequest;

class BlastType extends AbstractType
{
    protected $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
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
        $builder->add('dbPath', $this->container->get('blast.db.list.provider')->getWidgetType(), $this->container->get('blast.db.list.provider')->getWidgetOptions());
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
        $builder->add('gapCostsProt', 'choice', array('choices' => BlastRequest::getProtGapCostLabels()));
        $builder->add('templateTypes', 'choice', array('choices' => BlastRequest::getTemplateTypeLabels()));
        $builder->add('templateLengths', 'choice', array('choices' => BlastRequest::getTemplateLengthLabels()));
        $builder->add('compositionalAdjustments', 'choice', array('choices' => BlastRequest::getCompositionalAdjustmentLabels()));
        $builder->add('compositionalAdjustmentsDelta', 'choice', array('choices' => BlastRequest::getCompositionalAdjustmentDeltaLabels()));
        $builder->add('lowComplex');
        $builder->add('softMasking');
        $builder->add('lowerCase');
        $builder->add('psiThreshold', 'number', array('precision' => 10));
        $builder->add('deltaThreshold', 'number', array('precision' => 10));
        $builder->add('psiIterationNb', 'integer');
        $builder->add('psiPseudoCount', 'integer');
        $builder->add('psiPSSM', 'file', array('required' => false));
        $builder->add('phiPattern', 'file', array('required' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'error_mapping' => array(
                'sequencePresent' => 'pastedSeq',
                'sequencePresentForPsi' => 'pastedSeq',
                'sequenceSingle' => 'pastedSeq',
                'sequenceSingleForPsi' => 'pastedSeq',
                'databankOk' => 'bankTypeNuc',
                'dbPathValid' => 'bankTypeNuc',
            ),
        ));
    }
    
    public function getName()
    {
        return 'blast';
    }

}

