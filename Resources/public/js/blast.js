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

    jQuery('#blast_bankTypeNuc').change(updateDbList);
    jQuery('#blast_bankTypeProt').change(updateDbList);
    jQuery('#blast_program').change(updateDbList);
    jQuery('#blast_blastnType').change(refreshControls);
    jQuery('#blast_blastpType').change(refreshControls);
}

function refreshControls() {
    program = jQuery('#blast_program').val();
    blastnType = jQuery('#blast_blastnType').val();
    blastpType = jQuery('#blast_blastpType').val();
    
    if ((program == "blastn") || (program == "tblastn"))
      bankType = jQuery('#blast_bankTypeNuc').val();
    else
      bankType = jQuery('#blast_bankTypeProt').val();
    
    if ((program == "blastn") || (program == "tblastn"))
      jQuery('#blast_bankTypeNuc').parent().parent().show();
    else
      jQuery('#blast_bankTypeNuc').parent().parent().hide();
    
    if ((program == "blastn") || (program == "tblastn"))
      jQuery('#blast_bankTypeProt').parent().parent().hide();
    else
      jQuery('#blast_bankTypeProt').parent().parent().show();
      
    
    if (program == "blastn")
      jQuery('#blast_blastnType').parent().parent().show();
    else
      jQuery('#blast_blastnType').parent().parent().hide();
    
    if (program == "blastp")
      jQuery('#blast_blastpType').parent().parent().show();
    else
      jQuery('#blast_blastpType').parent().parent().hide();
    
    
    if ((program == "blastn") && (blastnType == "megablast"))
      jQuery('#blast_wordSizesMegablast').parent().parent().show();
    else
      jQuery('#blast_wordSizesMegablast').parent().parent().hide();
      
    if ((program == "blastn") && (blastnType == "dc-megablast"))
      jQuery('#blast_wordSizesDcMegablast').parent().parent().show();
    else
      jQuery('#blast_wordSizesDcMegablast').parent().parent().hide();
      
    if ((program == "blastn") && (blastnType == "blastn"))
      jQuery('#blast_wordSizesBlastn').parent().parent().show();
    else
      jQuery('#blast_wordSizesBlastn').parent().parent().hide();
      
    if (program != "blastn")
      jQuery('#blast_wordSizesProt').parent().parent().show();
    else
      jQuery('#blast_wordSizesProt').parent().parent().hide();
      
      
    if ((program == "blastn") && (blastnType == "megablast"))
      jQuery('#blast_matricesMegablast').parent().parent().show();
    else
      jQuery('#blast_matricesMegablast').parent().parent().hide();
      
    if ((program == "blastn") && (blastnType != "megablast"))
      jQuery('#blast_matricesNuc').parent().parent().show();
    else
      jQuery('#blast_matricesNuc').parent().parent().hide();
      
    if (program != "blastn")
      jQuery('#blast_matricesProt').parent().parent().show();
    else
      jQuery('#blast_matricesProt').parent().parent().hide();
      
      
    if ((program == "blastn") && (blastnType == "megablast"))
      jQuery('#blast_gapCostsMegablast').parent().parent().show();
    else
      jQuery('#blast_gapCostsMegablast').parent().parent().hide();
      
    if ((program == "blastn") && (blastnType != "megablast"))
      jQuery('#blast_gapCostsBlastn').parent().parent().show();
    else
      jQuery('#blast_gapCostsBlastn').parent().parent().hide();
      
    if ((program != "blastn") && (program != 'tblastx'))
      jQuery('#blast_gapCostsProt').parent().parent().show();
    else
      jQuery('#blast_gapCostsProt').parent().parent().hide();
    
    
    if (program == 'tblastx') {
      jQuery('#blast_queryCode').parent().parent().show();
      jQuery('#blast_dbCode').parent().parent().show();
    }
    else if (program == 'blastx') {
      jQuery('#blast_queryCode').parent().parent().show();
      jQuery('#blast_dbCode').parent().parent().hide();
    }
    else if (program == 'tblastn') {
      jQuery('#blast_queryCode').parent().parent().hide();
      jQuery('#blast_dbCode').parent().parent().show();
    }
    else {
      jQuery('#blast_queryCode').parent().parent().hide();
      jQuery('#blast_dbCode').parent().parent().hide();
    }
    
    if ((program == "blastp") && (blastpType != "blastp")) {
      if (jQuery('#blast_maxTargetSequences').val() == 100)
        jQuery('#blast_maxTargetSequences').val(500);
    }
    else {
      if (jQuery('#blast_maxTargetSequences').val() == 500)
        jQuery('#blast_maxTargetSequences').val(100);
    }
    
    if (((program == "blastp") && (blastpType != "phiblast")) || (program == "tblastn"))
      jQuery('#blast_compositionalAdjustments').parent().parent().show();
    else
      jQuery('#blast_compositionalAdjustments').parent().parent().hide();
      
    if ((program == "blastp") && ((blastpType == "phiblast") || (blastpType == "psiblast")))
      jQuery('.psiFormPart').show();
    else
      jQuery('.psiFormPart').hide();
      
    if ((program == "blastp") && (blastpType == "psiblast"))
      jQuery('#blast_psiPSSM').parent().parent().show();
    else
      jQuery('#blast_psiPSSM').parent().parent().hide();
      
    if ((program == "blastp") && (blastpType == "phiblast"))
      jQuery('#blast_phiPattern').parent().parent().show();
    else
      jQuery('#blast_phiPattern').parent().parent().hide();
      
      
    if ((program == "blastp") && (blastpType == "phiblast"))
      jQuery('#phiwarn').show();
    else
      jQuery('#phiwarn').hide();
      
    
    
    if (bankType == 'persodb') {
      jQuery('#blast_persoBankFile').parent().parent().show();
      jQuery('#blast_dbPath').parent().parent().hide();
    }
    else {
      jQuery('#blast_persoBankFile').parent().parent().hide();
      jQuery('#blast_dbPath').parent().parent().show();
    }
    
    jQuery('#blast_lowComplex').attr('checked', !((program == "blastp") || ((program == "blastn") && (blastnType == "megablast"))));
    jQuery('#blast_softMasking').attr('checked', !(program != "blastn"));
}


