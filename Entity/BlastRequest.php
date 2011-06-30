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

use Genouest\Bundle\SchedulerBundle\Entity\Job;
use Genouest\Bundle\SchedulerBundle\Entity\ResultFile;
use Genouest\Bundle\SchedulerBundle\Entity\ResultViewer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use Symfony\Component\Validator\Constraints as Assert;

class BlastRequest
{
    /**
     * @Assert\MaxLength(255)
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
     * @Genouest\Bundle\CommonsBundle\Constraints\Fasta(seqType = "PROT_OR_ADN")
     */
    public $pastedSeq;
    
    /**
     * @Genouest\Bundle\CommonsBundle\Constraints\FastaFile(maxSize = "104857600", seqType = "PROT_OR_ADN")
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
     * @Genouest\Bundle\CommonsBundle\Constraints\FastaFile(maxSize = "104857600", seqType = "PROT_OR_ADN")
     */
    public $persoBankFile;
    
    /**
     * @Genouest\Bundle\CommonsBundle\Constraints\Biomaj(dbtype = {"nucleic", "proteic", "genome/procaryotic", "genome/eucaryotic"}, dbformat = "blast"))
     */
    public $dbPath;

    /**
     * @Assert\Choice(callback = "getMaxTargetSequences")
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
     * @Assert\Choice(callback = "getMegablastGapCosts")
     * @Assert\NotBlank
     */
    public $gapCostsMegablast = 'linear';

    /**
     * @Assert\Choice(callback = "getProtGapCosts")
     * @Assert\NotBlank
     */
    public $gapCostsProt = '11,1';

    /**
     * @Assert\Choice(callback = "getCompositionalAdjustments")
     * @Assert\NotBlank
     */
    public $compositionalAdjustments = '2';
    
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
     * @Assert\Min(0)
     * @Assert\Max(1000)
     */
    public $psiThreshold = 0.005;
    
    /**
     * @Assert\Type("integer")
     * @Assert\Min(0)
     * @Assert\Max(1000)
     */
    public $psiIterationNb = 10;
    
    /**
     * @Assert\Type("integer")
     * @Assert\Min(0)
     * @Assert\Max(1000)
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
    
    /**
     * @Assert\True(message = "Please paste or upload a query sequence")
     */
    public function isSequencePresent() {
        if ($this->program != 'blastp' || $this->blastpType != 'psiblast')
            return !empty($this->pastedSeq) || !empty($this->fileSeq);

        return true;
    }
    
    /**
     * @Assert\True(message = "Please paste or upload a query sequence, or upload a PSSM")
     */
    public function isSequencePresentForPsi() {
        if ($this->program == 'blastp' && $this->blastpType == 'psiblast')
            return !empty($this->pastedSeq) || !empty($this->fileSeq) || !empty($this->psiPSSM);

        return true;
    }
    
    /**
     * @Assert\True(message = "You have to choose between pasting or uploading a query sequence")
     */
    public function isSequenceSingle() {
        return ($this->isSequencePresent() && (empty($this->pastedSeq) || empty($this->fileSeq))) || !$this->isSequencePresent();
    }
    
    /**
     * @Assert\True(message = "You have to choose between pasting a query sequence, uploading it, or uploading a PSSM")
     */
    public function isSequenceSingleForPsi() {
        if ($this->program == 'blastp' && $this->blastpType == 'psiblast')
            return ($this->isSequencePresentForPsi() && (!empty($this->pastedSeq) XOR !empty($this->fileSeq) XOR !empty($this->psiPSSM))) || !$this->isSequencePresentForPsi();

        return true;
    }
    
    /**
     * @Assert\True(message = "Please upload a personal databank, or choose a public one")
     */
    public function isDatabankOk() {
        if ($this->hasPersoDb())
            return !empty($this->persoBankFile);
        
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
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getProgramLabels()));
    }
    
    
    public static function getBlastnTypeLabels()
    {
        return array('megablast' => 'Highly similar sequences (megablast)',
                      'dc-megablast' => 'More dissimilar sequences (discontiguous megablast)',
                      'blastn' => 'Somewhat similar sequences (blastn)');
    }
    
    public static function getBlastnTypes()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getBlastnTypeLabels()));
    }
    
    public static function getBlastpTypeLabels()
    {
        return array('blastp' => 'Normal blastp',
                      'psiblast' => 'PSI-BLAST (Position-Specific Iterated BLAST)',
                      'phiblast' => 'PHI-BLAST (Pattern Hit Initiated BLAST)');
    }
    
    public static function getBlastpTypes()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getBlastpTypeLabels()));
    }
    
    public static function getNucBankTypeLabels()
    {
        return array('pubdb' => 'Public databank',
                    'procgenome' => 'Procaryotic genome',
                    'eucgenome' => 'Eucaryotic genome',
                    'persodb' => 'Personal databank');
    }
    
    public static function getNucBankTypes()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getNucBankTypeLabels()));
    }
    
    public static function getProtBankTypeLabels()
    {
        return array('pubdb' => 'Public databank',
                    'persodb' => 'Personal databank');
    }
    
    public static function getProtBankTypes()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getProtBankTypeLabels()));
    }
    
    public static function getExpectLabels()
    {
        return array('1e-20' => '1e-20',
                       '1e-10' => '1e-10',
                       '0.0001' => '0.0001',
                       '0.01' => '0.01',
                       '0.1' => '0.1',
                       '1' => '1',
                       '10' => '10');
    }
    
    public static function getExpects()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getExpectLabels()));
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
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getNucMatriceLabels()));
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
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getMegablastMatriceLabels()));
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
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getProtMatriceLabels()));
    }
    
    public static function getGeneticCodeLabels()
    {
        return array('1' => 'Standard',
                    '2' => 'Vertebrate Mitochondrial',
                    '3' => 'Yeast Mitochondrial',
                    '4' => 'Mold Mitochondrial',
                    '5' => 'Invertebrate Mitochondrial',
                    '6' => 'Ciliate Nuclear',
                    '9' => 'Echinoderm and Flatworm Mitochondrial',
                    '10' => 'Euplotid Nuclear',
                    '11' => 'Bacterial, Archaeal and Plant Plastid',
                    '12' => 'Alternative Yeast Nuclear',
                    '13' => 'Ascidian Mitochondrial',
                    '14' => 'Alternative Flatworm Mitochondrial',
                    '15' => 'Blepharisma Nuclear',
                    '16' => 'Chlorophycean Mitochondrial',
                    '21' => 'Trematode Mitochondrial',
                    '22' => 'Scenedesmus obliquus mitochondrial',
                    '23' => 'Thraustochytrium Mitochondrial');
    }
    
    public static function getGeneticCodes()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getGeneticCodeLabels()));
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
    
    public static function getMaxTargetSequences()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getMaxTargetSequenceLabels()));
    }
    
    public static function getProtWordSizeLabels()
    {
        return array('2' => '2',
                     '3' => '3');
    }
    
    public static function getProtWordSizes()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getProtWordSizeLabels()));
    }
    
    public static function getBlastnWordSizeLabels()
    {
        return array('7' => '7',
                       '11' => '11',
                       '15' => '15');
    }
    
    public static function getBlastnWordSizes()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getBlastnWordSizeLabels()));
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
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getMegablastWordSizeLabels()));
    }
    
    public static function getDcMegablastWordSizeLabels()
    {
        return array('11' => '11',
                        '12' => '12');
    }
    
    public static function getDcMegablastWordSizes()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getDcMegablastWordSizeLabels()));
    }
    
    public static function getBlastnGapCostLabels()
    {
        return array('4,4' => 'Creation: 4 Extension: 4',
                      '2,4' => 'Creation: 2 Extension: 4',
                      '0,4' => 'Creation: 0 Extension: 4',
                      '3,3' => 'Creation: 3 Extension: 3',
                      '6,2' => 'Creation: 6 Extension: 2',
                      '5,2' => 'Creation: 5 Extension: 2',
                      '4,2' => 'Creation: 4 Extension: 2',
                      '2,2' => 'Creation: 2 Extension: 2');
    }
    
    public static function getBlastnGapCosts()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getBlastnGapCostLabels()));
    }
    
    public static function getMegablastGapCostLabels()
    {
        return array('0,0' => 'Linear',
                     '5,2' => 'Creation: 5 Extension: 2',
                     '2,2' => 'Creation: 2 Extension: 2',
                     '1,2' => 'Creation: 1 Extension: 2',
                     '0,2' => 'Creation: 0 Extension: 2',
                     '3,1' => 'Creation: 3 Extension: 1',
                     '2,1' => 'Creation: 2 Extension: 1',
                     '1,1' => 'Creation: 1 Extension: 1');
    }
    
    public static function getMegablastGapCosts()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getMegablastGapCostLabels()));
    }
    
    public static function getProtGapCostLabels()
    {
        return array('9,2' => 'Creation: 9 Extension: 2',
                    '8,2' => 'Creation: 8 Extension: 2',
                    '7,2' => 'Creation: 7 Extension: 2',
                    '12,1' => 'Creation: 12 Extension: 1',
                    '11,1' => 'Creation: 11 Extension: 1',
                    '10,1' => 'Creation: 10 Extension: 1');
    }
    
    public static function getProtGapCosts()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getProtGapCostLabels()));
    }
    
    public static function getCompositionalAdjustmentLabels()
    {
        return array('0' => 'No adjustment',
                    '1' => 'Composition-based statistics',
                    '2' => 'Conditional compositional score matrix adjustment',
                    '3' => 'Universal compositional score matrix adjustment');
    }
    
    public static function getCompositionalAdjustments()
    {
        return array_map(create_function('$value', 'return (string) $value;'),array_keys(self::getCompositionalAdjustmentLabels()));
    }
    
    /**
     * Generate a Job object corresponding to this blast request
     *
     * @param Genouest\Bundle\SchedulerBundle\Scheduler\SchedulerInterface A job scheduler instance
     * @param string $back_url The url to get back to this application
     * @return Genouest\Bundle\SchedulerBundle\Entity\Job A job instance
     */
    public function getJob($scheduler, $back_url) {
        $job = new Job();
        $job->setProgramName('blast'); // It is important to set program name *before* generating the uid
        $outputFilePrefix = $job->generateJobUid();
        $job->setTitle($this->title);
        $job->setEmail($this->email);
        $job->setBackUrl($back_url);// Add an url to come back to the application
        
        $jobCommand = ". /local/env/envblast+.sh;";
    
        // Construct the job command
        $formatdbCommand = "";
        $dbPath = "";
        $workDir = $scheduler->getWorkDir($job);
        
        // Add databank path
        if (!$this->hasPersoDb())
            $dbPath = $dbPath;
        else {
            $formatDbWithProt = in_array($this->program, array('blastp', 'blastx')) ? 'prot' : 'nuc';

            $formatdbCommand = " makeblastdb -in $dbFilePath -dbtype $formatDbWithProt -out ".$workDir."uploadedDB -title uploadedDB; ";
            $dbPath = $workDir."uploadedDB";
        }
        
        $jobCommand .= "$formatdbCommand";
        
        if ($this->program == 'blastp' && ($this->blastpType == 'psiblast' || $this->blastpType == 'phiblast'))
            $blastCommand = "psiblast";
        else
            $blastCommand = $this->program;
        
        if ($this->program == 'blastn')
            $blastCommand .=  " -task ".$this->blastnType;
        else if ($this->program == 'blastp' && $this->blastpType != 'psiblast' && $this->blastpType != 'phiblast')
            $blastCommand .=  " -task ".$this->blastpType;
          
        if (!empty($this->fileSeq) || !empty($this->pastedSeq))
            $blastCommand .= " -query ".$workDir.'input.fasta';
        
        $blastCommand .= " -db ".$this->dbPath." -evalue ".$this->expect." -max_target_seqs ".$this->maxTargetSequences." -soft_masking ".($this->softMasking ? "true" : "false" );
        
        if ($this->lowerCase)
            $blastCommand .= " -lcase_masking";
        
        if ($this->program == 'blastn' && $this->blastnType == "megablast")
            $blastCommand .= " -word_size ".$this->wordSizesMegablast;
        else if ($this->program == 'blastn' && $this->blastnType == "dc-megablast")
            $blastCommand .= " -word_size ".$this->wordSizesDcMegablast;
        else if ($this->program == 'blastn')
            $blastCommand .= " -word_size ".$this->wordSizesBlastn;
        else
            $blastCommand .= " -word_size ".$this->wordSizesProt;
        
        if ($this->program == 'tblastx' || $this->program == 'blastx')
            $blastCommand .= " -query_gencode ".$this->queryCode;
        if ($this->program == 'tblastx' || $this->program == 'tblastn')
            $blastCommand .= " -db_gencode ".$this->dbCode;
        
        if ($this->program == 'blastn') {
            if ($this->blastnType == "megablast")
              $nucMatrix = explode(',', $this->matricesMegablast);
            else
              $nucMatrix = explode(',', $this->matricesNuc);
            $blastCommand .= " -reward ".$nucMatrix[0];
            $blastCommand .= " -penalty ".$nucMatrix[1];
        }
        else
            $blastCommand .= " -matrix ".$this->matricesProt;
        
        if ($this->program == 'blastn' && $this->blastnType == "megablast")
            $gaps = explode(',', $this->gapCostsMegablast);
        else if ($this->program == 'blastn')
            $gaps = explode(',', $this->gapCostsBlastn);
        else if ($this->program != 'tblastx')
            $gaps = explode(',', $this->gapCostsProt);
        
        if ($this->program != 'tblastx') {
            $blastCommand .= " -gapopen ".$gaps[0];
            $blastCommand .= " -gapextend ".$gaps[1];
        }
        
        if ($this->program == 'blastp' || $this->program == 'tblastn')
            $blastCommand .= " -comp_based_stats ".$this->compositionalAdjustments;
          
        if ($this->program == 'blastn')
            $blastCommand .= " -dust ".($this->lowComplex ? "yes" : "no");
        else
            $blastCommand .= " -seg ".($this->lowComplex ? "yes" : "no");
        
        // PSI/PHI-BLAST parameters
        if (($this->program == 'blastp') && (($this->blastpType == 'psiblast') || ($this->blastpType == 'phiblast'))) {
            $blastCommand .= " -inclusion_ethresh ".$this->psiThreshold;
            $blastCommand .= " -num_iterations ".$this->psiIterationNb;
            $blastCommand .= " -pseudocount ".$this->psiPseudoCount;
          
            if ($this->blastpType == 'psiblast') {
                $blastCommand .= " -out_pssm ".$workDir.$outputFilePrefix.".pssm";
                $blastCommand .= " -out_ascii_pssm ".$workDir.$outputFilePrefix.".pssm.ascii";
            }
          
            if ($this->blastpType == 'psiblast' && $this->psiPSSM) {
                $blastCommand .= " -in_pssm ".$workDir.'input.pssm';
            }
            
            if ($this->blastpType == 'phiblast' && $this->phiPattern) {
                $blastCommand .= " -phi_pattern ".$workDir.'input.pattern';
            }
        }
        
        $blastCommand .= " -num_threads 4";
        
        // Create an array containing the name of each result file
        $resultFiles = array();
        $resultViewers = array();
        if ($this->program == 'blastp' && $this->blastpType == 'psiblast') {
          $resultFiles = array('HTML blast output' => $outputFilePrefix.'.html',
                               'PSSM' => $outputFilePrefix.'.pssm',
                               'PSSM ASCII' => $outputFilePrefix.'.pssm.ascii',
                               'Executed command' => "blast_command.txt");
                                                          
          $blastCommand .= " -outfmt 0 -html -out ".$workDir.$outputFilePrefix.".html ;";
          $jobCommand .= $blastCommand;
        }
        else if ($this->program == 'blastp' && $this->blastpType == 'phiblast') {
          $resultFiles = array('HTML blast output' => $outputFilePrefix.'.html',
                               'Executed command' => "blast_command.txt");
                               
          $blastCommand .= " -outfmt 0 -html -out ".$workDir.$outputFilePrefix.".html ;";
          $jobCommand .= $blastCommand;
        }
        else {          
          $resultFiles = array('HTML blast output' => $outputFilePrefix.'.html',
                               'Text blast output' => $outputFilePrefix.'.txt',
                               'Comma-separated blast output' => $outputFilePrefix.'.csv',
                               'Tabular blast output' => $outputFilePrefix.'.tsv',
                               'XML blast output' => $outputFilePrefix.'.xml',
                               'ASN.1 archive blast output' => $outputFilePrefix.'.asn',
                               'Executed command' => "blast_command.txt");

          $blastCommand .= " -outfmt 11 -out ".$workDir.$outputFilePrefix.".asn ;"; // Format 11 = blast archive format
          $jobCommand .= $blastCommand;
          
          // Reformat the blast output in txt, xml, html ...
          $jobCommand .= "blast_formatter -archive ".$workDir.$outputFilePrefix.".asn -outfmt 5 -out ".$workDir.$outputFilePrefix.".xml;";
          $jobCommand .= "blast_formatter -archive ".$workDir.$outputFilePrefix.".asn -outfmt 0 -html -out ".$workDir.$outputFilePrefix.".html;";
          $jobCommand .= "blast_formatter -archive ".$workDir.$outputFilePrefix.".asn -outfmt 0 -out ".$workDir.$outputFilePrefix.".txt;";
          $jobCommand .= "blast_formatter -archive ".$workDir.$outputFilePrefix.".asn -outfmt 6 -out ".$workDir.$outputFilePrefix.".tsv;";
          $jobCommand .= "blast_formatter -archive ".$workDir.$outputFilePrefix.".asn -outfmt 10 -out ".$workDir.$outputFilePrefix.".csv;";
        }
        
        // Move uploaded files
        if ($this->hasPersoDb())
            $this->persoBankFile->move($workDir, 'uploadedDB.fasta');
        if (!empty($this->fileSeq))
            $this->fileSeq->move($workDir, 'input.fasta');
        if (!empty($this->pastedSeq)) {
            $seqFile = @fopen($workDir.'input.fasta','w');
            $fError = $seqFiles === false;
            if ($seqFile) {
                $fError = $fError || (false === @fwrite($seqFile,$this->pastedSeq));
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
    
        // Write command line in case the user wants to see it
        $fp = @fopen($workDir."blast_command.txt", 'w');
        $fError = $fp === false;
        if ($fp) {
            $fError = $fError || (false === @fwrite($fp, $blastCommand));
            $fError = $fError || (false === @fclose($fp));
        }

        if ($fError) {
            $error = error_get_last();
            throw new FileException(sprintf('Could not create file %s (%s)', $workDir.'blast_command.txt', strip_tags($error['message'])));
        }

        // Save files and viewers in db
        $job->addResultFilesArray($resultFiles);
        $job->addResultViewersArray($resultViewers);

        \BankManager::sendStats($job->getProgramName(), $dbPath);

        // Store generated command line
        $job->setCommand($jobCommand);
        
        return $job;
    }
    
}

