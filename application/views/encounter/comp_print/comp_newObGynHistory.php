<?php
$stop = TRUE;
if(!$stop):
//<!--- 
//	<responsibilities>This is the page for the creation of a new 'Ob\Gyn History' section of the Chart Notes pages using the data from the new Flex History Module</responsibilities>
//
//	<note author="Chris Hoffman" date="08 March 2011">File: comp_newObGynHistory.cfm
//													Case: 8899 - Created file
//													CASE: 10,024 
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
//
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

//
//<cfset Variables.dataObj = StructNew()>
//<cfset Variables.dataObj.patient_Id = Attributes.PatientKey>
//<cfset Variables.dataObj.patientId = Attributes.PatientKey>
//<cfset Variables.dataObj.orgTimeZoneOffset = Variables.sTimeOffset>
//<cfset Variables.dataObj.orgTimeZoneDST = Variables.sDST>
//<cfset Variables.dataObj.orgTimeZoneId = Variables.sTimeZoneID>	
//<!---CASE 10,024 --->
//<cfset Variables.ShowField = 0>
//
//<cfif variables.bUseDetailKeys eq true>
//	<!--- the Encounter or Referral Locked--->
//	<cfif isDefined('Attributes.Referral')>
//
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

$ShowField = 0;
if ($bUseDetailKeys) {
  if (isset($Referral)) {
    $SearchId = 0; //SKIPP
  } else {
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


//<cfif variables.bUseDetailKeys eq false>
//	<!--- Not Locked, We need the FaceSheetNewHistory information query --->
//	<cfset Variables.HistoryInfo = CreateObject("component","cfc.history.FaceSheet_History").FaceSheetNewHistory(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//<cfelse>
//	<!--- Locked, We need the ChartNotesHistory information query --->
//	<cfset Variables.HistoryInfo = CreateObject("component","cfc.history.ChartNotes_History").NewObGynHistory(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//</cfif>
//<cfset Variables.DropDown_Master = CreateObject("component","cfc.history.FaceSheet_History").FaceSheetDropDownMasterInfo(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>

  if($bUseDetailKeys == FALSE){
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
  }else{
//    SKIPP UTCtoLocalTZ
    $sql = "SELECT
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
					O.LastEditedOn_UTC as ObEditedOn
			FROM	HistoryObGyn_Dtl O		
			WHERE	O.HistoryObGyn_Dtl_Id = $SearchId";
     
      $HistoryInfo = $this->ReportModel->data_db->query($sql);
      $HistoryInfo_num = $HistoryInfo->num_rows();
      $HistoryInfo_row = $HistoryInfo->row();   
  }
  
 
  
$sql_DropDown_Master = "SELECT	DisplayName,
					HistoryDropdownMaster_ID
			FROM	HistoryDropdownMaster";
  $DropDown_Master = $this->ReportModel->data_db->query($sql_DropDown_Master);
  $DropDown_Master_num = $DropDown_Master->num_rows();
  $DropDown_Master_result = $DropDown_Master->result();

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

if(isset($RefHeader)){
    $TextStyle = 'font-size: 12px; color: Black; font-weight: normal; font-face: Garamond, Arial, Helvetica;';
  }else {
  $data['data_db'] = $data_db;
  $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
  $TextStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
}
echo "<p />	";
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

$spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

?>

	<table  border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">		
		
    <?php
    if(isset($RefHeader)){
    ?>
    <tr>
				<td colspan="12" style="font-size: 14px; color: Maroon; font-weight: bold; font-face: Garamond, Arial, Helvetica;"><?php echo $RefHeader; ?></td>
		</tr>	
    <?php
    }
    ?>
    
		
<!---		Original
		<tr>
			<td rowspan="5" style="width:0.25in; #Variables.TextStyle#" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width="18%" style="#Variables.TextStyle#" align="right"><strong>Menarche:</strong></td>
			<td width="11%" style="#Variables.TextStyle#" >&nbsp;#Variables.HistoryInfo.Menarche#</td>
			<td width="2%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td width="17%" style="#Variables.TextStyle#" align="right"><strong>Full Term:</strong></td>
			<td width="17%" style="#Variables.TextStyle#">&nbsp;#Variables.HistoryInfo.FullTerm#</td>							
			<td width="2%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>							
			<td width="13%" style="#Variables.TextStyle#" align="right"><strong>Now Alive:</strong></td>
			<td width="22%" #Variables.TextStyle#  >&nbsp;#Variables.HistoryInfo.NowAlive#</td>							
		</tr>	
		
--->		
		
		<!--- CASE 10,024 Added cfif to display only if there is data --->
		<?php
    if($HistoryInfo_row->Menarche != "" || trim($HistoryInfo_row->FullTerm) != "" || trim($HistoryInfo_row->NowAlive) != ""){
      $H1 = "";
      $D1 = "";
      $H2 = "";
      $D2 = "";
      $H3 = "";
      $D3 = "";
      $ItemCounter = 0;
      
//      <cfif Trim(Variables.HistoryInfo.Menarche) neq ''>
//				<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Menarche:'>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.HistoryInfo.HistoryInfo>
//			</cfif>
//			<cfif Trim(Variables.HistoryInfo.FullTerm) neq ''>	
//				<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Full Term:'>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.HistoryInfo.FullTerm>
//			</cfif>
//			<cfif Trim(Variables.HistoryInfo.NowAlive) neq ''>
//				<cfset Variables.ItemCounter = Variables.ItemCounter + 1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Now Alive:'>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.HistoryInfo.NowAlive>
//			</cfif>
      
      if($HistoryInfo_row->Menarche != ""){
        $ItemCounter = $ItemCounter+1;
        $H1 = 'Menarche:';
        $D1 = $HistoryInfo_row->HistoryInfo;
      }
      if($HistoryInfo_row->FullTerm != ""){
        $ItemCounter = $ItemCounter+1;
        $H2 = 'Full Term:';
        $D2 = $HistoryInfo_row->FullTerm;
      }
      if($HistoryInfo_row->NowAlive != ""){
        $H3 = 'Now Alive:';
        $D3 = $HistoryInfo_row->NowAlive;
      }
    ?>
		

			<tr>
				<td width="7">&nbsp;</td>
				<td width="1%" style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong><?php echo $H1; ?></strong></td>
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>"  valign="top" ><?php echo $D1; ?></td>
				<td width="10">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong><?php echo $H2; ?></strong></td>
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>"  valign="top" ><?php echo $D2; ?></td>							
				<td width="10">&nbsp;</td>
				<td width="15%" style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong><?php echo $H3; ?></strong></td>
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>"  valign="top" ><?php echo $D3; ?></td>							
			</tr>		
    <?php
    }
    ?>
		
<!---		Original
		
		<tr>
			<td width="18%" style="#Variables.TextStyle#" align="right"><strong>Menopause:</strong></td>
			<td width="11%" style="#Variables.TextStyle#" >&nbsp;#Variables.HistoryInfo.Menopause#</td>
			<td width="2%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>				
			<td width="17%" style="#Variables.TextStyle#" align="right"><strong>Premature:</strong></td>
			<td width="17%" style="#Variables.TextStyle#" >&nbsp;#Variables.HistoryInfo.Premature#</td>		
			<td width="2%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>									
			<td width="13%" style="#Variables.TextStyle#" align="right"><strong>Twins/Triplets:</strong></td>
			<td width="22%" style="#Variables.TextStyle#" >&nbsp;#Variables.HistoryInfo.TwinsTriplets#</td>							
		</tr>	
		
--->		
		<!--- CASE 10,024 Added cfif to display only if there is data --->
		<?php
    if(trim($HistoryInfo_row->Menopause) != "" || trim($HistoryInfo_row->Premature) != "" || trim($HistoryInfo_row->TwinsTriplets) != ""){
      $H1 = "";
      $D1 = "";
      $H2 = "";
      $D2 = "";
      $H3 = "";
      $D3 = "";
      $ItemCounter = 0;
      
//      <cfif Trim(Variables.HistoryInfo.Menopause) neq ''>
//				<cfset Variables.ItemCounter = Variables.ItemCounter +1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Menopause:'>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.HistoryInfo.Menopause>
//			</cfif>
//			<cfif Trim(Variables.HistoryInfo.Premature) neq ''>	
//				<cfset Variables.ItemCounter = Variables.ItemCounter +1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Premature:'>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.HistoryInfo.Premature>
//			</cfif>
//			<cfif trim(Variables.HistoryInfo.TwinsTriplets) neq ''>
//				<cfset Variables.ItemCounter = Variables.ItemCounter +1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Twins/Triplets:'>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.HistoryInfo.TwinsTriplets>
//			</cfif>
      
      if($HistoryInfo_row->Menopause != ""){
        $ItemCounter = $ItemCounter+1;
        $H1 = 'Menopause:';
        $D1 = $HistoryInfo_row->Menopause;
      }
      if($HistoryInfo_row->Premature != ""){
        $ItemCounter = $ItemCounter+1;
        $H2 = 'Premature::';
        $D2 = $HistoryInfo_row->Premature;
      }
      if($HistoryInfo_row->TwinsTriplets != ""){
        $H3 = 'Twins/Triplets:';
        $D3 = $HistoryInfo_row->TwinsTriplets;
      }
    ?>

			<tr>
				<td width="7">&nbsp;</td>
				<td width="1%" style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong><?php echo $H1; ?></strong></td>
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>"  valign="top" ><?php echo $D1; ?></td>
				<td width="10">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong><?php echo $H2; ?></strong></td>
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>"  valign="top" ><?php echo $D2; ?></td>							
				<td width="10">&nbsp;</td>
				<td width="15%" style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong><?php echo $H3; ?></strong></td>
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>"  valign="top" ><?php echo $D3; ?></td>							
			</tr>		
    <?php
    }
    ?>
		
<!---	Original	
		<tr>
			<td width="18%" style="#Variables.TextStyle#" align="right" valign="top"><strong>SBE:</strong></td>
			<td width="11%" style="#Variables.TextStyle#" >
				<cfloop query="Variables.DropDown_Master">
					<cfif Variables.DropDown_Master.HistoryDropdownMaster_ID eq Variables.HistoryInfo.SBE_HistoryDropdownMaster_ID>
						&nbsp;#Variables.DropDown_Master.DisplayName#
					</cfif>	
				</cfloop>
			</td>
			<td width="2%" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>						
			<td width="17%" style="#Variables.TextStyle#" align="right"><strong>Abortions/Miscarriages:</strong></td>
			<td colspan="4" style="#Variables.TextStyle#" >&nbsp;#Variables.HistoryInfo.AbortionMiscarriages#</td>	
		</tr>	
		
--->		

		<!--- CASE 10,024 Added cfif to display only if there is data --->
		<?php 
    if($HistoryInfo_row->SBE_HistoryDropdownMaster_ID != 18 || trim($HistoryInfo_row->AbortionMiscarriages) != ""){
      $H1 = "";
      $D1 = "";
      $H2 = "";
      $D2 = "";
      $H3 = "";
      $D3 = "";
      $ItemCounter = 0;   
      
//      <cfif Variables.HistoryInfo.SBE_HistoryDropdownMaster_ID neq 18>
//				<cfset Variables.ItemCounter = Variables.ItemCounter +1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'SBE:'>
//				<cfloop query="Variables.DropDown_Master">
//					<cfif Variables.DropDown_Master.HistoryDropdownMaster_ID eq Variables.HistoryInfo.SBE_HistoryDropdownMaster_ID>
//						<cfset "Variables.D#Variables.ItemCounter#" = Variables.DropDown_Master.DisplayName>
//					</cfif>	
//				</cfloop>
//			</cfif>
//			<cfif Trim(Variables.HistoryInfo.AbortionMiscarriages) neq ''>	
//				<cfset Variables.ItemCounter = Variables.ItemCounter +1 >
//				<cfset "Variables.H#Variables.ItemCounter#" = 'Abortions/Miscarriages:'>
//				<cfset "Variables.D#Variables.ItemCounter#" = Variables.HistoryInfo.AbortionMiscarriages>
//			</cfif>
      
      if($HistoryInfo_row->SBE_HistoryDropdownMaster_ID != 18){
        $ItemCounter = $ItemCounter  + 1;
        $H1 =  'SBE:';  
        
        foreach ($DropDown_Master_result as $dmr){
          if($dmr->HistoryDropdownMaster_ID == $HistoryInfo_row->SBE_HistoryDropdownMaster_ID){
            $D1 = $dmr->DisplayName;
          }
        }
      }
      
      if($HistoryInfo_row->AbortionMiscarriages != ""){
        $ItemCounter = $ItemCounter  + 1;
        $H2 = 'Abortions/Miscarriages:';
        $D2 = $HistoryInfo_row->AbortionMiscarriages;
      }
      
    ?>


			<tr>
				<td width="7">&nbsp;</td>
				<td width="1%" style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong><?php echo $H1; ?></strong></td>
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>"  valign="top" ><?php echo $D1; ?></td>
				<td width="10">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong><?php echo $H2; ?></strong></td>
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>"  valign="top" ><?php echo $D2; ?></td>							
				<td width="10">&nbsp;</td>
				<td width="15%" style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong><?php echo $H3; ?></strong></td>
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>"  valign="top" ><?php echo $D3; ?></td>							
			</tr>		
    <?php
    }
    ?>
		
		<cfif Trim(Variables.HistoryInfo.ObNotes) neq ''>		
    <?php
    if(trim($HistoryInfo_row->ObNotes) != ""){
    ?>
			<!---CASE 10,005 - Added Missing OBG/YN Notes--->
			<tr>
				<td width="7">&nbsp;</td>
				<td width="1%" style="<?php echo $TextStyle; ?>" align="right" valign="top"><strong>Notes:</strong></td>						
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>" colspan="9"  valign="top" ><?php echo $HistoryInfo_row->ObNotes  ?></td>
			</tr>			
    <?php
    }
    
        $sql = "SELECT	Top 1
					LName,
					FName,
					MI
			FROM	" . $user_db . ".dbo.Users
			WHERE	Id = $HistoryInfo_row->ObEditedUserPK";

    $WhoDidIt = $this->ReportModel->data_db->query($sql);
    $WhoDidIt_num = $WhoDidIt->num_rows();
    $WhoDidIt_row = $WhoDidIt->row();
    
    $EditBy = trim($WhoDidIt_row->FName)." ".$WhoDidIt_row->MI." ".trim($WhoDidIt_row->LName);
    ?>

		<tr>
			<td width="7">&nbsp;</td>				
			<td width="1%" style="<?php echo $TextStyle; ?>" align="right" nowrap><strong>Last Edited On:</strong></td>
			<td width="4">&nbsp;</td>
			<td colspan="2" style="<?php echo $TextStyle; ?>" ><?php echo date('m/d/Y', strtotime($HistoryInfo_row->ObEditedOn)); ?></td>
			<td style="<?php echo $TextStyle; ?>" align="right" nowrap><strong>Last Edited By:</strong></td>
			<td width="4">&nbsp;</td>
			<td colspan="5" style="<?php echo $TextStyle; ?>" ><?php echo $EditBy; ?></td>					
		</tr>
	</table>




<?php endif;?>

