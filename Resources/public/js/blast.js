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

/**
 * Prepare initial state of the form and register event handlers and different select boxes.
 * Called when the page is fully loaded.
 * 'updateDbList' is defined in the html body as it is dynamic
 */
function blastLoaded() {
    refreshControls();

    updateDbList();

    jQuery(blastFormPrefix+'bankTypeNuc').change(updateDbList);
    jQuery(blastFormPrefix+'bankTypeProt').change(updateDbList);
    jQuery(blastFormPrefix+'program').change(updateDbList);
    jQuery(blastFormPrefix+'blastnType').change(refreshControls);
    jQuery(blastFormPrefix+'blastpType').change(refreshControls);
    
    jQuery(blastFormPrefix+'matricesMegablast').change(refreshControls);
    jQuery(blastFormPrefix+'matricesNuc').change(refreshControls);
    jQuery(blastFormPrefix+'matricesProt').change(refreshControls);
}

function refreshControls() {
    program = jQuery(blastFormPrefix+'program').val();
    blastnType = jQuery(blastFormPrefix+'blastnType').val();
    blastpType = jQuery(blastFormPrefix+'blastpType').val();
    
    if ((program != "blastp") && (program != "blastx"))
      bankType = jQuery(blastFormPrefix+'bankTypeNuc').val();
    else
      bankType = jQuery(blastFormPrefix+'bankTypeProt').val();
    
    if ((program != "blastp") && (program != "blastx"))
      jQuery(blastFormPrefix+'bankTypeNuc').parent().parent().show();
    else
      jQuery(blastFormPrefix+'bankTypeNuc').parent().parent().hide();
    
    if ((program != "blastp") && (program != "blastx"))
      jQuery(blastFormPrefix+'bankTypeProt').parent().parent().hide();
    else
      jQuery(blastFormPrefix+'bankTypeProt').parent().parent().show();
      
    
    if (program == "blastn")
      jQuery(blastFormPrefix+'blastnType').parent().parent().show();
    else
      jQuery(blastFormPrefix+'blastnType').parent().parent().hide();
    
    if (program == "blastp")
      jQuery(blastFormPrefix+'blastpType').parent().parent().show();
    else
      jQuery(blastFormPrefix+'blastpType').parent().parent().hide();
    
    
    if ((program == "blastn") && (blastnType == "dc-megablast"))
      jQuery('.discontiguousFormPart').show();
    else
      jQuery('.discontiguousFormPart').hide();
    
    if ((program == "blastn") && (blastnType == "megablast"))
      jQuery(blastFormPrefix+'wordSizesMegablast').parent().parent().show();
    else
      jQuery(blastFormPrefix+'wordSizesMegablast').parent().parent().hide();
      
    if ((program == "blastn") && (blastnType == "dc-megablast"))
      jQuery(blastFormPrefix+'wordSizesDcMegablast').parent().parent().show();
    else
      jQuery(blastFormPrefix+'wordSizesDcMegablast').parent().parent().hide();
      
    if ((program == "blastn") && (blastnType == "blastn"))
      jQuery(blastFormPrefix+'wordSizesBlastn').parent().parent().show();
    else
      jQuery(blastFormPrefix+'wordSizesBlastn').parent().parent().hide();
      
    if (program != "blastn")
      jQuery(blastFormPrefix+'wordSizesProt').parent().parent().show();
    else
      jQuery(blastFormPrefix+'wordSizesProt').parent().parent().hide();
      
      
    if ((program == "blastn") && (blastnType == "megablast"))
      jQuery(blastFormPrefix+'matricesMegablast').parent().parent().show();
    else
      jQuery(blastFormPrefix+'matricesMegablast').parent().parent().hide();
      
    if ((program == "blastn") && (blastnType != "megablast"))
      jQuery(blastFormPrefix+'matricesNuc').parent().parent().show();
    else
      jQuery(blastFormPrefix+'matricesNuc').parent().parent().hide();
      
    if (program != "blastn")
      jQuery(blastFormPrefix+'matricesProt').parent().parent().show();
    else
      jQuery(blastFormPrefix+'matricesProt').parent().parent().hide();
      
    // Set the allowed gap costs
    if ((program == "blastn") && (blastnType == "megablast"))
        gapCosts = getGapCosts( jQuery(blastFormPrefix+'matricesMegablast').val());
    else if ((program == "blastn") && (blastnType != "megablast"))
        gapCosts = getGapCosts( jQuery(blastFormPrefix+'matricesNuc').val());
    else
        gapCosts = getGapCosts( jQuery(blastFormPrefix+'matricesProt').val());
    
    if ((program == "blastn")) {
      jQuery(blastFormPrefix+'gapCostsBlastn').parent().parent().show();
      setGapCosts(blastFormPrefix+'gapCostsBlastn', gapCosts);
    }
    else
      jQuery(blastFormPrefix+'gapCostsBlastn').parent().parent().hide();
      
    if ((program != "blastn") && (program != 'tblastx')) {
      jQuery(blastFormPrefix+'gapCostsProt').parent().parent().show();
      setGapCosts(blastFormPrefix+'gapCostsProt', gapCosts);
    }
    else
      jQuery(blastFormPrefix+'gapCostsProt').parent().parent().hide();
    
    
    if (program == 'tblastx') {
      jQuery(blastFormPrefix+'queryCode').parent().parent().show();
      jQuery(blastFormPrefix+'dbCode').parent().parent().show();
    }
    else if (program == 'blastx') {
      jQuery(blastFormPrefix+'queryCode').parent().parent().show();
      jQuery(blastFormPrefix+'dbCode').parent().parent().hide();
    }
    else if (program == 'tblastn') {
      jQuery(blastFormPrefix+'queryCode').parent().parent().hide();
      jQuery(blastFormPrefix+'dbCode').parent().parent().show();
    }
    else {
      jQuery(blastFormPrefix+'queryCode').parent().parent().hide();
      jQuery(blastFormPrefix+'dbCode').parent().parent().hide();
    }
    
    if ((program == "blastp") && (blastpType != "blastp")) {
      if (jQuery(blastFormPrefix+'maxTargetSequences').val() == 100)
        jQuery(blastFormPrefix+'maxTargetSequences').val(500);
    }
    else {
      if (jQuery(blastFormPrefix+'maxTargetSequences').val() == 500)
        jQuery(blastFormPrefix+'maxTargetSequences').val(100);
    }
    
    if (((program == "blastp") && (blastpType != "phiblast") && (blastpType != "deltablast")) || (program == "tblastn")) {
      jQuery(blastFormPrefix+'compositionalAdjustments').parent().parent().show();
      jQuery(blastFormPrefix+'compositionalAdjustmentsDelta').parent().parent().hide();
    }
    else if ((program == "blastp") && (blastpType == "deltablast")) {
      jQuery(blastFormPrefix+'compositionalAdjustments').parent().parent().hide();
      jQuery(blastFormPrefix+'compositionalAdjustmentsDelta').parent().parent().show();
    }
    else {
      jQuery(blastFormPrefix+'compositionalAdjustments').parent().parent().hide();
      jQuery(blastFormPrefix+'compositionalAdjustmentsDelta').parent().parent().hide();
    }
      
    if ((program == "blastp") && ((blastpType == "phiblast") || (blastpType == "psiblast") || (blastpType == "deltablast")))
      jQuery('.psiFormPart').show();
    else
      jQuery('.psiFormPart').hide();
      
    if ((program == "blastp") && (blastpType == "psiblast"))
      jQuery(blastFormPrefix+'psiPSSM').parent().parent().show();
    else
      jQuery(blastFormPrefix+'psiPSSM').parent().parent().hide();
      
    if ((program == "blastp") && (blastpType == "phiblast"))
      jQuery(blastFormPrefix+'phiPattern').parent().parent().show();
    else
      jQuery(blastFormPrefix+'phiPattern').parent().parent().hide();
      
    if ((program == "blastp") && (blastpType == "deltablast")) {
      jQuery(blastFormPrefix+'deltaThreshold').parent().parent().show();
    }
    else {
      jQuery(blastFormPrefix+'deltaThreshold').parent().parent().hide();
    }
      
    
    
    if (bankType == 'persodb') {
      jQuery(blastFormPrefix+'persoBankFile').parent().parent().show();
      jQuery(blastFormPrefix+'dbPath').parent().parent().hide();
    }
    else {
      jQuery(blastFormPrefix+'persoBankFile').parent().parent().hide();
      jQuery(blastFormPrefix+'dbPath').parent().parent().show();
    }
    
    jQuery(blastFormPrefix+'lowComplex').attr('checked', !((program == "blastp") || ((program == "blastn") && (blastnType == "megablast"))));
    jQuery(blastFormPrefix+'softMasking').attr('checked', !(program != "blastn"));
}

function getGapCosts(matrix) {
    // Proteic matrices
    if (matrix == 'PAM30')
        return { 'values': {
                    '7,2' : 'Creation: 7 Extension: 2',
                    '6,2' :  'Creation: 6 Extension: 2',
                    '5,2' :  'Creation: 5 Extension: 2',
                    '10,1' :  'Creation: 10 Extension: 1',
                    '9,1' :  'Creation: 9 Extension: 1',
                    '8,1' :  'Creation: 8 Extension: 1',
                    },
                 'defaultVal' : '9,1'
               }
    if (matrix == 'PAM70')
        return { 'values': {
                    '8,2' : 'Creation: 8 Extension: 2',
                    '7,2' :  'Creation: 7 Extension: 2',
                    '6,2' :  'Creation: 6 Extension: 2',
                    '11,1' :  'Creation: 11 Extension: 1',
                    '10,1' :  'Creation: 10 Extension: 1',
                    '9,1' :  'Creation: 9 Extension: 1',
                    },
                 'defaultVal' : '10,1'
               }
    if (matrix == 'BLOSUM80')
        return { 'values': {
                    '8,2' : 'Creation: 8 Extension: 2',
                    '7,2' :  'Creation: 7 Extension: 2',
                    '6,2' :  'Creation: 6 Extension: 2',
                    '11,1' :  'Creation: 11 Extension: 1',
                    '10,1' :  'Creation: 10 Extension: 1',
                    '9,1' :  'Creation: 9 Extension: 1',
                    },
                 'defaultVal' : '10,1'
               }
    if (matrix == 'BLOSUM62')
        return { 'values': {
                    '9,2' : 'Creation: 9 Extension: 2',
                    '8,2' :  'Creation: 8 Extension: 2',
                    '7,2' :  'Creation: 7 Extension: 2',
                    '12,1' :  'Creation: 12 Extension: 1',
                    '11,1' :  'Creation: 11 Extension: 1',
                    '10,1' :  'Creation: 10 Extension: 1',
                    },
                 'defaultVal' : '11,1'
               }
    if (matrix == 'BLOSUM45')
        return { 'values': {
                    '13,3' : 'Creation: 13 Extension: 3',
                    '12,3' :  'Creation: 12 Extension: 3',
                    '11,3' :  'Creation: 11 Extension: 3',
                    '10,3' :  'Creation: 10 Extension: 3',
                    '15,2' :  'Creation: 15 Extension: 2',
                    '14,2' :  'Creation: 14 Extension: 2',
                    '13,2' :  'Creation: 13 Extension: 2',
                    '12,2' :  'Creation: 12 Extension: 2',
                    '19,1' :  'Creation: 19 Extension: 1',
                    '18,1' :  'Creation: 18 Extension: 1',
                    '17,1' :  'Creation: 17 Extension: 1',
                    '16,1' :  'Creation: 16 Extension: 1',
                    },
                 'defaultVal' : '15,2'
               }
    // Nucleic matrices
    if (matrix == '1,-2')
        return { 'values': {
                    '5,2' : 'Creation: 5 Extension: 2',
                    '2,2' : 'Creation: 2 Extension: 2',
                    '1,2' : 'Creation: 1 Extension: 2',
                    '0,2' : 'Creation: 0 Extension: 2',
                    '3,1' : 'Creation: 3 Extension: 1',
                    '2,1' : 'Creation: 2 Extension: 1',
                    '1,1' : 'Creation: 1 Extension: 1',
                    },
                 'defaultVal' : '5,2'
               }
    if (matrix == '1,-3')
        return { 'values': {
                    '5,2' : 'Creation: 5 Extension: 2',
                    '2,2' : 'Creation: 2 Extension: 2',
                    '1,2' : 'Creation: 1 Extension: 2',
                    '0,2' : 'Creation: 0 Extension: 2',
                    '2,1' : 'Creation: 2 Extension: 1',
                    '1,1' : 'Creation: 1 Extension: 1',
                    },
                 'defaultVal' : '5,2'
               }
    if (matrix == '1,-4')
        return { 'values': {
                    '5,2' : 'Creation: 5 Extension: 2',
                    '1,2' : 'Creation: 1 Extension: 2',
                    '0,2' : 'Creation: 0 Extension: 2',
                    '2,1' : 'Creation: 2 Extension: 1',
                    '1,1' : 'Creation: 1 Extension: 1',
                    },
                 'defaultVal' : '5,2'
               }
    if (matrix == '2,-3')
        return { 'values': {
                    '4,4' : 'Creation: 4 Extension: 4',
                    '2,4' : 'Creation: 2 Extension: 4',
                    '0,4' : 'Creation: 0 Extension: 4',
                    '3,3' : 'Creation: 3 Extension: 3',
                    '6,2' : 'Creation: 6 Extension: 2',
                    '5,2' : 'Creation: 5 Extension: 2',
                    '4,2' : 'Creation: 4 Extension: 2',
                    '2,2' : 'Creation: 2 Extension: 2',
                    },
                 'defaultVal' : '4,4'
               }
    if (matrix == '4,-5')
        return { 'values': {
                    '12,8' : 'Creation: 12 Extension: 8',
                    '6,5' : 'Creation: 6 Extension: 5',
                    '5,5' : 'Creation: 5 Extension: 5',
                    '4,5' : 'Creation: 4 Extension: 5',
                    '3,5' : 'Creation: 3 Extension: 5',
                    },
                 'defaultVal' : '12,8'
               }
    if (matrix == '1,-1')
        return { 'values': {
                    '5,2' : 'Creation: 5 Extension: 2',
                    '3,2' : 'Creation: 3 Extension: 2',
                    '2,2' : 'Creation: 2 Extension: 2',
                    '1,2' : 'Creation: 1 Extension: 2',
                    '0,2' : 'Creation: 0 Extension: 2',
                    '4,1' : 'Creation: 4 Extension: 1',
                    '3,1' : 'Creation: 3 Extension: 1',
                    '2,1' : 'Creation: 2 Extension: 1',
                    },
                 'defaultVal' : '5,2'
               }
}

function setGapCosts(field, costs) {

    if (field && costs && costs.values) {
        newHtml = '';
        
        for (value in costs.values) {
            if (!costs.values.hasOwnProperty(value)) {
                //The current property is not a direct property of costs.values
                continue;
            }
            
            newHtml += '<option value="'+value+'"';
            if (value == costs.defaultVal)
                newHtml += ' selected="selected"'
            newHtml += '>'+costs.values[value]+'</option>';
        }
        jQuery(field).html(newHtml);
    }
}
