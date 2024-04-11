<?php
$sleep = TRUE;
if (!$sleep):
	$TobaccoUse_HistoryDropdownMaster_ID_field = long_field_odbc('TobaccoUse_HistoryDropdownMaster_ID'); 
  $SexualHistory_HistoryDropdownMaster_ID = long_field_odbc('SexualHistory_HistoryDropdownMaster_ID'); 
  $SexualPartners_HistoryDropdownMaster_ID = long_field_odbc('SexualPartners_HistoryDropdownMaster_ID');
  $DrugHistory_HistoryDropdownMaster_ID = long_field_odbc('DrugHistory_HistoryDropdownMaster_ID');
  $AlcoholHistory_HistoryDropdownMaster_ID = long_field_odbc('AlcoholHistory_HistoryDropdownMaster_ID');
  $DrinkUnits_HistoryDropdownMaster_ID = long_field_odbc('DrinkUnits_HistoryDropdownMaster_ID');
//<!--- 
//	<responsibilities>This is the page for the creation of a new 'Social History' section of the Chart Notes pages using the data from the new Flex History Module</responsibilities>
//
//	<note author="Chris Hoffman" date="08 March 2011">File: comp_newSocialHistory.cfm
//													Case: 8899 - Created file
//													CASE 10,024
//													CASE 344 - Added NoContraception
//	</note>
//	<io>
//		<in>
//		</in>
//
//		<out>
//		</out>
//	</io>
//
//  --->
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.OrgId=Session.Org_Id>
//	<cfset Variables.sTimeOffset=Session.UTC_TimeOffset>
//	<cfset Variables.sDST=Session.UTC_DST>
//	<cfset Variables.sTimeZoneID=Session.UTC_TimeZoneId>
//</cflock>
//
//<cfif Trim(Attributes.UseDetailKeys) NEQ 1>
//	<cfset variables.bUseDetailKeys = false>
//<cfelse>
//	<cfset variables.bUseDetailKeys = true>
//</cfif>
if ($UseDetailKeys != 1) {
  $bUseDetailKeys = FALSE;
} else {
  $bUseDetailKeys = TRUE;
}

//<cfset Variables.TobaccoProducts = ''>
//<cfset Variables.Pack_Years = ''>
//<cfset Variables.SexualContacts = ''>
//<cfset Variables.Contraception = ''>
//<cfset Variables.Booze = ''>
//<!---CASE 10,024 --->
//<cfset Variables.ShowField = 0>
//
//<cfset Variables.dataObj = StructNew()>
//<cfset Variables.dataObj.patient_Id = Attributes.PatientKey>
//<cfset Variables.dataObj.patientId = Attributes.PatientKey>
//<cfset Variables.dataObj.orgTimeZoneOffset = Variables.sTimeOffset>
//<cfset Variables.dataObj.orgTimeZoneDST = Variables.sDST>
//<cfset Variables.dataObj.orgTimeZoneId = Variables.sTimeZoneID>
//
//<cfif variables.bUseDetailKeys eq true>
//	<!--- the Encounter or Referral Locked--->
//	<cfif isDefined('Attributes.Referral')>
//		<!--- This is being called from printreferrals.cfm--->
//		<cfset Variables.dataObj.SearchId = Attributes.KeyValue>
//	<cfelse>	
//		<cfset Variables.dataObj.RptType = Attributes.HEADERMASTERKEY>
//		<cfset Variables.dataObj.EncounterId = Attributes.PRIMARYKEY>
//		<cfset Variables.SearchIds = CreateObject("component","cfc.history.ChartNotes_History").getSearchIds(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//		<cfoutput query="Variables.SearchIds">
//			<cfset Variables.dataObj.SearchId = #Variables.SearchIds.ComponentKeys#>
//		</cfoutput>	
//	</cfif>
//</cfif>

  if($bUseDetailKeys == TRUE){
    if(isset($Referral)){ //SKIPP
      $SearchId = ""; //SKIPP
    }else{
     $sql = "			SELECT	E.ComponentKeys
			FROM	EncounterComponents E
			WHERE	E.Patient_ID = $PatientKey
					AND Encounter_ID = $PrimaryKey
					AND	HeaderMaster_ID = $HeaderMasterKey";
     
      $SearchIds = $this->ReportModel->data_db->query($sql);
      $SearchIds_num = $SearchIds->num_rows();
      $SearchIds_row = $SearchIds->row();     
      
      $SearchId = $SearchIds_row->ComponentKeys;
    }
  }

//<cfif IsDefined('Attributes.RefHeader')>
//	<cfset Variables.TextStyle = 'font-size: 12px; color: Black; font-weight: normal; font-face: Garamond, Arial, Helvetica;'>
//<cfelse>
//
//	<!---
//	The CFC call below returns the Font and Color information for the display of Chart Note body items.
//	Six items are returned:
//
//	Variables.BodyFontInfo.FontColor = Body Font Color  (ex. 000000 for black)
//	Variables.BodyFontInfo.FontSize = Body Font Size (ex. 12)
//	Variables.BodyFontInfo.FontFace = Body Font Family (ex. Times New Roman)
//	Variables.BodyFontInfo.FontWeight = Bold or Normal
//	Variables.BodyFontInfo.FontStyle = Italics or Normal
//	Variables.BodyFontInfo.FontDecoration = Underline or None
//	--->
//	<cfset variables.BodyFontInfo = CreateObject("component","cfc.chartnote.chartnote").getBodyFontInfo(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Attributes.HeaderKey)>
//	<cfset Variables.TextStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//</cfif>

  if(isset($RefHeader)){ //SKIPP
    $TextStyle = 'font-size: 12px; color: Black; font-weight: normal; font-face: Garamond, Arial, Helvetica;';
  }else{
    $data['data_db'] = $data_db;
    $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
    $TextStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  }
  
//<cfif variables.bUseDetailKeys eq false>
//	<!--- Not Locked, We need the FaceSheetNewHistory information query --->
//	<cfset Variables.HistoryInfo = CreateObject("component","cfc.history.FaceSheet_History").FaceSheetNewHistory(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//	<cfset Variables.PertinentInfo1 = CreateObject("component","cfc.history.FaceSheet_History").FaceSheetNewHistoryPertinents(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//<cfelse>
//	<!--- Locked, We need the ChartNotesHistory information query --->
//	<cfset Variables.HistoryInfo = CreateObject("component","cfc.history.ChartNotes_History").NewSocialHistory(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//	<cfset Variables.PertinentInfo1 = CreateObject("component","cfc.history.ChartNotes_History").NewSocialHistoryPertinents(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//</cfif>
//<cfset Variables.DropDown_Master = CreateObject("component","cfc.history.FaceSheet_History").FaceSheetDropDownMasterInfo(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>

  if($bUseDetailKeys == FALSE){
//    SKIPP UTCtoLocalTZ
    $sql = "SELECT TOP 1 H.History_Hist_ID, 
						H.Org_ID, 
						H.Patient_ID, 
						H.HistoryMedical_Dtl_ID, 
						H.HistorySurgical_Dtl_ID, 
            H.HistorySocial_Dtl_ID, 
						H.HistoryFamilyNotes_Dtl_ID, 
						H.HistoryObGyn_Dtl_ID, 
						H.DatePopulated_UTC, 
						PP.Sex,
            M.Notes as MedicalNotes, 
						M.LastEditedBy_Users_PK as MedicalEditedUserPK, 
						M.LastEditedOn_UTC as MedicalEditedOn,
						P.Notes as SurgicalNotes, 
						P.LastEditedBy_Users_PK as SurcialEditedUserPK, 
						P.LastEditedOn_UTC as SurgicalEditedOn,
						O.Menarche, 
						O.SBE_HistoryDropdownMaster_ID, 
						O.Menopause, 
						O.FullTerm, 
						O.Premature, 
						O.AbortionMiscarriages, 
						O.NowAlive, 
						O.TwinsTriplets, 
						O.Notes as ObNotes, 
						O.LastEditedBy_Users_PK as ObEditedUserPK, 
						O.LastEditedOn_UTC as ObEditedOn,
						S.TobaccoUse_HistoryDropdownMaster_ID, 
						S.Cigarettes, 
						S.Cigars, 
						S.Pipe, 
						S.ChewingTobacco, 
						S.PacksPerDay, 
						S.YearsSmoked, 
						S.TobaccoDateQuit, 
						S.TobaccoComments, 
						S.SexualHistory_HistoryDropdownMaster_ID, 
						S.SexualPartners_HistoryDropdownMaster_ID, 
						S.NumberPartnersYear, 
						S.Vaginal, 
						S.Anal, 
						S.Oral,
						S.Condoms, 
						S.Spermicides, 
						S.Rhythm, 
						S.IUD, 
						S.Hormonal, 
						S.Pill, 
						S.Diaphragm, 
						S.Surgical,
						S.SexualComments, 
						S.DrugHistory_HistoryDropdownMaster_ID,  
						S.DrugQuitDate, 
						S.AlcoholHistory_HistoryDropdownMaster_ID,  
						S.AlcoholQuitDate, 
						S.Beer, 
						S.Wine, 
						S.Liquor,
						S.NumberDrinks, 
						S.DrinkUnits_HistoryDropdownMaster_ID,  
						S.DrugAlcoholComments, 
						S.EducationHistory, 
						S.JobHistory, 
						S.Notes as SocialNotes, 
						S.LastEditedBy_Users_PK as SocialEditedUserPK, 
						S.LastEditedOn_UTC as SocialEditedOn,
            F.Notes as FamilyNotes, 
						F.LastEditedBy_Users_PK as FamilyNotesEditedUserPK, 
						F.LastEditedOn_UTC as FamilyNotesEditedOn
			FROM	History H, HistoryMedical_Dtl M, HistorySurgical_Dtl P, HistoryObGyn_Dtl O, HistorySocial_Dtl S, HistoryFamilyNotes_Dtl F, PatientProfile PP
			WHERE   (H.Patient_ID = $PatientKey) 
					AND (H.HistoryMedical_Dtl_ID = M.HistoryMedical_Dtl_Id)
					AND (H.HistorySurgical_Dtl_ID = P.HistorySurgical_Dtl_Id) 
					AND (H.HistoryObGyn_Dtl_ID = O.HistoryObGyn_Dtl_Id)
					AND (H.HistorySocial_Dtl_ID = S.HistorySocial_Dtl_Id) 
					AND (H.HistoryFamilyNotes_Dtl_ID = F.HistoryFamilyNotes_Dtl_ID)
					AND PP.Patient_ID = H.Patient_ID";
     
      $HistoryInfo = $this->ReportModel->data_db->query($sql);
      $HistoryInfo_num = $HistoryInfo->num_rows();
      $HistoryInfo_row = $HistoryInfo->row();
      
    $sql_GetIds = "SELECT TOP 1 
						H.HistoryMedical_Dtl_ID as Medical,
						H.HistorySurgical_Dtl_ID as Surgical,
						H.HistorySocial_Dtl_ID as Social
			FROM
				History H
			WHERE	
				H.Patient_ID = $PatientKey";
      $GetIds = $this->ReportModel->data_db->query($sql_GetIds);
      $GetIds_num = $GetIds->num_rows();
      $GetIds_row = $GetIds->row();
      
    $sql_SelectPertinents = "SELECT 	
        SC.SmartControlMaster_ID, SC.Type, SC.DisplayName, 
        SC.DatePopulated_UTC, SC.Hidden, SC.HiddenBy, 
        SC.HiddenOn_UTC, A.AnswerID, A.DetailID, 
        A.Org_ID, A.SmartControlAnswer, isNull(A.SmartControlComment, '') as SmartControlComment
			FROM	
        HistorySmartControlsMaster SC LEFT OUTER JOIN
        (SELECT HistoryMedical_Answer_ID as AnswerID, SmartControlMaster_ID, HistoryMedical_Dtl_ID as DetailID, Org_ID, SmartControlAnswer, isNull(SmartControlComment, '') as SmartControlComment 
        FROM HistoryMedical_Answer
        WHERE  	(HistoryMedical_Dtl_ID = $GetIds_row->Medical)
        UNION
        SELECT HistorySurgical_Answer_ID as AnswerID, SmartControlMaster_ID, HistorySurgical_Dtl_ID as DetailID, Org_ID, SmartControlAnswer, isNull(SmartControlComment, '') as SmartControlComment 
        FROM HistorySurgical_Answer
        WHERE  	(HistorySurgical_Dtl_ID = $GetIds_row->Surgical)
        UNION
        SELECT HistorySocial_Answer_ID as AnswerID, SmartControlMaster_ID, HistorySocial_Dtl_ID as DetailID, Org_ID, SmartControlAnswer, isNull(SmartControlComment, '') as SmartControlComment 
        FROM HistorySocial_Answer
        WHERE  	(HistorySocial_Dtl_ID = $GetIds_row->Social)) AS A 
        ON SC.SmartControlMaster_ID = A.SmartControlMaster_ID
			WHERE  	(SC.Hidden <> 1) OR (SC.Hidden IS NULL)";
    $PertinentInfo1 = $this->ReportModel->data_db->query($sql_SelectPertinents);
    $PertinentInfo1_num = $PertinentInfo1->num_rows();
    $PertinentInfo1_result = $PertinentInfo1->result();  
  }else{
//    SKIPP dbo.UTCtoLocalTZ
    $sql_NewSocialHistory = "SELECT
					S.TobaccoUse_HistoryDropdownMaster_ID, 
					S.Cigarettes, 
					S.Cigars, 
					S.Pipe, 
					S.ChewingTobacco, 
					S.PacksPerDay, 
					S.YearsSmoked, 
					S.TobaccoDateQuit, 
					S.TobaccoComments, 
					S.SexualHistory_HistoryDropdownMaster_ID, 
					S.SexualPartners_HistoryDropdownMaster_ID, 
					S.NumberPartnersYear, 
					S.Vaginal, 
					S.Anal, 
					S.Oral,
					S.Condoms, 
					S.Spermicides, 
					S.Rhythm, 
					S.IUD, 
					S.Hormonal, 
					S.Pill, 
					S.Diaphragm, 
					S.Surgical,
					S.SexualComments, 
					S.DrugHistory_HistoryDropdownMaster_ID,  
					S.DrugQuitDate, 
					S.AlcoholHistory_HistoryDropdownMaster_ID,  
					S.AlcoholQuitDate, 
					S.Beer, 
					S.Wine, 
					S.Liquor,
					S.NumberDrinks, 
					S.DrinkUnits_HistoryDropdownMaster_ID,  
					S.DrugAlcoholComments, 
					S.EducationHistory, 
					S.JobHistory, 
					S.Notes as SocialNotes, 
					S.LastEditedBy_Users_PK as SocialEditedUserPK, 
					S.LastEditedOn_UTC as SocialEditedOn
			FROM	HistorySocial_Dtl S	
			WHERE	S.HistorySocial_Dtl_Id = $SearchId";
    $HistoryInfo = $this->ReportModel->data_db->query($sql_NewSocialHistory);
    $HistoryInfo_num = $HistoryInfo->num_rows();
    $HistoryInfo_row = $HistoryInfo->row();
    
//  $sql_NewSocialHistoryPertinents = "SELECT 	SC.SmartControlMaster_ID, SC.Type, SC.DisplayName, 
//				          	SC.DatePopulated_UTC, SC.Hidden, SC.HiddenBy, 
//				          	SC.HiddenOn_UTC, A.AnswerID, A.DetailID, 
//				          	A.Org_ID, A.SmartControlAnswer, isNull(A.SmartControlComment, '') as SmartControlComment
//			FROM	HistorySmartControlsMaster SC LEFT OUTER JOIN
//				    	(
//				    	SELECT HistorySocial_Answer_ID as AnswerID, SmartControlMaster_ID, HistorySocial_Dtl_ID as DetailID, Org_ID, SmartControlAnswer, isNull(SmartControlComment, '') as SmartControlComment 
//				    	FROM HistorySocial_Answer
//				    	WHERE  	(HistorySocial_Dtl_ID = $SearchId)) AS A 
//				    	ON SC.SmartControlMaster_ID = A.SmartControlMaster_ID
//			WHERE  	(SC.Hidden <> 1) OR (SC.Hidden IS NULL)";
//  $NewSocialHistoryPertinents = $this->ReportModel->data_db->query($sql_NewSocialHistoryPertinents);
//  $NewSocialHistoryPertinents_num = $NewSocialHistoryPertinents->num_rows();
//  $NewSocialHistoryPertinents_row = $NewSocialHistoryPertinents->row();
  
  
    $sql_PertinentInfo1 = "SELECT Type, DisplayName, SmartControlAnswer,SmartControlComment
			FROM (
					SELECT 	SC.SmartControlMaster_ID, SC.Type, SC.DisplayName, 
				          						SC.DatePopulated_UTC, SC.Hidden, SC.HiddenBy, 
				          						SC.HiddenOn_UTC, A.AnswerID, A.DetailID, 
				          						A.Org_ID, A.SmartControlAnswer, isNull(A.SmartControlComment, '') as SmartControlComment
								FROM	HistorySmartControlsMaster SC LEFT OUTER JOIN
				    						(
				    						SELECT HistorySocial_Answer_ID as AnswerID, SmartControlMaster_ID, HistorySocial_Dtl_ID as DetailID, Org_ID, SmartControlAnswer, isNull(SmartControlComment, '') as SmartControlComment 
				    						FROM HistorySocial_Answer
				    						WHERE  	(HistorySocial_Dtl_ID = $SearchId)) AS A 
				    						ON SC.SmartControlMaster_ID = A.SmartControlMaster_ID
					WHERE  	(SC.Hidden <> 1) OR (SC.Hidden IS NULL)
) as tmp
			WHERE DetailId IS NOT NULL
			ORDER BY Type, DisplayName
";
  $PertinentInfo1 = $this->ReportModel->data_db->query($sql_PertinentInfo1);
  $PertinentInfo1_num = $PertinentInfo1->num_rows();
  $PertinentInfo1_result = $PertinentInfo1->result();
  
  }
  
  $sql_DropDown_Master = "SELECT	DisplayName,
					HistoryDropdownMaster_ID
			FROM	HistoryDropdownMaster";
  $DropDown_Master = $this->ReportModel->data_db->query($sql_DropDown_Master);
  $DropDown_Master_num = $DropDown_Master->num_rows();
  $DropDown_Master_result = $DropDown_Master->result();
  
//<p />
//<cfmodule template="componentheaders.cfm"
//		 EMRDataSource="#Attributes.EMRDataSource#"
//		 HeaderKey="#Attributes.HeaderKey#"
//		 PatientKey="#Attributes.PatientKey#"
//		 HeaderMasterKey="#Attributes.HeaderMasterKey#"
//		 FreeTextKey="#Attributes.FreeTextKey#"
//		 SOHeaders="#Attributes.SOHeaders#">
//		<cfset caller.HeaderNeeded = False>
//		<cfset caller.NeedTemplateHeader = False>

  $data['HeaderKey'] = $HeaderKey;
  $data['PatientKey'] = $PatientKey;
  $data['HeaderMasterKey'] = $HeaderMasterKey;
  $data['FreeTextKey'] = $FreeTextKey;
  $data['SOHeaders'] = $SOHeaders;
  $this->load->view('encounter/print/componentheaders', $data);
  
?>
	<table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">			
		<?php 
    if(isset($RefHeader)){
    ?>
			<!--- This is being called from printreferrals.cfm and needs a header --->
			<tr>
				<td colspan="12" style="font-size: 14px; color: Maroon; font-weight: bold; font-face: Garamond, Arial, Helvetica;"><?php echo $RefHeader; ?></td>
			</tr>	
      <?php
    }
    if($HistoryInfo_row->SocialNotes != ""){
      ?>
		<!--- CASE 10,024 Added cfif to display only if there is data --->
			<tr>
				<td width="7">&nbsp;</td>				
				<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top"><strong>Notes:</strong></td>	
				<td width="4">&nbsp;</td>				
				<td colspan="9" style="<?php echo $TextStyle ?>" align="left" valign="bottom" ><?php echo $HistoryInfo_row->SocialNotes;  ?></td>
			</tr>

			<tr>
				<td colspan="12">&nbsp;</td>
			</tr>	
    <?php
    }
    ?>

		<!--- CASE 10,024 (display this section only if there is tobacco related data) --->
    <?php
    
  
    
    
    if($HistoryInfo_row->TobaccoDateQuit != "" || $HistoryInfo_row->$TobaccoUse_HistoryDropdownMaster_ID_field != 56 &&
       trim($HistoryInfo_row->$TobaccoUse_HistoryDropdownMaster_ID_field)  != "" ||
       $HistoryInfo_row->Cigarettes == 1 ||
       $HistoryInfo_row->Cigars == 1 ||
       $HistoryInfo_row->Pipe == 1 ||
       $HistoryInfo_row->ChewingTobacco == 1 ||
       trim($HistoryInfo_row->PacksPerDay) != "" && $HistoryInfo_row->PacksPerDay != 0 ||
       trim($HistoryInfo_row->YearsSmoked) != "" && $HistoryInfo_row->YearsSmoked != 0 ||
       trim($HistoryInfo_row->TobaccoComments) != ""     
            ){
      
      $ShowField = 0;
      $H1 = "";
      $D1 = "";
      if($HistoryInfo_row->TobaccoDateQuit != ""){
        $H1 = 'Date Quit:';
        $D1 = trim($HistoryInfo_row->TobaccoDateQuit);
      }
      if( ($HistoryInfo_row->$TobaccoUse_HistoryDropdownMaster_ID_field != 56 && trim($HistoryInfo_row->$TobaccoUse_HistoryDropdownMaster_ID_field)) || ($HistoryInfo_row->$TobaccoUse_HistoryDropdownMaster_ID_field != 56 && trim($HistoryInfo_row->$TobaccoUse_HistoryDropdownMaster_ID_field) != "")){
        $ShowField = 1; 
      }
    ?>

<!---		Original
		<tr>
			<td rowspan="24" style="width:0.25in; #Variables.TextStyle#" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width="27%" style="#Variables.TextStyle#" align="right"><strong>History of Tobacco Use:</strong></td>
			<td colspan="5" style="#Variables.TextStyle#" >&nbsp;
				<cfloop query="Variables.DropDown_Master">
					<cfif Variables.DropDown_Master.HistoryDropdownMaster_ID eq Variables.HistoryInfo.TobaccoUse_HistoryDropdownMaster_ID>
						#Variables.DropDown_Master.DisplayName#
					</cfif>	
				</cfloop>
			</td>
			<td width="18%" style="#Variables.TextStyle#" align="right"><strong>Date Quit:</strong></td>
			<td width="12%" style="#Variables.TextStyle#" >&nbsp;#Trim(Variables.HistoryInfo.TobaccoDateQuit)#</td>
		</tr>	
		
--->		
			<!--- CASE 10,024 Added cfif to display only if there is data --->
		
		
			<tr>
				<td width="7">&nbsp;</td>				
        <?php
        if($ShowField == 1){
        ?>
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>History of Tobacco Use:</strong></td>
				<?php
        }else{
        ?>
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>History of Tobacco Use</strong></td>
        <?php
        }
        ?>
				<td width="4">&nbsp;</td>				
				<td colspan="5" style="<?php echo $TextStyle ?>" valign="top">
        <?php
        if($ShowField == 1){
          foreach ($DropDown_Master_result as $dmr){
            if($dmr->HistoryDropdownMaster_ID == $HistoryInfo_row->$TobaccoUse_HistoryDropdownMaster_ID_field || $dmr->HistoryDropdownMaster_ID == $HistoryInfo_row->$TobaccoUse_HistoryDropdownMaster_ID_field){
              echo $dmr->DisplayName;
            }
          }
        }
        ?>
				</td>
				<td width="10">&nbsp;</td>
				<td width="15%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong><?php echo $H1; ?></strong></td>
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle ?>"  valign="bottom" ><?php echo $D1; ?></td>
			</tr>		
		
			<!--- CASE 10,024 Added cfif to display only if there is data --->
			
      <?php 
//      <cfif	Variables.HistoryInfo.Cigarettes eq 1
//					OR Variables.HistoryInfo.Cigars eq 1
//					OR Variables.HistoryInfo.Pipe eq 1
//					OR Variables.HistoryInfo.ChewingTobacco eq 1
//			>
            if($HistoryInfo_row->Cigarettes == 1 || $HistoryInfo_row->Cigars == 1 || $HistoryInfo_row->Pipe == 1 || $HistoryInfo_row->ChewingTobacco == 1){
      ?>
				<tr>
					<td width="7">&nbsp;</td>				
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Tobacco Products Used:</strong></td>
					<td width="4">&nbsp;</td>
					<td colspan="9" style="<?php echo $TextStyle ?>"  valign="bottom" >
            <?php
            $TobaccoProducts = "";
            if($HistoryInfo_row->Cigarettes == 1){
//              <cfset Variables.TobaccoProducts = ListAppend(Variables.TobaccoProducts, 'Cigarettes')>
              $TobaccoProducts[] = 'Cigarettes';
            }
            if($HistoryInfo_row->Cigars == 1){
//              <cfset Variables.TobaccoProducts = ListAppend(Variables.TobaccoProducts, 'Cigars')>
              $TobaccoProducts[] = 'Cigars';
            }           
            if($HistoryInfo_row->Pipe == 1){
//              <cfset Variables.TobaccoProducts = ListAppend(Variables.TobaccoProducts, 'Pipe')>
              $TobaccoProducts[] = 'Pipe';
            }  
            if($HistoryInfo_row->ChewingTobacco == 1){
//              <cfset Variables.TobaccoProducts = ListAppend(Variables.TobaccoProducts, 'Chewing Tobacco')>
              $TobaccoProducts[] = 'Chewing Tobacco';
            }
            $Count = 0;
            $ItemOutput = "";
//            <cfloop list="#Variables.TobaccoProducts#" index="variables.I">
//							<cfif Variables.Count eq 0>
//								<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//								<cfset Variables.Count = 1>	
//							<cfelse>	
//								<cfif variables.I eq ListLast(Variables.TobaccoProducts)>
//									<cfset variables.ItemOutput = variables.ItemOutput & " and ">
//								<cfelse>
//									<cfset variables.ItemOutput = variables.ItemOutput & ", ">
//								</cfif>
//								<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//							</cfif>
//						</cfloop>
            $n=0;
            foreach ($TobaccoProducts as $tp){
             if($Count == 0){
               $ItemOutput = $ItemOutput.$TobaccoProducts[$n];
               $Count = 1;
             }else{
               if($TobaccoProducts[$n] == $TobaccoProducts[sizeof($TobaccoProducts)-1]){
                 $ItemOutput = $ItemOutput.$TobaccoProducts[$n]." and ";
               }else{
                 $ItemOutput = $ItemOutput.$TobaccoProducts[$n].", ";
               }
               $ItemOutput = $ItemOutput.$TobaccoProducts[$n];
             }
             $n++;
            }
            
            echo $ItemOutput;
            ?>
					</td>
				</tr>	
      <?php 
//    </cfif>
        } 
      ?>
<!---		ORIGINAL
		<tr>
			<td width="27%" style="#Variables.TextStyle#" align="right"><strong>Packs/Day:</strong></td>
			<td style="#Variables.TextStyle#" colspan="2" >&nbsp;&nbsp;#Variables.HistoryInfo.PacksPerDay#</td>
			<td width="12%" style="#Variables.TextStyle#" align="right"><strong>Years Smoked:</strong></td>
			<td style="#Variables.TextStyle#" colspan="2"  >&nbsp;#Variables.HistoryInfo.YearsSmoked#</td>							
			<td width="18%" style="#Variables.TextStyle#" align="right"><strong>Pack Years:</strong></td>
			<cfif Trim(Variables.HistoryInfo.PacksPerDay) neq '' AND Trim(Variables.HistoryInfo.YearsSmoked) neq ''> 
				<cfset Variables.Pack_Years = #Variables.HistoryInfo.PacksPerDay# * #Variables.HistoryInfo.YearsSmoked#>
			</cfif>
			<td width="12%" style="#Variables.TextStyle#" >&nbsp;#Variables.Pack_Years#</td>							
		</tr>	
		
--->		

			<!--- CASE 10,024 Added cfif to display only if there is data --->
				
      <?php
//      <cfif (Trim(Variables.HistoryInfo.PacksPerDay) neq '' AND Variables.HistoryInfo.PacksPerDay neq 0) OR 
//				   (Trim(Variables.HistoryInfo.YearsSmoked) neq '' AND Variables.HistoryInfo.YearsSmoked neq 0)> 
      if(trim($HistoryInfo_row->PacksPerDay) != "" && $HistoryInfo_row->PacksPerDay != 0 ||
         trim($HistoryInfo_row->YearsSmoked) != "" && $HistoryInfo_row->YearsSmoked != 0){
        
        $H1 = "";
        $D1 = "";
        $H2 = "";
        $D2 = "";
        $H3 = "";
        $D3 = "";
        
        $ItemCounter = 0;
        
//        <cfif (Trim(Variables.HistoryInfo.PacksPerDay) neq '' AND Variables.HistoryInfo.PacksPerDay neq 0)>
//					<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//					<cfset "Variables.H#Variables.ItemCounter#" = 'Packs/Day:'>
//					<cfset "Variables.D#Variables.ItemCounter#" = Variables.HistoryInfo.PacksPerDay>
//				</cfif>
//				<cfif (Trim(Variables.HistoryInfo.YearsSmoked) neq '' AND Variables.HistoryInfo.YearsSmoked neq 0)>
//					<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//					<cfset "Variables.H#Variables.ItemCounter#" = 'Years Smoked:'>
//					<cfset "Variables.D#Variables.ItemCounter#" = Variables.HistoryInfo.YearsSmoked>
//				</cfif>
//				<cfif ((Trim(Variables.HistoryInfo.PacksPerDay) neq '' AND Variables.HistoryInfo.PacksPerDay neq 0)) AND ((Trim(Variables.HistoryInfo.YearsSmoked) neq '' AND Variables.HistoryInfo.YearsSmoked neq 0))>
//					<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//					<cfset "Variables.H#Variables.ItemCounter#" = 'Pack Years:'>
//					<cfset "Variables.D#Variables.ItemCounter#" = Variables.HistoryInfo.PacksPerDay * Variables.HistoryInfo.YearsSmoked>
//				</cfif>
        
        $Var_H_1 = "";
        $Var_H_2 = "";
        $Var_H_3 = "";
        
        $Var_D_1= "";
        $Var_D_2= "";
        $Var_D_3= "";
        if(trim($HistoryInfo_row->PacksPerDay) != "" && $HistoryInfo_row->PacksPerDay != 0){
          $ItemCounter = $ItemCounter + 1;
          $Var_H_1 =  'Packs/Day:';
          $Var_D_1 =  $HistoryInfo_row->PacksPerDay;
        }
        if(trim($HistoryInfo_row->YearsSmoked) != "" && $HistoryInfo_row->YearsSmoked != 0){
          $ItemCounter = $ItemCounter + 1;
          $Var_H_2 = 'Years Smoked:';
          $Var_D_2 =  $HistoryInfo_row->YearsSmoked;          
        }
        if(trim($HistoryInfo_row->PacksPerDay)!= "" && $HistoryInfo_row->PacksPerDay != 0 && $HistoryInfo_row->YearsSmoked != "" && $HistoryInfo_row->YearsSmoked != 0){
          $ItemCounter = $ItemCounter + 1;
          $Var_H_3 = 'Years Years:';
          $Var_D_3 =  $HistoryInfo_row->PacksPerDay * $HistoryInfo_row->YearsSmoked;             
        }
      ?>
					
				<tr>
					<td width="7">&nbsp;</td>				
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong><?php echo $Var_H_1; ?></strong></td>
					<td width="4">&nbsp;</td>				
					<td style="<?php echo $TextStyle ?>" valign="bottom" ><?php echo $Var_D_1; ?></td>
					<td width="10">&nbsp;</td>				
					<td style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong><?php echo $Var_D_1." ".$Var_H_2; ?></strong></td>
					<td width="4">&nbsp;</td>				
					<td style="<?php echo $TextStyle ?>" valign="bottom"  ><?php echo $Var_D_2; ?></td>							
					<td width="10">&nbsp;</td>				
					<td width="15%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong><?php echo $Var_H_3; ?></strong></td>
					<td width="4">&nbsp;</td>				
					<td style="<?php echo $TextStyle ?>"  valign="bottom" ><?php echo $Var_D_3; ?></td>							
				</tr>
      <?php } ?>

			<!--- CASE 10,024 Added cfif to display only if there is data --->
      <?php 
      if(trim($HistoryInfo_row->TobaccoComments) != ""){
      ?>
				<tr>
					<td width="7">&nbsp;</td>				
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top"><strong>Comments:</strong></td>
					<td width="4">&nbsp;</td>				
					<td colspan="9" style="<?php echo $TextStyle ?>"  valign="bottom"><?php echo trim($HistoryInfo_row->TobaccoComments); ?></td>
				</tr>
      <?php } ?>
			<tr>
				<td colspan="12">&nbsp;</td>
			</tr>
    <?php } ?>

		<!--- CASE 10,024 (display this section only if there is sexual related data) --->
      <?php
       
      
      if($HistoryInfo_row->$SexualHistory_HistoryDropdownMaster_ID != 5 
              && trim($HistoryInfo_row->$SexualHistory_HistoryDropdownMaster_ID) != "" 
              || trim($HistoryInfo_row->$SexualPartners_HistoryDropdownMaster_ID != "") 
              && $HistoryInfo_row->$SexualPartners_HistoryDropdownMaster_ID != 8 
              || trim($HistoryInfo_row->NumberPartnersYear) != "" ||
         $HistoryInfo_row->Vaginal == 1 ||
         $HistoryInfo_row->Anal == 1 ||
         $HistoryInfo_row->Oral == 1 ||
         $HistoryInfo_row->Condoms == 1 ||
         $HistoryInfo_row->Spermicides == 1 ||
         $HistoryInfo_row->Rhythm == 1 ||
         $HistoryInfo_row->IUD == 1 ||
         $HistoryInfo_row->Hormonal == 1 ||
         $HistoryInfo_row->Pill == 1 ||
         $HistoryInfo_row->Diaphragm == 1 ||
         $HistoryInfo_row->Surgical == 1 ||
         //$HistoryInfo_row->NoContraception == 1 ||
         $HistoryInfo_row->SexualComments != ""     ){ 
      ?>
			<tr>
			
				<!--- CASE 10,024 Added cfif to display only if there is data --->
				<td width="7">&nbsp;</td>	
         <?php
        if($HistoryInfo_row->$SexualHistory_HistoryDropdownMaster_ID != "" && $HistoryInfo_row->$SexualHistory_HistoryDropdownMaster_ID != 5){
        ?>
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>History of<br>Sexual Activity:</strong></td>
        <?php 
        }else{
        ?>
      		<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>History of<br>Sexual Activity</strong></td>
        <?php
        }
        ?>
				<td width="4">&nbsp;</td>				
				<td style="<?php echo $TextStyle ?>" valign="bottom">
					<!--- CASE 10,024 Added cfif to display only if there is data --->
<!--					<cfif Trim(Variables.HistoryInfo.SexualHistory_HistoryDropdownMaster_ID) neq '' AND Variables.HistoryInfo.SexualHistory_HistoryDropdownMaster_ID neq 5>
						<cfloop query="Variables.DropDown_Master">
							<cfif Variables.DropDown_Master.HistoryDropdownMaster_ID eq Variables.HistoryInfo.SexualHistory_HistoryDropdownMaster_ID>
								#Variables.DropDown_Master.DisplayName#
							</cfif>	
						</cfloop>
					</cfif>-->
          <?php
            if(trim($HistoryInfo_row->$SexualHistory_HistoryDropdownMaster_ID) != "" && $HistoryInfo_row->$SexualHistory_HistoryDropdownMaster_ID != 5){
              foreach ($DropDown_Master_result as $dmr){
                if($dmr->HistoryDropdownMaster_ID == $HistoryInfo_row->$SexualHistory_HistoryDropdownMaster_ID){
                  echo $dmr->DisplayName;
                }
              }
            }
          ?>
				</td>
				<td width="10">&nbsp;</td>				
				<!--- CASE 10,024 Added cfif to display only if there is data --->
				
        <?php 
        if(trim($HistoryInfo_row->SexualPartners_HistoryDropdownMaster_ID != "") && $HistoryInfo_row->SexualPartners_HistoryDropdownMaster_ID != 8){
        ?>
        <!--<cfif (Trim(Variables.HistoryInfo.SexualPartners_HistoryDropdownMaster_ID) neq '' AND Variables.HistoryInfo.SexualPartners_HistoryDropdownMaster_ID neq 8)>-->
					<td style="<?php echo $TextStyle ?>" align="right" valign="bottom" nowrap><strong>Partners:</strong></td>
					<td width="4">&nbsp;</td>				
					<td style="<?php echo $TextStyle ?>" valign="bottom" nowrap>
            
<!--						<cfloop query="Variables.DropDown_Master">
							<cfif Variables.DropDown_Master.HistoryDropdownMaster_ID eq Variables.HistoryInfo.SexualPartners_HistoryDropdownMaster_ID>
								#Trim(Variables.DropDown_Master.DisplayName)#
							</cfif>	
						</cfloop>-->
            <?php
              foreach ($DropDown_Master_result as $dmr){
                  if($dmr->HistoryDropdownMaster_ID == $HistoryInfo_row->$SexualPartners_HistoryDropdownMaster_ID){
                    echo trim($dmr->DisplayName);
                  }
              }
            ?>
					</td>		
        <?php
        }else{
        ?>
        <td colspan="3">&nbsp;</td>
        <?php
        }
        ?>
        
      
				<!--- CASE 10,024 Added cfif to display only if there is data --->
				<td width="10">&nbsp;</td>				
        <?php 
        if($HistoryInfo_row->NumberPartnersYear != ""){
        ?>
					<td width="15%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Partners in the<br>Last Year:</strong></td>
					<td width="4">&nbsp;</td>				
					<td style="<?php echo $TextStyle ?>"  valign="bottom" ><?php echo $HistoryInfo_row->NumberPartnersYear; ?></td>							
				<?php
        }else{
        ?>
					<td width="15%">&nbsp;</td>
					<td oolspan="2">&nbsp;</td>
        <?php } ?>
			</tr>
      <!--SKIPP HERE PROGRES-->
			<!--- CASE 10,024 Added cfif to display only if there is data --->
<!--			<cfif 	Variables.HistoryInfo.Vaginal eq 1 OR
					Variables.HistoryInfo.Anal eq 1 OR
					Variables.HistoryInfo.Oral eq 1
			>-->
      <?php
      if($HistoryInfo_row->Vaginal == 1 || $HistoryInfo_row->Anal || $HistoryInfo_row->Oral == 1){
      ?>
				<tr>
					<td width="7">&nbsp;</td>				
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Sexual Contact:</strong></td>
					<td width="4">&nbsp;</td>				
					<td colspan="9" style="<?php echo $TextStyle ?>" valign="bottom" >
<!--						<cfset Variables.SexualContacts = ''>
						<cfif Variables.HistoryInfo.Vaginal eq 1>
							<cfset Variables.SexualContacts = ListAppend(Variables.SexualContacts, 'Vaginal')>
						</cfif>							
						<cfif Variables.HistoryInfo.Anal eq 1>									
							<cfset Variables.SexualContacts = ListAppend(Variables.SexualContacts, 'Anal')>
						</cfif>	
						<cfif Variables.HistoryInfo.Oral eq 1>									
							<cfset Variables.SexualContacts = ListAppend(Variables.SexualContacts, 'Oral')>
						</cfif>	
            <cfset Variables.Count = 0>
						<cfset variables.ItemOutput = ""> 
           
            <cfloop list="#Variables.SexualContacts#" index="variables.I">
							<cfif Variables.Count eq 0>
								<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
								<cfset Variables.Count = 1>	
							<cfelse>	
								<cfif variables.I eq ListLast(Variables.SexualContacts)>
									<cfset variables.ItemOutput = variables.ItemOutput & " and ">
								<cfelse>
									<cfset variables.ItemOutput = variables.ItemOutput & ", ">
								</cfif>
								<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
							</cfif>
						</cfloop>-->
            <?php
            $SexualContacts = "";
            $SexualContacts[] = "Vaginal";
            $SexualContacts[] = "Anal";
            $SexualContacts[] = "Oral";
            $Count = 0;
            $ItemOutput = "";
            foreach ($SexualContacts as $v){
              if($Count == 0){
                $ItemOutput = $ItemOutput.$v;
                $Count = 1;
              }else{
                if($v == $SexualContacts[sizeof($SexualContacts)-1]){
                  $ItemOutput = $ItemOutput." and ";
                }else{
                  $ItemOutput = $ItemOutput.", ";
                }
                $ItemOutput = $ItemOutput.$v;
              }
            }
            
            echo $ItemOutput;
            ?>

					</td>
				</tr>
      <?php
      }
      
      if($HistoryInfo_row->Condoms == 1 || $HistoryInfo_row->Rhythm == 1 ||
         $HistoryInfo_row->IUD == 1 ||
         $HistoryInfo_row->Hormonal == 1 ||
         $HistoryInfo_row->Pill == 1 ||
         $HistoryInfo_row->Diaphragm == 1 ||
         $HistoryInfo_row->Surgical == 1 ||
         $HistoryInfo_row->NoContraception == 1 
       ){
      ?>
      <!--LANJUT LAGI DISINI-->
			<!--- CASE 10,024 Added cfif to display only if there is data --->
				
				<tr>
					<td width="7">&nbsp;</td>				
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Contraception:</strong></td>
					<td width="4">&nbsp;</td>				
					<td colspan="9" style="<?php echo $TextStyle ?>" valign="bottom" >
            <?php
            $Contraception = array();
            if($HistoryInfo_row->Condoms == 1){
              $Contraception[] = "Condoms";
            }
            if($HistoryInfo_row->Spermicides == 1){
              $Contraception[] = "Spermicides";
            }
            if($HistoryInfo_row->Rhythm == 1){
              $Contraception[] = "Rhythm";
            }
            if($HistoryInfo_row->IUD == 1){
              $Contraception[] = "IUD";
            }
            if($HistoryInfo_row->Hormonal == 1){
              $Contraception[] = "Hormonal";
            }
            if($HistoryInfo_row->Pill == 1){
              $Contraception[] = "Pill";
            }
            if($HistoryInfo_row->Diaphragm == 1){
              $Contraception[] = "Diaphragm";
            }
            if($HistoryInfo_row->Surgical == 1){
              $Contraception[] = "Surgical";
            }
            if($HistoryInfo_row->NoContraception == 1){
              $Contraception[] = "None";
            }
//            <cfset Variables.Contraception = ''>
//						<cfif Variables.HistoryInfo.Condoms eq 1>
//							<cfset Variables.Contraception = ListAppend(Variables.Contraception, 'Condoms')>
//						</cfif>		
//						<cfif Variables.HistoryInfo.Spermicides eq 1>									
//							<cfset Variables.Contraception = ListAppend(Variables.Contraception, 'Spermicides')>
//						</cfif>	
//						<cfif Variables.HistoryInfo.Rhythm eq 1>									
//							<cfset Variables.Contraception = ListAppend(Variables.Contraception, 'Rhythm')>
//						</cfif>	
//						<cfif Variables.HistoryInfo.IUD eq 1>									
//							<cfset Variables.Contraception = ListAppend(Variables.Contraception, 'IUD')>
//						</cfif>	
//						<cfif Variables.HistoryInfo.Hormonal eq 1>									
//							<cfset Variables.Contraception = ListAppend(Variables.Contraception, 'Hormonal')>
//						</cfif>	
//						<cfif Variables.HistoryInfo.Pill eq 1>									
//							<cfset Variables.Contraception = ListAppend(Variables.Contraception, 'Pill')>
//						</cfif>	
//						<cfif Variables.HistoryInfo.Diaphragm eq 1>									
//							<cfset Variables.Contraception = ListAppend(Variables.Contraception, 'Diaphragm')>
//						</cfif>	
//						<cfif Variables.HistoryInfo.Surgical eq 1>									
//							<cfset Variables.Contraception = ListAppend(Variables.Contraception, 'Surgical')>
//						</cfif>	
//						<!--- CASE 344  Added NoContraception--->
//						<cfif Variables.HistoryInfo.NoContraception eq 1>									
//							<cfset Variables.Contraception = ListAppend(Variables.Contraception, 'None')>
//						</cfif>	
            
//            <cfset Variables.Count = 0>
//						<cfset variables.ItemOutput = ""> 
//						<cfloop list="#Variables.Contraception#" index="variables.I">
//							<cfif Variables.Count eq 0>
//								<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//								<cfset Variables.Count = 1>	
//							<cfelse>	
//								<cfif variables.I eq ListLast(Variables.Contraception)>
//									<cfset variables.ItemOutput = variables.ItemOutput & " and ">
//								<cfelse>
//									<cfset variables.ItemOutput = variables.ItemOutput & ", ">
//								</cfif>
//								<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//							</cfif>
//						</cfloop>	
//						#variables.ItemOutput#
            
            $Count = 0;
            $ItemOutput = "";
            $n=0;
            foreach ($Contraception as $v){
              if($Count == 0){
                $ItemOutput = $ItemOutput.$v;
                $Count = 1;
              }else{
                if($v == $Contraception[sizeof($Contraception)-1]){
                  $ItemOutput = $ItemOutput. " and ";
                }else{
                  $ItemOutput = $ItemOutput. ", ";
                }
                $ItemOutput = $ItemOutput.$v;
              }
              $n++;
            }
            echo $ItemOutput;
            ?>
						
					</td>
				</tr>
      
       <?php 
         }
         
//         <cfif Trim(Variables.HistoryInfo.SexualComments) neq ''>
         if($HistoryInfo_row->SexualComments != ""){
       ?>
			<!--- CASE 10,024 Added cfif to display only if there is data --->
				<tr>
					<td width="7">&nbsp;</td>				
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Comments:</strong></td>
					<td width="4">&nbsp;</td>				
					<td colspan="9" style="<?php echo $TextStyle ?>" valign="bottom"><?php echo trim($HistoryInfo_row->SexualComments) ?></td>
				</tr>
      <?php
//      	</cfif>	
         }
      ?>
			<tr>
				<td colspan="12">&nbsp;</td>
			</tr>
    <?php } ?>
		
		<!--- CASE 10,024 Added cfif to display only if there is data --->
    <?php
    $ShowField = 0;
    if(trim($HistoryInfo_row->$DrugHistory_HistoryDropdownMaster_ID) != "" && $HistoryInfo_row->$DrugHistory_HistoryDropdownMaster_ID != 12){
      $ShowField = 1;
    }
//    <cfset Variables.ShowField = 0>
//		<cfif (Trim(Variables.HistoryInfo.DrugHistory_HistoryDropdownMaster_ID) neq '' AND Variables.HistoryInfo.DrugHistory_HistoryDropdownMaster_ID neq 12) >
//			<cfset Variables.ShowField = 1>
//		</cfif>
    
    if($ShowField == 1 || trim($HistoryInfo_row->DrugQuitDate) != "" ){
//      <cfif Variables.ShowField eq 1 OR Trim(Variables.HistoryInfo.DrugQuitDate) neq ''>
    ?>
		
			<tr>
				<td width="7">&nbsp;</td>				
				<cfif Variables.ShowField eq 1 >
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Recreational Drug<br>Use History:</strong></td>
				<cfelse>
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Recreational Drug<br>Use History</strong></td>
				</cfif>

				<td width="4">&nbsp;</td>
				<td colspan="5" style="<?php echo $TextStyle ?>" valign="bottom" >
          <?php
//          <cfif Variables.ShowField eq 1>
//						<cfloop query="Variables.DropDown_Master">
//							<cfif Variables.DropDown_Master.HistoryDropdownMaster_ID eq Variables.HistoryInfo.$DrugHistory_HistoryDropdownMaster_ID>
//								#Trim(Variables.DropDown_Master.DisplayName)#
//							</cfif>	
//						</cfloop>
//					</cfif>
          if($ShowField == 1){
            foreach ($DropDown_Master_result as $dmr){
              if($dmr->HistoryDropdownMaster_ID == $HistoryInfo_row->$DrugHistory_HistoryDropdownMaster_ID){
                echo $trim($dmr->DisplayName);
              }
            }
          }
          ?>
					
				</td>
				<td width="10">&nbsp;</td>
        <?php
        
        if($HistoryInfo_row->DrugQuitDate != ""){
        ?>
					<td width="15%" style="<?php echo $TextStyle ?>" align="right" valign="bottom" nowrap><strong>Date Quit:</strong></td>
					<td width="4">&nbsp;</td>
					<td style="<?php echo $TextStyle ?>"  valign="bottom" ><?php echo trim($HistoryInfo_row->DrugQuitDate); ?></td>	
				<?php
        }else{
        ?>
					<td width="15%" style="<?php echo $TextStyle ?>" align="right" valign="bottom" >&nbsp;</td>
					<td colspan="2">&nbsp;</td>
        <?php
        }
        ?>
			</tr>
    <?php
//    		</cfif>
    }
    
//   <CFIF
//			(Trim(Variables.HistoryInfo.AlcoholHistory_HistoryDropdownMaster_ID) neq '' AND Variables.HistoryInfo.AlcoholHistory_HistoryDropdownMaster_ID neq 15) OR
//			Trim(Variables.HistoryInfo.AlcoholQuitDate) neq '' OR
//			Variables.HistoryInfo.Beer eq 1 OR
//			Variables.HistoryInfo.Wine eq 1 OR
//			Variables.HistoryInfo.Liquor eq 1 OR
//			Trim(Variables.HistoryInfo.NumberDrinks) neq '' OR
//			Trim(Variables.HistoryInfo.DrugAlcoholComments) neq ''
//		>
    if($HistoryInfo_row->$AlcoholHistory_HistoryDropdownMaster_ID != "" && $HistoryInfo_row->$AlcoholHistory_HistoryDropdownMaster_ID != 15 ||
       $HistoryInfo_row->AlcoholQuitDate != "" ||
       $HistoryInfo_row->Beer == 1 ||
       $HistoryInfo_row->Wine == 1 ||
       $HistoryInfo_row->Liquor == 1 ||     
       $HistoryInfo_row->NumberDrinks != "" ||    
       $HistoryInfo_row->DrugAlcoholComments != ""    
      ){
    ?>

		<!--- CASE 10,024 (display this section only if there is alchol related data) --->

			<tr>
				<td width="7">&nbsp;</td>
        <?php
        if(trim($HistoryInfo_row->$AlcoholHistory_HistoryDropdownMaster_ID) != "" && $HistoryInfo_row->$AlcoholHistory_HistoryDropdownMaster_ID != 15){
        ?>
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>History of Alcohol Use:</strong></td>		
				<?php
        }else{
        ?>
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>History of Alcohol Use</strong></td>
        <?php
        }
        ?>
				<td width="4">&nbsp;</td>				
				<td  colspan="5"  style="<?php echo $TextStyle ?>"  valign="bottom">
          <?php
//          <cfif (Trim(Variables.HistoryInfo.AlcoholHistory_HistoryDropdownMaster_ID) neq '' AND Variables.HistoryInfo.AlcoholHistory_HistoryDropdownMaster_ID neq 15)>
//						<cfloop query="Variables.DropDown_Master">
//							<cfif Variables.DropDown_Master.HistoryDropdownMaster_ID eq Variables.HistoryInfo.AlcoholHistory_HistoryDropdownMaster_ID>
//								#Trim(Variables.DropDown_Master.DisplayName)#
//							</cfif>	
//						</cfloop>
//					</cfif>
          if(trim($HistoryInfo_row->$AlcoholHistory_HistoryDropdownMaster_ID) != "" && $HistoryInfo_row->$AlcoholHistory_HistoryDropdownMaster_ID != 15){
            foreach ($DropDown_Master_result as $dmr){
              if($dmr->HistoryDropdownMaster_ID == $HistoryInfo_row->$AlcoholHistory_HistoryDropdownMaster_ID){
                echo trim($dmr->DisplayName);
              }
            }
          }
          ?>
				</td>
				<td width="10">&nbsp;</td>
        <?php
        if($HistoryInfo_row->AlcoholQuitDate != ""){
        ?>
					<td width="15%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Date Quit:</strong></td>
					<td width="4">&nbsp;</td>				
					<td style="<?php echo $TextStyle ?>"  valign="bottom" ><?php echo trim($HistoryInfo_row->AlcoholQuitDate); ?></td>
				<?php
        }else{
        ?>
					<td width="15%" style="<?php echo $TextStyle ?>" align="right" valign="bottom" >&nbsp;</td>
					<td colspan="2">&nbsp;</td>
        <?php
        }
        ?>
			</tr>
	
			<!--- CASE 10,024 Added cfif to display only if there is data --->
			
      <?php
//      <cfif	Variables.HistoryInfo.Beer eq 1 OR
//					Variables.HistoryInfo.Wine eq 1 OR
//					Variables.HistoryInfo.Liquor eq 1
//			>
      if($HistoryInfo_row->Beer == 1 || $HistoryInfo_row->Wine == 1 || $HistoryInfo_row == 1){
      ?>
				<tr>
					<td width="7">&nbsp;</td>
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Products Used:</strong></td>
					<td width="4">&nbsp;</td>
					<td colspan="9" style="<?php echo $TextStyle ?>" valign="bottom" >
						
            <?php
//            <cfset Variables.Booze = ''>
//						<cfif Variables.HistoryInfo.Beer eq 1>
//							<cfset Variables.Booze = ListAppend(Variables.Booze, 'Beer')>
//						</cfif>		
//						<cfif Variables.HistoryInfo.Wine eq 1>									
//							<cfset Variables.Booze = ListAppend(Variables.Booze, 'Wine')>
//						</cfif>	
//						<cfif Variables.HistoryInfo.Liquor eq 1>									
//							<cfset Variables.Booze = ListAppend(Variables.Booze, 'Liquor')>
//						</cfif>	
            $Booze = array();
            if($HistoryInfo_row->Beer == 1){
              $Booze[] = "Beer";
            }
            if($HistoryInfo_row->Wine == 1){
              $Booze[] = "Wine";
            }
            if($HistoryInfo_row->Liquor == 1){
              $Booze[] = "Liquor";
            }
            
            
//            <cfset Variables.Count = 0>
//						<cfset variables.ItemOutput = ""> 
//						<cfloop list="#Variables.Booze#" index="variables.I">
//							<cfif Variables.Count eq 0>
//								<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//								<cfset Variables.Count = 1>	
//							<cfelse>	
//								<cfif variables.I eq ListLast(Variables.Booze)>
//									<cfset variables.ItemOutput = variables.ItemOutput & " and ">
//								<cfelse>
//									<cfset variables.ItemOutput = variables.ItemOutput & ", ">
//								</cfif>
//								<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//							</cfif>
//						</cfloop>	
//						#variables.ItemOutput#
            
            $Count = 0;
            $ItemOutput = "";
            
            foreach ($Booze as $v){
              if($Count == 0){
                $ItemOutput = $ItemOutput.$v;
              }else{
                if($v == $Booze[sizeof($Booze)-1]){
                  $ItemOutput = $ItemOutput." and ";
                }else{
                  $ItemOutput = $ItemOutput.", ";
                }
                $ItemOutput = $ItemOutput.$v; 
              }
            }
            echo $ItemOutput;
            ?>

					</td>
				</tr>
			</cfif>
      <?php
      }
      ?>
			<!--- CASE 10,024 Added cfif to display only if there is data --->
      <?php
      if($HistoryInfo_row->NumberDrinks != ""){
      ?>
				<tr>
					<td width="7">&nbsp;</td>
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Total Alcohol<br>Consumption:</strong></td>
					<td width="4">&nbsp;</td>
					<td colspan="9" style="<?php echo $TextStyle ?>" valign="bottom">
					
            <?php
//            	#Variables.HistoryInfo.NumberDrinks# / 
//						<cfloop query="Variables.DropDown_Master">
//							<cfif Variables.DropDown_Master.HistoryDropdownMaster_ID eq Variables.HistoryInfo.DrinkUnits_HistoryDropdownMaster_ID>
//								#Trim(Variables.DropDown_Master.DisplayName)#
//							</cfif>	
//						</cfloop>
            echo $HistoryInfo_row->NumberDrinks;
            foreach ($DropDown_Master_result as $dmr){
              if($dmr->HistoryDropdownMaster_ID == $HistoryInfo_row->$DrinkUnits_HistoryDropdownMaster_ID){
                echo trim($dmr->DisplayName);
              }
            }
            ?>
					</td>
				</tr>
      <?php
      }
      ?>
			<!--- CASE 10,024 Added cfif to display only if there is data --->
      <?php
      if($HistoryInfo_row->DrugAlcoholComments != ""){
      ?>
				<tr>
					<td width="7">&nbsp;</td>
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Comments:</strong></td>
					<td width="4">&nbsp;</td>
					<td colspan="9" style="<?php echo $TextStyle ?>" valign="bottom"><?php echo trim($HistoryInfo_row->DrugAlcoholComments); ?></td>
				</tr>
      <?php
      }
      ?>
			<tr>
				<td colspan="12">&nbsp;</td>
			</tr>
		
    <?php
//    </CFIF>
      }
    ?>
		
		<!--- CASE 10,024 Added cfif to display only if there is data --->
    <?php
    if($HistoryInfo_row->EducationHistory != ""){
    ?>
			<tr>
				<td width="7">&nbsp;</td>
				<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Educational History:</strong></td>
				<td width="4">&nbsp;</td>
				<td colspan="9" style="<?php echo $TextStyle ?>" valign="bottom"><?php echo trim($HistoryInfo_row->EducationHistory); ?></td>
			</tr>
    <?php
    }
    if($HistoryInfo_row->JobHistory != ""){
    ?>
		<!--- CASE 10,024 Added cfif to display only if there is data --->
			<tr>
				<td width="7">&nbsp;</td>
				<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Occupational History:</strong></td>
				<td width="4">&nbsp;</td>
				<td colspan="7" style="<?php echo $TextStyle ?>" valign="bottom"><?php echo trim($HistoryInfo_row->JobHistory); ?></td>
			</tr>
    <?php
    }
    if($HistoryInfo_row->EducationHistory != "" || $HistoryInfo_row->JobHistory != ""){
    ?>
			<tr>
				<td colspan="12">&nbsp;</td>
			</tr>	
    <?php
    }
    
//    <cfset Variables.ShowField = 0>
//		<cfloop query="Variables.PertinentInfo1">
//			<cfif Variables.PertinentInfo1.Type eq 'S' AND Variables.PertinentInfo1.SmartControlAnswer eq 'True'>
//				<cfset Variables.ShowField = 1>
//			</cfif>
//		</cfloop>
    
    $ShowField = 0;
    foreach ($PertinentInfo1_result as $pr){
      if($pr->Type == 'S' && $pr->SmartControlAnswer == 'True'){
        $ShowField = 1;
      }
    }
    
//    <!--- CASE 10,024 Added cfif to display only if there is data --->
//		
//		<cfif Variables.ShowField neq 1>
//			<cfloop query="Variables.PertinentInfo1">		
//				<cfif Variables.PertinentInfo1.Type eq 'S' AND Variables.PertinentInfo1.SmartControlAnswer eq 'False'>
//					<cfset Variables.ShowField = 2>
//				</cfif>
//			</cfloop>
//		<cfelse>	
//			<cfloop query="Variables.PertinentInfo1">		
//				<cfif Variables.PertinentInfo1.Type eq 'S' AND Variables.PertinentInfo1.SmartControlAnswer eq 'False'>
//					<cfset Variables.ShowField = 3>
//				</cfif>
//			</cfloop>
//		</cfif>
    if($ShowField  != 1){
      foreach ($PertinentInfo1_result as $pr){
        if($pr->Type == 'S' && $pr->SmartControlAnswer == 'False'){
          $ShowField = 2;
        }
      }
    }else{
      foreach ($PertinentInfo1_result as $pr){
        if($pr->Type == 'S' && $pr->SmartControlAnswer == 'False'){
          $ShowField = 3;
        }
      }
    }
    ?>
	
		
<!--- ********** --->	

    <?php
//    <cfif Variables.ShowField neq 0>	
    if($ShowField != 0){
    ?>
			<tr>
				<td width="7">&nbsp;</td>
				<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top"><strong>Behaviors/Habits</strong></td>
				<td colspan="10">&nbsp;</td>
			</tr>
      <?php
      if($ShowField == 1 || $ShowField == 3){
      ?>
				<tr>
					<td width="7">&nbsp;</td>
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top"><strong>Pertinent Positives:</strong></td>
					<td width="4">&nbsp;</td>
					<td colspan="9"  style="<?php echo $TextStyle ?>" valign="bottom" >
						<!---Create a list of all the answers for the Pertinent output to find the last one to change a ',' with an 'and' --->
						<?php
            
//            <cfset Variables.pertlist = ''>
//						<cfloop query="Variables.PertinentInfo1">
//							<cfif Variables.PertinentInfo1.Type eq 'S' AND Variables.PertinentInfo1.SmartControlAnswer eq 'True'>
//								<cfset Variables.PertDisplayValue = ''>
//								<cfif #Trim(Variables.PertinentInfo1.SmartControlComment)# neq ''>
//									 <cfset Variables.PertDisplayValue = Variables.PertinentInfo1.DisplayName&'&nbsp;('&#Trim(Variables.PertinentInfo1.SmartControlComment)#&')'>	
//								<cfelse>
//									<cfset Variables.PertDisplayValue = Variables.PertinentInfo1.DisplayName>
//								</cfif>											
//								<cfif Variables.pertlist eq ''>
//									<cfset Variables.pertlist = Variables.PertDisplayValue>
//								<cfelse>				
//									<cfset Variables.pertlist = Variables.pertlist&','&Variables.PertDisplayValue>					
//								</cfif>
//							</cfif>	
//						</cfloop>
              $pertlist = array();
              foreach ($PertinentInfo1_result as $pr){
                if($pr->Type == 'S' && $pr->SmartControlAnswer == 'True'){
                  $PertDisplayValue = '';
                  
                  if($pr->SmartControlComment != ""){
                    $PertDisplayValue = $pr->DisplayName." &nbsp; ('".trim($pr->SmartControlComment)."')";
                  }else{
                    $PertDisplayValue = $pr->DisplayName;
                  }
                  
                  if($PertDisplayValue == ""){
                    $pertlist[] = $PertDisplayValue;
                  }else{
//                    $pertlist[] = $pertlist.", ".$PertDisplayValue;
                    $pertlist[] = $PertDisplayValue;
                  }
                }
              }
//            <cfset Variables.PertinentInfoCount = 0>
//						<cfset variables.ItemOutput = ""> 
//						<cfloop list="#Variables.pertlist#" index="variables.I">
//							<cfif Variables.PertinentInfoCount eq 0>
//								<cfset variables.ItemOutput = variables.ItemOutput & "The patient has a history of " & variables.I>
//								<cfset Variables.PertinentInfoCount = 1>
//							<cfelse>
//								<cfif variables.I eq ListLast(Variables.pertlist)>
//									<cfset variables.ItemOutput = variables.ItemOutput & " and ">
//								<cfelse>
//									<cfset variables.ItemOutput = variables.ItemOutput & ", ">
//								</cfif>	
//								<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//							</cfif>
//						</cfloop>
              $PertinentInfoCount = 0;
              $ItemOutput = "";
              foreach ($pertlist as $pl){
                if($PertinentInfoCount == 0){
                  $ItemOutput = $ItemOutput." The patient has a history of ".$pl;
                  $PertinentInfoCount = 1;
                }else{
                  if($pl == $pertlist[sizeof($pertlist)-1]){
                    $ItemOutput = $ItemOutput." and ";
                  }else{
                    $ItemOutput = $ItemOutput.", ";
                  }
                  $ItemOutput = $ItemOutput." ".$pl;
                }
              }
              
//            <cfif Variables.PertinentInfoCount eq 0>
//							<cfset variables.ItemOutput = "&nbsp;">
//						<cfelse>
//							<cfset variables.ItemOutput = variables.ItemOutput & ".">
//						</cfif>
//						#variables.ItemOutput#
              if($PertinentInfoCount == 0){
                $ItemOutput = "&nbsp;";
              }else{
                $ItemOutput = $ItemOutput.".";
              }
              echo $ItemOutput;
            ?>
					</td>
				</tr>
      <?php
      }
      
      if($ShowField == 2 || $ShowField == 3){
        
      ?>
				<tr>
					<td width="7">&nbsp;</td>
					<td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top"><strong>Pertinent Negatives:</strong></td>
					<td width="4">&nbsp;</td>
					<td colspan="9" style="<?php echo $TextStyle ?>" valign="bottom">
						<!---Create a list of all the answers for the Pertinent output to find the last one to change a ',' with an 'and' --->
						
            <?php
//            <cfset Variables.pertlist = ''>
//						<cfloop query="Variables.PertinentInfo1">
//							<cfif Variables.PertinentInfo1.Type eq 'S' AND Variables.PertinentInfo1.SmartControlAnswer eq 'False'>
//								<cfset Variables.PertDisplayValue = ''>
//								<cfif #Trim(Variables.PertinentInfo1.SmartControlComment)# neq ''>
//									<cfset Variables.PertDisplayValue = Variables.PertinentInfo1.DisplayName&'&nbsp;('&#Trim(Variables.PertinentInfo1.SmartControlComment)#&')'>	
//								<cfelse>
//									<cfset Variables.PertDisplayValue = Variables.PertinentInfo1.DisplayName>
//								</cfif>											
//								<cfif Variables.pertlist eq ''>
//									<cfset Variables.pertlist = Variables.PertDisplayValue>
//								<cfelse>				
//									<cfset Variables.pertlist = Variables.pertlist&','&Variables.PertDisplayValue>					
//								</cfif>
//							</cfif>	
//						</cfloop>
                    
            $pertlist = array();
            foreach ($PertinentInfo1_result as $pr){
              if($pr == "S" && $pr->SmartControlAnswer == 'False'){
                $PertDisplayValue = "";
                
                if(trim($pr->SmartControlComment) != ""){
                  $PertDisplayValue = $pr->DisplayName." &nbsp; (".trim($pr->SmartControlComment).")";
                }else{
                  $PertDisplayValue = $pr->DisplayName;
                }
                
                if($pertlist == ""){
                  $pertlist[] = $PertDisplayValue;
                }else{
//                $pertlist[] = $pertlist.$PertDisplayValue;
                  $pertlist[] = $PertDisplayValue;
                }
              }
            }
            
//            <cfset Variables.PertinentInfoCount = 0>
//						<cfset variables.ItemOutput = ""> 
//						<cfloop list="#Variables.pertlist#" index="variables.I">
//							<cfif Variables.PertinentInfoCount eq 0>
//								<cfset variables.ItemOutput = variables.ItemOutput & "The patient denies any history of " & variables.I>
//								<cfset Variables.PertinentInfoCount = 1>
//							<cfelse>
//								<cfif variables.I eq ListLast(Variables.pertlist)>
//									<cfset variables.ItemOutput = variables.ItemOutput & " and ">
//								<cfelse>
//									<cfset variables.ItemOutput = variables.ItemOutput & ", ">
//								</cfif>	
//								<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//							</cfif>
//						</cfloop>
                    
            $PertinentInfoCount = 0;
            $ItemOutput = "";
            foreach ($pertlist as $p){
              if($PertinentInfoCount == 0){
                $ItemOutput = $ItemOutput." The patient denies any history of ".$p;
                $PertinentInfoCount = 1;
              }else{
                if($p == $pertlist[sizeof($pertlist)-1]){
                  $ItemOutput = $ItemOutput." and ";
                }else{
                  $ItemOutput = $ItemOutput.", ";
                }
                $ItemOutput = $ItemOutput.$p; 
              }
            }
            
//            <cfif Variables.PertinentInfoCount eq 0>
//							<cfset variables.ItemOutput = "&nbsp;">
//						<cfelse>
//							<cfset variables.ItemOutput = variables.ItemOutput & ".">
//						</cfif>
//						#variables.ItemOutput#
            
            if($PertinentInfoCount == 0){
              $ItemOutput = "&nbsp;";
            }else{
              $ItemOutput = $ItemOutput.".";
            }
            echo $ItemOutput;
            ?>
						
					</td>
				</tr>
      <?php
      }
      ?>

    <?php
//    </cfif>	
    }
    
//    <cfset Variables.dataObj.EditBy = Variables.HistoryInfo.SocialEditedUserPK>
//		<cfset Variables.WhoDidIt = CreateObject("component","cfc.history.ChartNotes_History").getWhoDidIt(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//		<cfset Variables.EditBy = #Trim(Variables.WhoDidIt.FName)# & ' ' & #Trim(Variables.WhoDidIt.MI)# & ' ' & #Trim(Variables.WhoDidIt.LName)#>
    $sql = "SELECT	Top 1
					LName,
					FName,
					MI
			FROM	" . $user_db . ".dbo.Users
			WHERE	Id = $HistoryInfo_row->SocialEditedUserPK";

    $WhoDidIt = $this->ReportModel->data_db->query($sql);
    $WhoDidIt_num = $WhoDidIt->num_rows();
    $WhoDidIt_row = $WhoDidIt->row();
    
    $EditBy = $WhoDidIt_row->FName." ".trim($WhoDidIt_row->MI)." ".trim($WhoDidIt_row->LName);
    ?>

		
		<tr>
			<td width="7">&nbsp;</td>				
			<td width="1%" style="<?php echo $TextStyle ?>" vailgn="top" align="right" nowrap><strong>Last Edited On:</strong></td>
			<td width="4">&nbsp;</td>				
			<td style="<?php echo $TextStyle ?>" vailgn="bottom"><?php echo date('m/d/Y', strtotime($HistoryInfo_row->SocialEditedOn)); ?> </td>
			<td colspan="5">&nbsp;</td>
			<td style="<?php echo $TextStyle ?>" vailgn="top" align="right" nowrap><strong>Last Edited By:</strong></td>
			<td width="4">&nbsp;</td>
			<td style="<?php echo $TextStyle ?>" vailgn="bottom" nowrap><?php echo $EditBy; ?></td>					
		</tr>
		
	</table>





<?php endif;?>