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

class SequenceUtils {

    const CHECK_WORD                = 0x01;
    const CHECK_FASTA               = 0x02;
    const CHECK_PROSITE             = 0x04;
    const CHECK_ADN                 = 0x08;
    const CHECK_PROTEIC             = 0x10;
    const CHECK_PROTEIC_OR_ADN      = 0x20;

    private $adnChars = array('a','t','c','g','n','u','r','y','A','T','C','G','N','U','R','Y',"\n","\t","\s",' ');
    private $strictProteinChars = array('N','D','E','F','H','I','K','L','M','P','Q','R','S','V','W','Y','d','e','f','h','i','k','l','m','n','p','q','r','s','v','w','y');
    private $proteinChars = array('A','C','D','E','F','G','H','I','K','L','M','N','P','Q','R','S','T','V','W','Y','B','Z','X','*',
                                'a','c','d','e','f','g','h','i','k','l','m','n','p','q','r','s','t','v','w','y','b','z','x',"\n","\t","\s",' ');
    private $prositeChars = array('-','[',']','(',')','{','}','x',',','>','<','1','2','3','4','5','6','7','8','9','0','A','C','D','E','F','G','H','I','K','L',
                                'M','N','P','Q','R','S','T','V','W','Y','a','c','d','e','f','g','h','i','k','l','m','n','p','q','r','s','t','v','w','y',"\n");
    private $wordChars = array('ÿ','Ð','Ï','à','±','þ');

    /**
     * Converts a text from dos (or mac before OSX) to unix carriage returns
     *
     * @param string The text to convert
     * @returns The text converted
     **/
    public function dos2Unix($string) {
        $order   = array("\r\n", "\r");
        $replace = "\n";
        // Processes \r\n's first so they aren't converted twice.
        return str_replace($order, $replace, $string);
    }

    /**
     * Ensure the text is a correct unix text with \n and a final \n
     *
     * @param string The text to convert
     * @returns The text converted
     **/
    public function formatSequence($string) {
        $res = $this->dos2Unix($string);
        $res .= "\n";
        return $res;
    }

    /**
     * Check that the given sequence file matches a format
     *
     * @param sequence The file path containing the sequence to check
     * @param rule The formats to check (CHECK_*, see the const at the beginning of this file)
     * @param doFormat Ensure the file contains only unix line returns. This will modify the original file!
     * @returns An error message if case of failure, an empty string otherwise.
     **/
    public function checkSequenceFromFile($seqPath, $rule = SequenceUtils::CHECK_WORD, $doFormat = true) {
        $seqFile = fopen( $seqPath, "r" );
        $data = "";
        if ($seqFile) {
            while (!feof($seqFile)) {
                $data .= fgets($seqFile);
            }
            fclose($seqFile);
        }
        else {
            return "Could not open the file for reading.";
        }

        $resCheck = $this->checkSequence($data, $rule);

        if ($doFormat && empty($resCheck)) {
            $data = $this->formatSequence($data);
            $seqFileOut = fopen( $seqPath, "w" );
            if ($seqFileOut) {
                $data = fwrite($seqFileOut, $data);
                fclose($seqFileOut);
            }
            else {
                return "Could not open the file for writing.";
            }
        }

        return $resCheck;
    }

    /**
     * Check that the given sequence matches a format
     *
     * @param sequence The sequence to check
     * @param rule The formats to check (CHECK_*, see the const at the beginning of this file)
     * @returns An error message if case of failure, an empty string otherwise.
     **/
    public function checkSequence($sequence, $rule = SequenceUtils::CHECK_WORD) {
        $seqLines =  preg_split('/[\r\n]+/', $sequence, -1, PREG_SPLIT_NO_EMPTY);
        
        if ($rule & SequenceUtils::CHECK_WORD) {
            foreach ($seqLines as $line) {
                if (preg_match('/^\{\\\\rtf/', $line)) {
                    // This is an RTF file
                    return "This is not a valid text file.";
                }
                if (!empty($line) && (strpos($line, '>') === false)) {
                    $lineChars = str_split($line);
                    foreach ($lineChars as $char) {
                        if (in_array($char, $this->wordChars)) {
                            // This is a word-like file
                            return "This is not a valid text file.";
                        }
                    }
                }
            }
        }
        
        // Ensure the file is proper unix file
        $sequence = $this->dos2Unix($sequence);
        $seqLines = explode("\n", $sequence);

        $foundDef = false; // Did we found a '>' in the sequence?
        // Check every line
        foreach ($seqLines as $line) {
            $posDef = strpos($line, '>');
            if (!empty($line) && ($posDef === FALSE)) {
                $lineChars = str_split($line);
                // Check every char from the current line
                foreach ($lineChars as $char) {
                    if (($rule & SequenceUtils::CHECK_PROSITE) && !in_array($char, $this->prositeChars)) {
                        return $char." is not allowed in prosite format.";
                    }
                    else if (($rule & SequenceUtils::CHECK_ADN) && !in_array($char, $this->adnChars)) {
                        return "'".$char."'"." is not allowed in nucleic format.";
                    }
                    else if (($rule & SequenceUtils::CHECK_PROTEIC) && !in_array($char, $this->proteinChars)) {
                        return "'".$char."'"." is not allowed in proteic sequence.";
                    }
                    else if (($rule & SequenceUtils::CHECK_PROTEIC_OR_ADN) && !in_array($char, $this->adnChars) && !in_array($char, $this->proteinChars)) {
                        return "Could not determine sequence type.";
                    }
                }
            }
            else if ($posDef !== false) {
                $foundDef = true;
            }
        }
        
        if (($rule & SequenceUtils::CHECK_FASTA) && !$foundDef) {
            return "Fasta definition line is missing (line begining by '>').";
        }

        return ""; // No errors, the sequence match the rules
    }

}

