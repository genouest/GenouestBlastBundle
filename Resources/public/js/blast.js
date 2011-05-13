function blastLoaded(dbPathId) {
    refreshControls();
    
    program = jQuery('#blast_program').val();
    if ((program == "blastn") || (program == "tblastn"))
      bankType = jQuery('#blast_bankTypeNuc').val();
    else
      bankType = jQuery('#blast_bankTypeProt').val();
     
    reloadDbList('#'+dbPathId, getDbTypeFromBlastBankType(bankType, program), 'blast', 'false', 'true');
    
    jQuery('#blast_bankTypeNuc').change(function() {
      reloadDbList('#'+dbPathId, getDbTypeFromBlastBankType(jQuery(this).val(), jQuery('#blast_program').val()), 'blast', 'false', 'true');
      refreshControls();
    });
    
    jQuery('#blast_bankTypeProt').change(function() {
      reloadDbList('#'+dbPathId, getDbTypeFromBlastBankType(jQuery(this).val(), jQuery('#blast_program').val()), 'blast', 'false', 'true');
      refreshControls();
    });
    
    
    jQuery('#blast_program').change(function() {
      reloadDbList('#'+dbPathId, getDbTypeFromBlastProgram(jQuery(this).val()), 'blast', 'false', 'true');
      
      refreshControls();
    });
    
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
      jQuery('#blast_persoBankFile_file').parent().parent().parent().show();
      jQuery('#blast_dbPath').parent().parent().hide();
    }
    else {
      jQuery('#blast_persoBankFile_file').parent().parent().parent().hide();
      jQuery('#blast_dbPath').parent().parent().show();
    }
    
    jQuery('#blast_lowComplex').attr('checked', !((program == "blastp") || ((program == "blastn") && (blastnType == "megablast"))));
    jQuery('#blast_softMasking').attr('checked', !(program != "blastn"));
}

function getDbTypeFromBlastProgram(program)
{
  if ((program == 'blastp') || (program == 'blastx')) {
    return 'proteic';
  }

  return 'nucleic';
}

function getDbTypeFromBlastBankType(bankType, program)
{
  if (bankType == 'procgenome') {
    return 'genome/procaryotic';
  }
  else if (bankType == 'eucgenome') {
    return 'genome/eucaryotic';
  }

  return getDbTypeFromBlastProgram(program)
}

function getDbTypeFromBlastProgramLepidoDB(program)
{
  if ((program == 'blastp') || (program == 'blastx')) {
    return 'lepidodb/proteic';
  }

  return 'lepidodb/nucleic';
}

function getDbTypeFromBlastProgramAphidBase(program)
{
  if ((program == 'blastp') || (program == 'blastx')) {
    return 'aphidbase/proteic';
  }

  return 'aphidbase/nucleic';
}

function getDbTypeFromBlastProgramSpodoblast(program)
{
  if ((program == 'blastp') || (program == 'blastx')) {
    return 'spodoblast/proteic';
  }

  return 'spodoblast/nucleic';
}

function getDbTypeFromBlastProgramSlitblast(program)
{
  if ((program == 'blastp') || (program == 'blastx')) {
    return 'slitblast/proteic';
  }

  return 'slitblast/nucleic';
}
