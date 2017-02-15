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

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;

use Genouest\Bundle\SchedulerBundle\Entity\Job;
use Genouest\Bundle\SchedulerBundle\Entity\ResultFile;
use Genouest\Bundle\SchedulerBundle\Entity\ResultViewer;
use Genouest\Bundle\SchedulerBundle\Scheduler\SchedulerInterface;

class BlastRequest implements BlastRequestInterface
{
    /**
     * @Assert\Length(max = 255)
     */
    public $title;

    /**
     * @Assert\Email
     */
    public $email;

    /**
     * @Assert\Choice(callback = "getPrograms")
     * @Assert\NotBlank
     */
    public $program = 'blastn';

    /**
     * @Assert\Choice(callback = "getBlastnTypes")
     * @Assert\NotBlank
     */
    public $blastnType = 'blastn';

    /**
     * @Assert\Choice(callback = "getBlastpTypes")
     * @Assert\NotBlank
     */
    public $blastpType = 'blastp';

    /**
     * @Genouest\Bundle\BioinfoBundle\Constraints\Fasta(seqType = "PROT_OR_ADN")
     */
    public $pastedSeq;

    /**
     * @Genouest\Bundle\BioinfoBundle\Constraints\FastaFile(maxSize = "104857600", seqType = "PROT_OR_ADN")
     */
    public $fileSeq;

    /**
     * @Assert\Choice(callback = "getNucBankTypes")
     * @Assert\NotBlank
     */
    public $bankTypeNuc = 'pubdb';

    /**
     * @Assert\Choice(callback = "getProtBankTypes")
     * @Assert\NotBlank
     */
    public $bankTypeProt = 'pubdb';

    /**
     * @Genouest\Bundle\BioinfoBundle\Constraints\FastaFile(maxSize = "104857600", seqType = "PROT_OR_ADN")
     */
    public $persoBankFile;

    public $dbPath;

    /**
     * @Assert\Choice(callback = "getMaxTargetSequenceChoices")
     * @Assert\NotBlank
     */
    public $maxTargetSequences = '100';

    /**
     * @Assert\Choice(callback = "getExpects")
     * @Assert\NotBlank
     */
    public $expect = '0.1';

    /**
     * @Assert\Choice(callback = "getProtWordSizes")
     * @Assert\NotBlank
     */
    public $wordSizesProt = '3';

    /**
     * @Assert\Choice(callback = "getBlastnWordSizes")
     * @Assert\NotBlank
     */
    public $wordSizesBlastn = '11';

    /**
     * @Assert\Choice(callback = "getMegablastWordSizes")
     * @Assert\NotBlank
     */
    public $wordSizesMegablast = '28';

    /**
     * @Assert\Choice(callback = "getDcMegablastWordSizes")
     * @Assert\NotBlank
     */
    public $wordSizesDcMegablast = '11';

    /**
     * @Assert\Choice(callback = "getNucMatrices")
     * @Assert\NotBlank
     */
    public $matricesNuc = '2,-3';

    /**
     * @Assert\Choice(callback = "getMegablastMatrices")
     * @Assert\NotBlank
     */
    public $matricesMegablast = '1,-2';

    /**
     * @Assert\Choice(callback = "getProtMatrices")
     * @Assert\NotBlank
     */
    public $matricesProt = "BLOSUM62";

    /**
     * @Assert\Choice(callback = "getGeneticCodes")
     * @Assert\NotBlank
     */
    public $queryCode = '1';

    /**
     * @Assert\Choice(callback = "getGeneticCodes")
     * @Assert\NotBlank
     */
    public $dbCode = '1';

    /**
     * @Assert\Choice(callback = "getBlastnGapCosts")
     * @Assert\NotBlank
     */
    public $gapCostsBlastn = '5,2';

    /**
     * @Assert\Choice(callback = "getProtGapCosts")
     * @Assert\NotBlank
     */
    public $gapCostsProt = '11,1';

    /**
     * @Assert\Choice(callback = "getTemplateTypeChoices")
     * @Assert\NotBlank
     */
    public $templateTypes = 'coding';

    /**
     * @Assert\Choice(callback = "getTemplateLengthChoices")
     * @Assert\NotBlank
     */
    public $templateLengths = '18';

    /**
     * @Assert\Choice(callback = "getCompositionalAdjustmentChoices")
     * @Assert\NotBlank
     */
    public $compositionalAdjustments = '2';

    /**
     * @Assert\Choice(callback = "getCompositionalAdjustmentDeltaChoices")
     * @Assert\NotBlank
     */
    public $compositionalAdjustmentsDelta = '2';

    /**
     * @Assert\Type("bool")
     */
    public $lowComplex = true;

    /**
     * @Assert\Type("bool")
     */
    public $softMasking = true;

    /**
     * @Assert\Type("bool")
     */
    public $lowerCase = false;

    /**
     * @Assert\Type("float")
     * @Assert\Range(min = 0, max = 1000)
     */
    public $psiThreshold = 0.005;

    /**
     * @Assert\Type("float")
     * @Assert\Range(min = 0, max = 1000)
     */
    public $deltaThreshold = 0.05;

    /**
     * @Assert\Type("integer")
     * @Assert\Range(min = 0, max = 1000)
     */
    public $psiIterationNb = 10;

    /**
     * @Assert\Type("integer")
     * @Assert\Range(min = 0, max = 1000)
     */
    public $psiPseudoCount = 0;

    /**
     * @Assert\File(maxSize = "104857600")
     */
    public $psiPSSM;

    /**
     * @Assert\File(maxSize = "104857600")
     */
    public $phiPattern;

    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * Validate dbPath with validator set as a service
     */

    /**
     * @Assert\Callback
     */
    public function isDbPathValid(ExecutionContextInterface $context)
    {
        if ($this->hasPersoDb())
            return; // Nothing to check if using a custom db

        $validator = $this->container->get('blast.db.list.constraint.validator');
        $constraint = $this->container->get('blast.db.list.constraint');
        $validator->initialize($context);

        // check if the name is actually a fake name
        $validator->validate($this->dbPath, $constraint);
        $violations = $context->getViolations();
        $c = 0;
        foreach ($violations as $viol) {
            $context->addViolationAt(
                'dbPath',
                $viol->getMessageTemplate(),
                $viol->getParameters(),
                $this->dbPath);
            $violations->remove($c);
            $c++;
        }
    }

    /**
     * @Assert\IsTrue(message = "Please paste or upload a query sequence")
     */
    public function isSequencePresent() {
        if ($this->program != 'blastp' || $this->blastpType != 'psiblast')
            return !empty($this->pastedSeq) || !empty($this->fileSeq);

        return true;
    }

    /**
     * @Assert\IsTrue(message = "Please paste or upload a query sequence, or upload a PSSM")
     */
    public function isSequencePresentForPsi() {
        if ($this->program == 'blastp' && $this->blastpType == 'psiblast')
            return !empty($this->pastedSeq) || !empty($this->fileSeq) || !empty($this->psiPSSM);

        return true;
    }

    /**
     * @Assert\IsTrue(message = "You have to choose between pasting or uploading a query sequence")
     */
    public function isSequenceSingle() {
        return ($this->isSequencePresent() && (empty($this->pastedSeq) || empty($this->fileSeq))) || !$this->isSequencePresent();
    }

    /**
     * @Assert\IsTrue(message = "You have to choose between pasting a query sequence, uploading it, or uploading a PSSM")
     */
    public function isSequenceSingleForPsi() {
        if ($this->program == 'blastp' && $this->blastpType == 'psiblast')
            return ($this->isSequencePresentForPsi() && (intval(!empty($this->pastedSeq)) + intval(!empty($this->fileSeq)) + intval(!empty($this->psiPSSM)) == 1)) || !$this->isSequencePresentForPsi();

        return true;
    }

    /**
     * @Assert\IsTrue(message = "Please upload a personal databank, or choose a public one")
     */
    public function isDatabankOk() {
        if ($this->hasPersoDb())
            return !empty($this->persoBankFile);
        else
            return !empty($this->dbPath);

        return true;
    }

    /**
     * Returns true if this blast request uses a personal databank
     */
    public function hasPersoDb() {
        return (in_array($this->program, array('blastn', 'tblastn')) && $this->bankTypeNuc == 'persodb') || (!in_array($this->program, array('blastn', 'tblastn')) && $this->bankTypeProt == 'persodb');
    }

    public static function getProgramLabels()
    {
        return array('blastn' => 'Blastn (nucleotide query, nucleotide databank)',
                      'blastp' => 'Blastp (protein query, protein databank)',
                      'blastx' => 'Blastx (nucleotide query, protein databank)',
                      'tblastn' => 'Tblastn (protein query, nucleotide databank)',
                      'tblastx' => 'Tblastx (nucleotide query, nucleotide databank, but at protein level)');
    }

    public static function getPrograms()
    {
        return array_keys(self::getProgramLabels());
    }


    public static function getBlastnTypeLabels()
    {
        return array('megablast' => 'Highly similar sequences (megablast)',
                      'dc-megablast' => 'More dissimilar sequences (discontiguous megablast)',
                      'blastn' => 'Somewhat similar sequences (blastn)');
    }

    public static function getBlastnTypes()
    {
        return array_keys(self::getBlastnTypeLabels());
    }

    public static function getBlastpTypeLabels($cdd_delta)
    {
        $options = array('blastp' => 'Normal blastp',
                      'psiblast' => 'PSI-BLAST (Position-Specific Iterated BLAST)',
                      'phiblast' => 'PHI-BLAST (Pattern Hit Initiated BLAST)');

        if (!empty($cdd_delta)) {
            $options['deltablast'] = 'DELTA-BLAST (Domain Enhanced Lookup Time Accelerated BLAST)';
        }
        return $options;
    }

    public static function getBlastpTypes()
    {
        return array_keys(self::getBlastpTypeLabels('yes')); // Passing yes to ensure deltablast will be accepted
    }

    public static function getNucBankTypeLabels()
    {
        return array('pubdb' => 'Public databank',
                    'persodb' => 'Personal databank');
    }

    public static function getNucBankTypes()
    {
        return array_keys(self::getNucBankTypeLabels());
    }

    public static function getProtBankTypeLabels()
    {
        return array('pubdb' => 'Public databank',
                    'persodb' => 'Personal databank');
    }

    public static function getProtBankTypes()
    {
        return array_keys(self::getProtBankTypeLabels());
    }

    public static function getExpectLabels()
    {
        return array('1e-20' => '1e-20',
                       '1e-10' => '1e-10',
                       '0.0001' => '0.0001',
                       '0.01' => '0.01',
                       '0.1' => '0.1',
                       '1' => '1',
                       '10' => '10',
                       '100' => '100',
                       '1000' => '1000');
    }

    public static function getExpects()
    {
        // The choice validator uses in_array() in strict mode which means the type is important.
        // array_keys converts string keys that looks like integer to int in the resulting array.
        // So we're forced to check that the array_keys result contains only strings.
        return array_map(create_function('$value', 'return (string) $value;'), array_keys(self::getExpectLabels()));
    }

    public static function getNucMatriceLabels()
    {
        return array('1,-2' => '1,-2',
                       '1,-3' => '1,-3',
                       '1,-4' => '1,-4',
                       '2,-3' => '2,-3',
                       '4,-5' => '4,-5',
                       '1,-1' => '1,-1');
    }

    public static function getNucMatrices()
    {
        return array_keys(self::getNucMatriceLabels());
    }

    public static function getMegablastMatriceLabels()
    {
        return array('1,-2' => '1,-2',
                     '1,-3' => '1,-3',
                     '1,-4' => '1,-4',
                     '2,-3' => '2,-3',
                     '4,-5' => '4,-5',
                     '1,-1' => '1,-1');
    }

    public static function getMegablastMatrices()
    {
        return array_keys(self::getMegablastMatriceLabels());
    }

    public static function getProtMatriceLabels()
    {
        return array('PAM30' => 'PAM30',
                    'PAM70' => 'PAM70',
                    'BLOSUM80' => 'BLOSUM80',
                    'BLOSUM62' => 'BLOSUM62',
                    'BLOSUM45' => 'BLOSUM45');
    }

    public static function getProtMatrices()
    {
        return array_keys(self::getProtMatriceLabels());
    }

    public static function getGeneticCodeLabels()
    {
        return array('1' => 'Standard',
                    '2' => 'Vertebrate Mitochondrial',
                    '3' => 'Yeast Mitochondrial',
                    '4' => 'Mold Mitochondrial; ...',
                    '5' => 'Invertebrate Mitochondrial',
                    '6' => 'Ciliate Nuclear; ...',
                    '9' => 'Echinoderm Mitochondrial',
                    '10' => 'Euplotid Nuclear',
                    '11' => 'Bacteria and Archaea',
                    '12' => 'Alternative Yeast Nuclear',
                    '13' => 'Ascidian Mitochondrial',
                    '14' => 'Flatworm Mitochondrial',
                    '15' => 'Blepharisma Macrouclear');
    }

    public static function getGeneticCodes()
    {
        // The choice validator uses in_array() in strict mode which means the type is important.
        // array_keys converts string keys that looks like integer to int in the resulting array.
        // So we're forced to check that the array_keys result contains only strings.
        return array_map(create_function('$value', 'return (string) $value;'), array_keys(self::getGeneticCodeLabels()));
    }

    public static function getMaxTargetSequenceLabels()
    {
        return array('10' => '10',
                      '50' => '50',
                      '100' => '100',
                      '250' => '250',
                      '500' => '500',
                      '1000' => '1000',
                      '5000' => '5000',
                      '10000' => '10000',
                      '20000' => '20000');
    }

    public static function getMaxTargetSequenceChoices()
    {
        // The choice validator uses in_array() in strict mode which means the type is important.
        // array_keys converts string keys that looks like integer to int in the resulting array.
        // So we're forced to check that the array_keys result contains only strings.
        return array_map(create_function('$value', 'return (string) $value;'), array_keys(self::getMaxTargetSequenceLabels()));
    }

    public static function getProtWordSizeLabels()
    {
        return array('2' => '2',
                     '3' => '3');
    }

    public static function getProtWordSizes()
    {
        // The choice validator uses in_array() in strict mode which means the type is important.
        // array_keys converts string keys that looks like integer to int in the resulting array.
        // So we're forced to check that the array_keys result contains only strings.
        return array_map(create_function('$value', 'return (string) $value;'), array_keys(self::getProtWordSizeLabels()));
    }

    public static function getBlastnWordSizeLabels()
    {
        return array('7' => '7',
                       '11' => '11',
                       '15' => '15');
    }

    public static function getBlastnWordSizes()
    {
        // The choice validator uses in_array() in strict mode which means the type is important.
        // array_keys converts string keys that looks like integer to int in the resulting array.
        // So we're forced to check that the array_keys result contains only strings.
        return array_map(create_function('$value', 'return (string) $value;'), array_keys(self::getBlastnWordSizeLabels()));
    }

    public static function getMegablastWordSizeLabels()
    {
        return array('16' => '16',
                      '20' => '20',
                      '24' => '24',
                      '28' => '28',
                      '32' => '32',
                      '48' => '48',
                      '64' => '64',
                      '128' => '128',
                      '256' => '256');
    }

    public static function getMegablastWordSizes()
    {
        // The choice validator uses in_array() in strict mode which means the type is important.
        // array_keys converts string keys that looks like integer to int in the resulting array.
        // So we're forced to check that the array_keys result contains only strings.
        return array_map(create_function('$value', 'return (string) $value;'), array_keys(self::getMegablastWordSizeLabels()));
    }

    public static function getDcMegablastWordSizeLabels()
    {
        return array('11' => '11',
                        '12' => '12');
    }

    public static function getDcMegablastWordSizes()
    {
        // The choice validator uses in_array() in strict mode which means the type is important.
        // array_keys converts string keys that looks like integer to int in the resulting array.
        // So we're forced to check that the array_keys result contains only strings.
        return array_map(create_function('$value', 'return (string) $value;'), array_keys(self::getDcMegablastWordSizeLabels()));
    }

    public static function getBlastnGapCostLabels()
    {
        return array(
                      '12,8' => 'Creation: 12 Extension: 8',
                      '6,5' => 'Creation: 6 Extension: 5',
                      '5,5' => 'Creation: 5 Extension: 5',
                      '4,5' => 'Creation: 4 Extension: 5',
                      '3,5' => 'Creation: 3 Extension: 5',
                      '4,4' => 'Creation: 4 Extension: 4',
                      '2,4' => 'Creation: 2 Extension: 4',
                      '0,4' => 'Creation: 0 Extension: 4',
                      '3,3' => 'Creation: 3 Extension: 3',
                      '6,2' => 'Creation: 6 Extension: 2',
                      '5,2' => 'Creation: 5 Extension: 2',
                      '4,2' => 'Creation: 4 Extension: 2',
                      '3,2' => 'Creation: 3 Extension: 2',
                      '2,2' => 'Creation: 2 Extension: 2',
                      '1,2' => 'Creation: 1 Extension: 2',
                      '0,2' => 'Creation: 0 Extension: 2',
                      '4,1' => 'Creation: 4 Extension: 1',
                      '3,1' => 'Creation: 3 Extension: 1',
                      '2,1' => 'Creation: 2 Extension: 1',
                      '1,1' => 'Creation: 1 Extension: 1',
                    );
    }

    public static function getBlastnGapCosts()
    {
        return array_keys(self::getBlastnGapCostLabels());
    }

    public static function getProtGapCostLabels()
    {
        return array(
                    '13,3' => 'Creation: 13 Extension: 3',
                    '12,3' => 'Creation: 12 Extension: 3',
                    '11,3' => 'Creation: 11 Extension: 3',
                    '10,3' => 'Creation: 10 Extension: 3',
                    '15,2' => 'Creation: 15 Extension: 2',
                    '14,2' => 'Creation: 14 Extension: 2',
                    '13,2' => 'Creation: 13 Extension: 2',
                    '12,2' => 'Creation: 12 Extension: 2',
                    '9,2' => 'Creation: 9 Extension: 2',
                    '8,2' => 'Creation: 8 Extension: 2',
                    '7,2' => 'Creation: 7 Extension: 2',
                    '6,2' => 'Creation: 6 Extension: 2',
                    '5,2' => 'Creation: 5 Extension: 2',
                    '19,1' => 'Creation: 19 Extension: 1',
                    '18,1' => 'Creation: 18 Extension: 1',
                    '17,1' => 'Creation: 17 Extension: 1',
                    '16,1' => 'Creation: 16 Extension: 1',
                    '12,1' => 'Creation: 12 Extension: 1',
                    '11,1' => 'Creation: 11 Extension: 1',
                    '10,1' => 'Creation: 10 Extension: 1',
                    '9,1' => 'Creation: 9 Extension: 1',
                    '8,1' => 'Creation: 8 Extension: 1',
                    );
    }

    public static function getProtGapCosts()
    {
        return array_keys(self::getProtGapCostLabels());
    }

    public static function getTemplateTypeLabels()
    {
        return array('coding' => 'Coding',
                    'optimal' => 'Maximal',
                    'coding_and_optimal' => 'Two templates');
    }

    public static function getTemplateLengthLabels()
    {
        return array('0' => 'None',
                    '16' => '16',
                    '18' => '18',
                    '21' => '21');
    }

    public static function getCompositionalAdjustmentLabels()
    {
        return array('0' => 'No adjustment',
                    '1' => 'Composition-based statistics',
                    '2' => 'Conditional compositional score matrix adjustment',
                    '3' => 'Universal compositional score matrix adjustment');
    }

    public static function getCompositionalAdjustmentDeltaLabels()
    {
        return array('0' => 'No adjustment',
                    '1' => 'Composition-based statistics');
    }

    public static function getTemplateTypeChoices()
    {
        // The choice validator uses in_array() in strict mode which means the type is important.
        // array_keys converts string keys that looks like integer to int in the resulting array.
        // So we're forced to check that the array_keys result contains only strings.
        return array_map(create_function('$value', 'return (string) $value;'), array_keys(self::getTemplateTypeLabels()));
    }

    public static function getTemplateLengthChoices()
    {
        // The choice validator uses in_array() in strict mode which means the type is important.
        // array_keys converts string keys that looks like integer to int in the resulting array.
        // So we're forced to check that the array_keys result contains only strings.
        return array_map(create_function('$value', 'return (string) $value;'), array_keys(self::getTemplateLengthLabels()));
    }

    public static function getCompositionalAdjustmentChoices()
    {
        // The choice validator uses in_array() in strict mode which means the type is important.
        // array_keys converts string keys that looks like integer to int in the resulting array.
        // So we're forced to check that the array_keys result contains only strings.
        return array_map(create_function('$value', 'return (string) $value;'), array_keys(self::getCompositionalAdjustmentLabels()));
    }

    public static function getCompositionalAdjustmentDeltaChoices()
    {
        // The choice validator uses in_array() in strict mode which means the type is important.
        // array_keys converts string keys that looks like integer to int in the resulting array.
        // So we're forced to check that the array_keys result contains only strings.
        return array_map(create_function('$value', 'return (string) $value;'), array_keys(self::getCompositionalAdjustmentDeltaLabels()));
    }

    /**
     * Generate a Job object corresponding to this blast request
     *
     * @param Genouest\Bundle\SchedulerBundle\Scheduler\SchedulerInterface A job scheduler instance
     * @return Genouest\Bundle\SchedulerBundle\Entity\Job A job instance
     */
    public function getJob(SchedulerInterface $scheduler) {

        $job = new Job();
        $job->setProgramName($this->container->getParameter('blast.scheduler.name')); // It is important to set program name *before* generating the uid
        $uid = $job->generateJobUid();
        $job->setTitle($this->title);
        $job->setEmail($this->email);

        $workDir = $scheduler->getWorkDir($job);

        $command = $this->container->get('templating')->render('GenouestBlastBundle:Blast:command.txt.twig', array('request' => $this,
            'workDir' => $workDir,
            'job' => $job,
            'cdd_delta' => $this->container->getParameter('blast.cdd_delta.path'),
            'pre_command' => $this->container->getParameter('blast.pre_command'),
            'link_command' => $this->container->getParameter('blast.link_command')
            ));

        // Create an array containing the name of each result file
        $resultFiles = array();
        $resultViewers = array();

        if ($this->program == 'blastp' && $this->blastpType == 'phiblast') {
            $resultFiles = array('HTML blast output' => $uid.'.html',
                                 'Executed command' => "blast_command.txt");
        }
        else {
            $resultFiles = array('HTML blast output' => $uid.'.html',
                                   'GFF3 blast output' => $uid.'.gff3',
                                   'Text blast output' => $uid.'.txt',
                                   'Comma-separated blast output' => $uid.'.csv',
                                   'Tabular blast output' => $uid.'.tsv',
                                   'XML blast output' => $uid.'.xml',
                                   'ASN.1 archive blast output' => $uid.'.asn',
                                   'Executed command' => "blast_command.txt");
        }

        if ($this->program == 'blastp' && (($this->blastpType == 'psiblast') || ($this->blastpType == 'deltablast'))) {
            $resultFiles['PSSM'] = $uid.'.pssm';
            $resultFiles['PSSM ASCII'] = $uid.'.pssm.ascii';
        }

        // Move uploaded files
        if ($this->hasPersoDb())
            $this->persoBankFile->move($workDir, 'uploadedDB.fasta');
        if (!empty($this->fileSeq))
            $this->fileSeq->move($workDir, 'input.fasta');
        if (!empty($this->pastedSeq)) {
            $seqFile = @fopen($workDir.'input.fasta','w');
            $fError = $seqFile === false;
            if ($seqFile) {
                $fError = $fError || (false === @fwrite($seqFile, $this->pastedSeq));
                $fError = $fError || (false === @fclose($seqFile));
            }

            if ($fError) {
                $error = error_get_last();
                throw new FileException(sprintf('Could not create file %s (%s)', $workDir.'input.fasta', strip_tags($error['message'])));
            }
        }
        if ($this->blastpType == 'psiblast' && $this->psiPSSM)
            $this->psiPSSM->move($workDir, 'input.pssm');
        if ($this->blastpType == 'phiblast' && $this->phiPattern)
            $this->phiPattern->move($workDir, 'input.pattern');

        // Save files and viewers in db
        $job->addResultFilesArray($resultFiles);
        $job->addResultViewersArray($resultViewers);

        // Store generated command line
        $job->setCommand($command);

        return $job;
    }
}
