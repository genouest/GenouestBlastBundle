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

namespace Genouest\Bundle\BlastBundle\Tests\Entity;

use Genouest\Bundle\BlastBundle\Tests\TestCase;
use Genouest\Bundle\BlastBundle\Entity\BlastRequest;
use Symfony\Component\HttpFoundation\File\File;

class BlastRequestTest extends TestCase
{
    public function testPersoDb()
    {
        $request = new BlastRequest($this->_container);
        
        // Public db
        $request->program = 'blastn';
        $request->blastnType = 'blastn';
        $request->bankTypeNuc = 'pubdb';
        $request->bankTypeProt = 'pubdb';
        
        $this->assertFalse($request->hasPersoDb(), '->hasPersoDb() with blastn and no db');
        $this->assertFalse($request->isDatabankOk(), '->isDatabankOk() with blastn and no db');
        
        $request->dbPath = '/db/some/nuc/db';
        $this->assertFalse($request->hasPersoDb(), '->hasPersoDb() with blastn and public db');
        $this->assertTrue($request->isDatabankOk(), '->isDatabankOk() with blastn and public db');
        
        $request->program = 'blastp';
        $request->blastnType = 'blastp';
        $this->assertFalse($request->hasPersoDb(), '->hasPersoDb() with blastp and public db');
        
        $request->blastnType = 'phiblast';
        $this->assertFalse($request->hasPersoDb(), '->hasPersoDb() with phiblast and public db');
        
        $request->blastnType = 'psiblast';
        $this->assertFalse($request->hasPersoDb(), '->hasPersoDb() with phiblast and public db');
        
        // Personal db
        $request->program = 'blastn';
        $request->blastnType = 'blastn';
        $request->bankTypeNuc = 'persodb';
        $request->bankTypeProt = 'persodb';
        $request->persoBankFile = '/db/some/nuc/db';
        $this->assertTrue($request->hasPersoDb(), '->hasPersoDb() with blastn and personal db');
        $this->assertTrue($request->isDatabankOk(), '->isDatabankOk() with blastn and personal db');
        
        $request->program = 'blastp';
        $request->blastnType = 'blastp';
        $this->assertTrue($request->hasPersoDb(), '->hasPersoDb() with blastp and personal db');
        
        $request->blastnType = 'phiblast';
        $this->assertTrue($request->hasPersoDb(), '->hasPersoDb() with phiblast and personal db');
        
        $request->blastnType = 'psiblast';
        $this->assertTrue($request->hasPersoDb(), '->hasPersoDb() with phiblast and personal db');
        
        // Mixing db type
        $request->program = 'blastn';
        $request->blastnType = 'blastn';
        $request->bankTypeNuc = 'persodb';
        $request->bankTypeProt = 'pubdb';
        $this->assertTrue($request->hasPersoDb(), '->hasPersoDb() with blastn and personal db (mixed)');
        
        $request->bankTypeNuc = 'pubdb';
        $request->bankTypeProt = 'persodb';
        $this->assertFalse($request->hasPersoDb(), '->hasPersoDb() with blastn and public db (mixed)');
        
        $request->program = 'blastp';
        $request->blastnType = 'blastp';
        $this->assertTrue($request->hasPersoDb(), '->hasPersoDb() with blastp and personal db (mixed)');
        
        $request->bankTypeNuc = 'persodb';
        $request->bankTypeProt = 'pubdb';
        $this->assertFalse($request->hasPersoDb(), '->hasPersoDb() with blastp and public db (mixed)');
    }
    
    public function testSequence()
    {
        $request = new BlastRequest($this->_container);
        
        $request->program = 'blastn';
        $request->blastnType = 'blastn';
        $request->bankTypeNuc = 'pubdb';
        $request->bankTypeProt = 'pubdb';
        
        $this->assertFalse($request->isSequencePresent(), '->isSequencePresent() with blastn and no seq');
        $this->assertTrue($request->isSequenceSingle(), '->isSequenceSingle() with blastn and no seq');
        
        $request->pastedSeq = 'foobar';
        
        $this->assertTrue($request->isSequencePresent(), '->isSequencePresent() with blastn and pasted seq');
        $this->assertTrue($request->isSequenceSingle(), '->isSequenceSingle() with blastn and pasted seq');
        
        $request->pastedSeq = '';
        $request->fileSeq = 'foo';
        
        $this->assertTrue($request->isSequencePresent(), '->isSequencePresent() with blastn and uploaded seq');
        $this->assertTrue($request->isSequenceSingle(), '->isSequenceSingle() with blastn and uploaded seq');
        
        $request->pastedSeq = 'foo';
        $request->fileSeq = 'foo';
        
        $this->assertFalse($request->isSequenceSingle(), '->isSequenceSingle() with blastn and pasted + uploaded seq');
    }
    
    public function testSequencePsi()
    {
        $request = new BlastRequest($this->_container);
        
        $request->program = 'blastp';
        $request->blastpType = 'psiblast';
        $request->bankTypeNuc = 'pubdb';
        $request->bankTypeProt = 'pubdb';
        
        $this->assertFalse($request->isSequencePresentForPsi(), '->isSequencePresentForPsi() with psiblast and no seq'.$request->isSequencePresentForPsi());
        $this->assertTrue($request->isSequenceSingleForPsi(), '->isSequenceSingleForPsi() with psiblast and no seq');
        
        $request->pastedSeq = 'foobar';
        
        $this->assertTrue($request->isSequencePresentForPsi(), '->isSequencePresentForPsi() with psiblast and pasted seq');
        $this->assertTrue($request->isSequenceSingleForPsi(), '->isSequenceSingleForPsi() with psiblast and pasted seq');
        
        $request->pastedSeq = '';
        $request->fileSeq = 'foo';
        
        $this->assertTrue($request->isSequencePresentForPsi(), '->isSequencePresentForPsi() with psiblast and uploaded seq');
        $this->assertTrue($request->isSequenceSingleForPsi(), '->isSequenceSingleForPsi() with psiblast and uploaded seq');
        
        $request->pastedSeq = '';
        $request->fileSeq = '';
        $request->psiPSSM = 'foo';
        
        $this->assertTrue($request->isSequencePresentForPsi(), '->isSequencePresentForPsi() with psiblast and pssm');
        $this->assertTrue($request->isSequenceSingleForPsi(), '->isSequenceSingleForPsi() with psiblast and pssm');
        
        $request->pastedSeq = 'foo';
        $request->fileSeq = 'foo';
        $request->psiPSSM = 'foo';
        
        $this->assertTrue($request->isSequencePresentForPsi(), '->isSequencePresentForPsi() with psiblast and pasted + uploaded + pssm');
        $this->assertFalse($request->isSequenceSingleForPsi(), '->isSequenceSingleForPsi() with psiblast and pasted + uploaded + pssm seq');
        
        $request->pastedSeq = '';
        
        $this->assertTrue($request->isSequencePresentForPsi(), '->isSequencePresentForPsi() with psiblast and uploaded + pssm');
        $this->assertFalse($request->isSequenceSingleForPsi(), '->isSequenceSingleForPsi() with psiblast and uploaded + pssm seq');
    }
    
    public function testFilesAndCommand()
    {
        $request = new BlastRequest($this->_container);
        
        // Public db, no seq
        $request->program = 'blastn';
        $request->blastnType = 'dc-megablast';
        $request->bankTypeNuc = 'pubdb';
        $request->dbPath = '/db/some/nuc/db';
        
        $this->assertRegExp('/blastn -task dc-megablast -db \/db\/some\/nuc\/db.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() ok for blastn against public db');
        
        // Personal db, no seq
        $request->bankTypeNuc = 'persodb';
        $request->dbPath = '';
        $bankFile = $this->getMock('File', array('move'));
        $request->persoBankFile = $bankFile;
        
        $bankFile->expects($this->once())
            ->method('move')
            ->with($this->anything(), $this->stringContains('uploadedDB'));
        
        $job = $request->getJob($this->get('scheduler.scheduler'));
        $workDir = $this->get('scheduler.scheduler')->getWorkDir($job);
        
        $this->assertRegExp('/blastn -task dc-megablast -db '.str_replace('/', '\/', $workDir).'uploadedDB.*/', $job->getCommand(), '->getCommand() ok for blastn against personal db');
        
        // Public db, uploaded seq
        $request->bankTypeNuc = 'pubdb';
        $request->dbPath = '/db/some/nuc/db';
        $seqFile = $this->getMock('File', array('move'));
        $request->fileSeq = $seqFile;
        
        $seqFile->expects($this->once())
            ->method('move')
            ->with($this->anything(), $this->stringContains('input.fasta'));
        
        $job = $request->getJob($this->get('scheduler.scheduler'));
        $workDir = $this->get('scheduler.scheduler')->getWorkDir($job);
        
        $this->assertRegExp('/blastn -task dc-megablast -query '.str_replace('/', '\/', $workDir).'input.fasta -db \/db\/some\/nuc\/db.*/', $job->getCommand(), '->getCommand() ok for blastn against public db');
        
        // Public db, pasted seq
        $request->bankTypeNuc = 'pubdb';
        $request->dbPath = '/db/some/nuc/db';
        $request->pastedSeq = 'foo';
        $request->fileSeq = '';
        
        $job = $request->getJob($this->get('scheduler.scheduler'));
        $workDir = $this->get('scheduler.scheduler')->getWorkDir($job);
        
        $this->assertRegExp('/blastn -task dc-megablast -query '.str_replace('/', '\/', $workDir).'input.fasta -db \/db\/some\/nuc\/db.*/', $job->getCommand(), '->getCommand() ok for blastn against public db');
        
        // Public db, PSSM (psiblast)
        $request->program = 'blastp';
        $request->blastpType = 'psiblast';
        $request->bankTypeProt = 'pubdb';
        $request->dbPath = '/db/some/nuc/db';
        $pssmFile = $this->getMock('File', array('move'));
        $request->psiPSSM = $pssmFile;
        
        $pssmFile->expects($this->once())
            ->method('move')
            ->with($this->anything(), $this->stringContains('input.pssm'));
        
        $job = $request->getJob($this->get('scheduler.scheduler'));
        $workDir = $this->get('scheduler.scheduler')->getWorkDir($job);
        
        $this->assertRegExp('/psiblast -query '.str_replace('/', '\/', $workDir).'input.fasta -db \/db\/some\/nuc\/db.*/', $job->getCommand(), '->getCommand() ok for blastn against public db');
        $this->assertRegExp('/.*-in_pssm '.str_replace('/', '\/', $workDir).'input.pssm.*/', $job->getCommand(), '->getCommand() ok for psiblast against public db with pssm');
        $this->assertNotRegExp('/.*-phi_pattern '.str_replace('/', '\/', $workDir).'input.pattern.*/', $job->getCommand(), '->getCommand() ok for psiblast against public db without pattern');
        
        // Public db, pattern (phiblast)
        $request->program = 'blastp';
        $request->blastpType = 'phiblast';
        $request->bankTypeProt = 'pubdb';
        $request->dbPath = '/db/some/nuc/db';
        $pssmFile = '';
        $patternFile = $this->getMock('File', array('move'));
        $request->phiPattern = $patternFile;
        
        $patternFile->expects($this->once())
            ->method('move')
            ->with($this->anything(), $this->stringContains('input.pattern'));
        
        $job = $request->getJob($this->get('scheduler.scheduler'));
        $workDir = $this->get('scheduler.scheduler')->getWorkDir($job);

        $this->assertRegExp('/psiblast -query '.str_replace('/', '\/', $workDir).'input.fasta -db \/db\/some\/nuc\/db.*/', $job->getCommand(), '->getCommand() ok for blastn against public db');
        $this->assertNotRegExp('/.*-in_pssm '.str_replace('/', '\/', $workDir).'input.pssm.*/', $job->getCommand(), '->getCommand() ok for phiblast against public db with pattern');
        $this->assertRegExp('/.*-phi_pattern '.str_replace('/', '\/', $workDir).'input.pattern.*/', $job->getCommand(), '->getCommand() ok for phiblast against public db without pssm');
    }
    
    public function testCommandOptions()
    {
        $request = new BlastRequest($this->_container);
        
        // Public db, no seq
        $request->maxTargetSequences = 50;
        $request->expect = 10;
        $request->wordSizesProt = 3;
        $request->wordSizesBlastn = 4;
        $request->wordSizesMegablast = 5;
        $request->wordSizesDcMegablast = 6;
        $request->matricesNuc = '9,9';
        $request->matricesMegablast = '10,10';
        $request->matricesProt = 'foo45';
        $request->queryCode = 7;
        $request->dbCode = 8;
        $request->gapCostsBlastn = '11,11';
        $request->gapCostsMegablast = '12,12';
        $request->gapCostsProt = '13,13';
        $request->compositionalAdjustments = 14;
        $request->psiThreshold = '15,16';
        $request->psiIterationNb = 17;
        $request->psiPseudoCount = 18;
        
        $request->lowComplex = true;
        $request->softMasking = true;
        $request->lowerCase = true;
        
        // Blastn params
        $request->program = 'blastn';
        $request->blastnType = 'blastn';
        $request->bankTypeNuc = 'pubdb';
        $request->dbPath = '/db/some/nuc/db';
        
        $this->assertRegExp('/.*-max_target_seqs '.$request->maxTargetSequences.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-evalue '.$request->expect.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesProt.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-word_size '.$request->wordSizesBlastn.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesMegablast.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesDcMegablast.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-reward 9 -penalty 9 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-reward 10 -penalty 10 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-matrix '.$request->matricesProt.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-query_gencode '.$request->queryCode.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-db_gencode '.$request->dbCode.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-gapopen 11 -gapextend 11 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-gapopen 12 -gapextend 12 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-gapopen 13 -gapextend 13 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-comp_based_stats '.$request->compositionalAdjustments.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-inclusion_ethresh '.$request->psiThreshold.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-num_iterations '.$request->psiIterationNb.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-pseudocount '.$request->psiPseudoCount.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        
        $this->assertRegExp('/.*-dust yes .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-seg yes .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-soft_masking true .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-lcase_masking .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        
        $request->lowComplex = false;
        $request->softMasking = false;
        $request->lowerCase = false;
        
        $this->assertRegExp('/.*-dust no .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-seg no .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-soft_masking false .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-lcase_masking .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        
        
        // Megablast params
        $request->blastnType = 'megablast';
        
        $this->assertRegExp('/.*-max_target_seqs '.$request->maxTargetSequences.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-evalue '.$request->expect.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesProt.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesBlastn.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-word_size '.$request->wordSizesMegablast.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesDcMegablast.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-reward 9 -penalty 9 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-reward 10 -penalty 10 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-matrix '.$request->matricesProt.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-query_gencode '.$request->queryCode.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-db_gencode '.$request->dbCode.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-gapopen 11 -gapextend 11 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-gapopen 12 -gapextend 12 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-gapopen 13 -gapextend 13 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-comp_based_stats '.$request->compositionalAdjustments.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-inclusion_ethresh '.$request->psiThreshold.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-num_iterations '.$request->psiIterationNb.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-pseudocount '.$request->psiPseudoCount.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-dust no .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-seg no .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-soft_masking false .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-lcase_masking .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        
        
        // dc-megablast params
        $request->blastnType = 'dc-megablast';
        
        $this->assertRegExp('/.*-max_target_seqs '.$request->maxTargetSequences.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-evalue '.$request->expect.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesProt.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesBlastn.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesMegablast.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-word_size '.$request->wordSizesDcMegablast.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-reward 9 -penalty 9 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-reward 10 -penalty 10 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-matrix '.$request->matricesProt.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-query_gencode '.$request->queryCode.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-db_gencode '.$request->dbCode.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-gapopen 11 -gapextend 11 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-gapopen 12 -gapextend 12 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-gapopen 13 -gapextend 13 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-comp_based_stats '.$request->compositionalAdjustments.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-inclusion_ethresh '.$request->psiThreshold.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-num_iterations '.$request->psiIterationNb.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-pseudocount '.$request->psiPseudoCount.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-dust no .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-seg no .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-soft_masking false .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-lcase_masking .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        
        
        // blastp params
        $request->program = 'blastp';
        $request->blastpType = 'blastp';
        
        $this->assertRegExp('/.*-max_target_seqs '.$request->maxTargetSequences.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-evalue '.$request->expect.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-word_size '.$request->wordSizesProt.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesBlastn.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesMegablast.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesDcMegablast.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-reward 9 -penalty 9 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-reward 10 -penalty 10 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-matrix '.$request->matricesProt.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-query_gencode '.$request->queryCode.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-db_gencode '.$request->dbCode.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-gapopen 11 -gapextend 11 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-gapopen 12 -gapextend 12 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-gapopen 13 -gapextend 13 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-comp_based_stats '.$request->compositionalAdjustments.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-inclusion_ethresh '.$request->psiThreshold.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-num_iterations '.$request->psiIterationNb.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-pseudocount '.$request->psiPseudoCount.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-dust no .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-seg no .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-soft_masking false .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-lcase_masking .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        
        
        // psiblast params
        $request->program = 'blastp';
        $request->blastpType = 'psiblast';
        
        $this->assertRegExp('/.*-max_target_seqs '.$request->maxTargetSequences.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-evalue '.$request->expect.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-word_size '.$request->wordSizesProt.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesBlastn.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesMegablast.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesDcMegablast.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-reward 9 -penalty 9 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-reward 10 -penalty 10 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-matrix '.$request->matricesProt.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-query_gencode '.$request->queryCode.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-db_gencode '.$request->dbCode.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-gapopen 11 -gapextend 11 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-gapopen 12 -gapextend 12 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-gapopen 13 -gapextend 13 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-comp_based_stats '.$request->compositionalAdjustments.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-inclusion_ethresh '.$request->psiThreshold.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-num_iterations '.$request->psiIterationNb.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-pseudocount '.$request->psiPseudoCount.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-dust no .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-seg no .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-soft_masking false .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-lcase_masking .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        
        
        // phiblast params
        $request->program = 'blastp';
        $request->blastpType = 'phiblast';
        
        $this->assertRegExp('/.*-max_target_seqs '.$request->maxTargetSequences.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-evalue '.$request->expect.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-word_size '.$request->wordSizesProt.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesBlastn.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesMegablast.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-word_size '.$request->wordSizesDcMegablast.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-reward 9 -penalty 9 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-reward 10 -penalty 10 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-matrix '.$request->matricesProt.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-query_gencode '.$request->queryCode.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-db_gencode '.$request->dbCode.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-gapopen 11 -gapextend 11 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-gapopen 12 -gapextend 12 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-gapopen 13 -gapextend 13 .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-comp_based_stats '.$request->compositionalAdjustments.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-inclusion_ethresh '.$request->psiThreshold.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-num_iterations '.$request->psiIterationNb.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-pseudocount '.$request->psiPseudoCount.'.*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-dust no .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-seg no .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertRegExp('/.*-soft_masking false .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
        $this->assertNotRegExp('/.*-lcase_masking .*/', $request->getJob($this->get('scheduler.scheduler'))->getCommand(), '->getCommand() options ok for blastn against public db');
    }
}
