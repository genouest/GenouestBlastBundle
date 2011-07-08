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
}

function refreshControls() {
    program = jQuery(blastFormPrefix+'program').val();
    blastnType = jQuery(blastFormPrefix+'blastnType').val();
    blastpType = jQuery(blastFormPrefix+'blastpType').val();
    
    if ((program == "blastn") || (program == "tblastn"))
      bankType = jQuery(blastFormPrefix+'bankTypeNuc').val();
    else
      bankType = jQuery(blastFormPrefix+'bankTypeProt').val();
    
    if ((program == "blastn") || (program == "tblastn"))
      jQuery(blastFormPrefix+'bankTypeNuc').parent().parent().show();
    else
      jQuery(blastFormPrefix+'bankTypeNuc').parent().parent().hide();
    
    if ((program == "blastn") || (program == "tblastn"))
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
      
      
    if ((program == "blastn") && (blastnType == "megablast"))
      jQuery(blastFormPrefix+'gapCostsMegablast').parent().parent().show();
    else
      jQuery(blastFormPrefix+'gapCostsMegablast').parent().parent().hide();
      
    if ((program == "blastn") && (blastnType != "megablast"))
      jQuery(blastFormPrefix+'gapCostsBlastn').parent().parent().show();
    else
      jQuery(blastFormPrefix+'gapCostsBlastn').parent().parent().hide();
      
    if ((program != "blastn") && (program != 'tblastx'))
      jQuery(blastFormPrefix+'gapCostsProt').parent().parent().show();
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
    
    if (((program == "blastp") && (blastpType != "phiblast")) || (program == "tblastn"))
      jQuery(blastFormPrefix+'compositionalAdjustments').parent().parent().show();
    else
      jQuery(blastFormPrefix+'compositionalAdjustments').parent().parent().hide();
      
    if ((program == "blastp") && ((blastpType == "phiblast") || (blastpType == "psiblast")))
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
      
      
    if ((program == "blastp") && (blastpType == "phiblast"))
      jQuery('#phiwarn').show();
    else
      jQuery('#phiwarn').hide();
      
    
    
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


