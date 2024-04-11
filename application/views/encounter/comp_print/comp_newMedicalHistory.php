<?php
$stop = TRUE;
if (!$stop):
//<!--- 
//	<responsibilities>This is the page for the creation of a new 'Medical History' section of the Chart Notes pages using the data from the new Flex History Module</responsibilities>
//
//	<note author="Chris Hoffman" date="08 March 2011">File: comp_newMedicalHistory.cfm
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
//<cfif Trim(Attributes.UseDetailKeys) NEQ 1>
//	<cfset variables.bUseDetailKeys = false>
//<cfelse>
//	<cfset variables.bUseDetailKeys = true>
//</cfif>

  $ShowField = 0;
  if ($UseDetailKeys != 1) {
    $bUseDetailKeys = FALSE;
  } else {
    $bUseDetailKeys = TRUE;
  }

//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.OrgId=Session.Org_Id>
//	<cfset Variables.sTimeOffset=Session.UTC_TimeOffset>
//	<cfset Variables.sDST=Session.UTC_DST>
//	<cfset Variables.sTimeZoneID=Session.UTC_TimeZoneId>
//</cflock>
//
//<cfset Variables.dataObj = StructNew()>
//<cfset Variables.dataObj.patient_Id = Attributes.PatientKey>
//<cfset Variables.dataObj.orgTimeZoneOffset = Variables.sTimeOffset>
//<cfset Variables.dataObj.orgTimeZoneDST = Variables.sDST>
//<cfset Variables.dataObj.orgTimeZoneId = Variables.sTimeZoneID>	
//<cfset Variables.PertDisplayValue = ''>
//<!---CASE 10,024 --->
//<cfset Variables.ShowField = 0>
//
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

  if (isset($$RefHeader)) { //SKIPP $RefHeader
    $TextStyle = 'font-size: 12px; color: Black; font-weight: normal; font-face: Garamond, Arial, Helvetica;';
  } else {
    $data['data_db'] = $data_db;
    $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
    $TextStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  }

//<cfif variables.bUseDetailKeys eq true>
//	<!--- the Encounter or Referral Locked--->
//	<cfif isDefined('Attributes.Referral')>
//		<!--- This is being called from printreferrals.cfm--->
//		<cfset Variables.dataObj.SearchId = Attributes.KeyValue>
//	<cfelse>
//		<!---If the Encounter is Locked, Get the detail Id--->
//		<cfset Variables.dataObj.RptType = Attributes.HEADERMASTERKEY>
//		<cfset Variables.dataObj.EncounterId = Attributes.PRIMARYKEY>
//		<cfset Variables.SearchIds = CreateObject("component","cfc.history.ChartNotes_History").getSearchIds(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//		<cfoutput query="Variables.SearchIds">
//			<cfset Variables.dataObj.SearchId = #Variables.SearchIds.ComponentKeys#>
//		</cfoutput>	
//	</cfif>
//</cfif>


  if ($bUseDetailKeys) {
    if (isset($Referral)) {//$Referral SKIPP 
      $SearchId = ""; //SKIPP
    } else {
      $sql = "SELECT	E.ComponentKeys
			FROM	" . $data_db . ".dbo.EncounterComponents E
			WHERE	E.Patient_ID = $PatientKey
					AND Encounter_ID = $PrimaryKey
					AND	HeaderMaster_ID = $HeaderMasterKey";
      $SearchIds = $this->ReportModel->data_db->query($sql);
      $SearchIds_num = $SearchIds->num_rows();
      $SearchIds_result = $SearchIds->result();
    }
  }

//<cfif variables.bUseDetailKeys eq false>
//	<!--- Not Locked, We need the FaceSheetNewHistory information query --->
//	<cfset Variables.HistoryInfo = CreateObject("component","cfc.history.FaceSheet_History").FaceSheetNewHistory(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//	<cfset Variables.PertinentInfo = CreateObject("component","cfc.history.FaceSheet_History").FaceSheetNewHistoryPertinents(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//<cfelse>
//	<!--- Locked, We need the ChartNotesHistory information query --->
//	<cfset Variables.HistoryInfo = CreateObject("component","cfc.history.ChartNotes_History").NewMedicalHistory(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//	<cfset Variables.PertinentInfo = CreateObject("component","cfc.history.ChartNotes_History").NewMedicalHistoryPertinents(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//</cfif>
//<cfset Variables.DropDown_Master = CreateObject("component","cfc.history.FaceSheet_History").FaceSheetDropDownMasterInfo(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>

  if ($bUseDetailKeys == FALSE) {
//  UTCtoLocalTZ SKIPP
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
			FROM	
      History H, HistoryMedical_Dtl M, 
      HistorySurgical_Dtl P, 
      HistoryObGyn_Dtl O, 
      HistorySocial_Dtl S, 
      HistoryFamilyNotes_Dtl F, 
      PatientProfile PP
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

    $sql = "SELECT TOP 1 
						H.HistoryMedical_Dtl_ID as Medical,
						H.HistorySurgical_Dtl_ID as Surgical,
						H.HistorySocial_Dtl_ID as Social
			FROM
				History H
			WHERE	
				H.Patient_ID = $PatientKey";

    $GetIds = $this->ReportModel->data_db->query($sql);
    $GetIds_num = $GetIds->num_rows();
    $GetIds_result = $GetIds->result();
    $GetIds_row = $GetIds->row();

    if ($GetIds_row) {
      $sql = "SELECT Type, DisplayName, SmartControlAnswer,SmartControlComment
        FROM (
        SELECT 	SC.SmartControlMaster_ID, SC.Type, SC.DisplayName, 
                      SC.DatePopulated_UTC, SC.Hidden, SC.HiddenBy, 
                      SC.HiddenOn_UTC, A.AnswerID, A.DetailID, 
                      A.Org_ID, A.SmartControlAnswer, isNull(A.SmartControlComment, '') as SmartControlComment
        FROM	HistorySmartControlsMaster SC LEFT OUTER JOIN
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
                WHERE  	(HistorySocial_Dtl_ID = $GetIds_row->Social  )) AS A 
                ON SC.SmartControlMaster_ID = A.SmartControlMaster_ID
        WHERE  	(SC.Hidden <> 1) OR (SC.Hidden IS NULL)
        ) as SelectPertinents
			WHERE DetailId IS NOT NULL
			ORDER BY Type, DisplayName";
      $PertinentInfo = $this->ReportModel->data_db->query($sql);
      $PertinentInfo_num = $PertinentInfo->num_rows();
      $PertinentInfo_result = $PertinentInfo->result();
    }
  } else {
//  SKIPP UTCtoLocalTZ

    $sql = "SELECT
            		M.Notes as MedicalNotes, 
					M.LastEditedBy_Users_PK as MedicalEditedUserPK, 
					M.LastEditedOn_UTC as MedicalEditedOn
			FROM	HistoryMedical_Dtl M
			WHERE	M.HistoryMedical_Dtl_ID = $ComponentKey";

    $HistoryInfo = $this->ReportModel->data_db->query($sql);
    $HistoryInfo_num = $HistoryInfo->num_rows();
    $HistoryInfo_row = $HistoryInfo->row();

    $sql = "SELECT 	SC.SmartControlMaster_ID, SC.Type, SC.DisplayName, 
		          	SC.DatePopulated_UTC, SC.Hidden, SC.HiddenBy, 
		          	SC.HiddenOn_UTC, A.AnswerID, A.DetailID, 
		          	A.Org_ID, A.SmartControlAnswer, isNull(A.SmartControlComment, '') as SmartControlComment
			FROM	HistorySmartControlsMaster SC LEFT OUTER JOIN
				    	(SELECT HistoryMedical_Answer_ID as AnswerID, SmartControlMaster_ID, HistoryMedical_Dtl_ID as DetailID, Org_ID, SmartControlAnswer, isNull(SmartControlComment, '') as SmartControlComment 
					    	FROM HistoryMedical_Answer
					    	WHERE  	HistoryMedical_Dtl_ID = $ComponentKey
						)AS A
	    	ON SC.SmartControlMaster_ID = A.SmartControlMaster_ID
			WHERE  	(SC.Hidden <> 1) OR (SC.Hidden IS NULL)";

    $PertinentInfo = $this->ReportModel->data_db->query($sql);
    $PertinentInfo_num = $PertinentInfo->num_rows();
    $PertinentInfo_result = $PertinentInfo->result();
  }

  $sql = "		SELECT	DisplayName,
					HistoryDropdownMaster_ID
			FROM	HistoryDropdownMaster	";

  $DropDown_Master = $this->ReportModel->data_db->query($sql);
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

//<cfoutput>
//	<cfset Variables.PertinentInfoCount = 0>
//	<cfset Variables.spacer = RepeatString("&nbsp;",5) >
//	<table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">	
//		<cfif IsDefined('Attributes.RefHeader')>
//			<!--- This is being called from printreferrals.cfm and needs a header --->
//			<tr>
//				<td colspan="7" style="font-size: 14px; color: Maroon; font-weight: bold; font-face: Garamond, Arial, Helvetica;">#Attributes.RefHeader#</td>
//			</tr>	
//		</cfif>
//	
//		<!--- CASE 10,024 Added loop to find if there is any data to display & cfif to display the row only if there is data --->
//		<cfloop query="Variables.PertinentInfo">
//			<cfif Variables.PertinentInfo.Type eq 'M' AND Variables.PertinentInfo.SmartControlAnswer eq 'True'>
//				<cfset Variables.ShowField = 1>
//			</cfif>
//		</cfloop>
//		<cfif Variables.ShowField eq 1>
//			<cfset Variables.ShowField = 0>
//			<tr>
//				<td width="7">&nbsp;</td>				
//				<td width="1%" style="#Variables.TextStyle#" align="right" valign="top" nowrap><strong>Pertinent Positives:</strong></td>
//				<td width="4">&nbsp;</td>
//				<td colspan="4" style="#Variables.TextStyle#"  valign="top" >
//					<!---Create a list of all the answers for the Pertinent output to find the last one to change a ',' with an 'and' --->					
//					<cfset Variables.pertlist = ''>
//					<cfloop query="Variables.PertinentInfo">
//						<cfif Variables.PertinentInfo.Type eq 'M' AND Variables.PertinentInfo.SmartControlAnswer eq 'True'>
//							<cfset Variables.PertDisplayValue = ''>
//							<cfif #Trim(Variables.PertinentInfo.SmartControlComment)# neq ''>
//								 <cfset Variables.PertDisplayValue = Variables.PertinentInfo.DisplayName&'&nbsp;('&#Trim(Variables.PertinentInfo.SmartControlComment)#&')'>	
//							<cfelse>
//								<cfset Variables.PertDisplayValue = Variables.PertinentInfo.DisplayName>
//							</cfif>											
//							<cfif Variables.pertlist eq ''>
//								<cfset Variables.pertlist = Variables.PertDisplayValue>
//							<cfelse>				
//								<cfset Variables.pertlist = Variables.pertlist&','&Variables.PertDisplayValue>					
//							</cfif>
//						</cfif>	
//					</cfloop>
//						
//					<cfset Variables.PertinentInfoCount = 0>
//					<cfset variables.ItemOutput = ""> 
//					<cfloop list="#Variables.pertlist#" index="variables.I">
//						<cfif Variables.PertinentInfoCount eq 0>
//							<cfset variables.ItemOutput = variables.ItemOutput & "The patient has a history of " & variables.I>
//							<cfset Variables.PertinentInfoCount = 1>
//						<cfelse>
//							<cfif variables.I eq ListLast(Variables.pertlist)>
//								<cfset variables.ItemOutput = variables.ItemOutput & " and ">
//							<cfelse>
//								<cfset variables.ItemOutput = variables.ItemOutput & ", ">
//							</cfif>	
//							<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//						</cfif>
//					</cfloop>
//					<cfif Variables.PertinentInfoCount eq 0>
//						<cfset variables.ItemOutput = "&nbsp;">
//					<cfelse>
//						<cfset variables.ItemOutput = variables.ItemOutput & ".">
//					</cfif>
//					#variables.ItemOutput#
//				</td>
//			</tr>	
//		</cfif>		
//
//		<!--- CASE 10,024 Added loop to find if there is any data to display & cfif to display the row only if there is data --->
//		<cfloop query="Variables.PertinentInfo">
//			<cfif Variables.PertinentInfo.Type eq 'M' AND Variables.PertinentInfo.SmartControlAnswer eq 'False'>
//				<cfset Variables.ShowField = 1>
//			</cfif>
//		</cfloop>
//		<cfif Variables.ShowField eq 1>
//			<cfset Variables.ShowField = 0>
//			<tr>
//				<td width="7">&nbsp;</td>				
//				<td width="1%" style="#Variables.TextStyle#" align="right" valign="top" nowrap><strong>Pertinent Negatives:</strong></td>						
//				<td width="4">&nbsp;</td>
//				<td colspan="4" style="#Variables.TextStyle#"  valign="top" >
//					<!---Create a list of all the answers for the Pertinent output to find the last one to change a ',' with an 'and' --->					
//					<cfset Variables.pertlist = ''>
//					<cfloop query="Variables.PertinentInfo">
//						<cfif Variables.PertinentInfo.Type eq 'M' AND Variables.PertinentInfo.SmartControlAnswer eq 'False'>
//							<cfset Variables.PertDisplayValue = ''>
//							<cfif #Trim(Variables.PertinentInfo.SmartControlComment)# neq ''>
//								 <cfset Variables.PertDisplayValue = Variables.PertinentInfo.DisplayName&'&nbsp;('&#Trim(Variables.PertinentInfo.SmartControlComment)#&')'>	
//							<cfelse>
//								<cfset Variables.PertDisplayValue = Variables.PertinentInfo.DisplayName>
//							</cfif>											
//							<cfif Variables.pertlist eq ''>
//								<cfset Variables.pertlist = Variables.PertDisplayValue>
//							<cfelse>				
//								<cfset Variables.pertlist = Variables.pertlist&','&Variables.PertDisplayValue>					
//							</cfif>
//						</cfif>	
//					</cfloop>
//						
//					<cfset Variables.PertinentInfoCount = 0>
//					<cfset variables.ItemOutput = ""> 
//					<cfloop list="#Variables.pertlist#" index="variables.I">
//						<cfif Variables.PertinentInfoCount eq 0>
//							<cfset variables.ItemOutput = variables.ItemOutput & "The patient denies any history of " & variables.I>
//							<cfset Variables.PertinentInfoCount = 1>
//						<cfelse>
//							<cfif variables.I eq ListLast(Variables.pertlist)>
//								<cfset variables.ItemOutput = variables.ItemOutput & " and ">
//							<cfelse>
//								<cfset variables.ItemOutput = variables.ItemOutput & ", ">
//							</cfif>	
//							<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//						</cfif>
//					</cfloop>
//					<cfif Variables.PertinentInfoCount eq 0>
//						<cfset variables.ItemOutput = "&nbsp;">
//					<cfelse>
//						<cfset variables.ItemOutput = variables.ItemOutput & ".">
//					</cfif>
//					#variables.ItemOutput#
//				</td>
//			</tr>	
//		</cfif>	
//		
//		<!--- CASE 10,024 Added cfif to display only if there is data --->	
//		<cfif Trim(Variables.HistoryInfo.MedicalNotes) neq ''>
//			<tr>
//				<td width="7">&nbsp;</td>				
//				<td width="1%" style="#Variables.TextStyle#" align="right" valign="top"><strong>Notes:</strong></td>						
//				<td width="4">&nbsp;</td>
//				<td colspan="4" style="#Variables.TextStyle#"  valign="top" >#HTMLEditFormat(Trim(Variables.HistoryInfo.MedicalNotes))#</td>
//			</tr>	
//		</cfif>
//
//		<cfset Variables.dataObj.EditBy = Variables.HistoryInfo.MedicalEditedUserPK>
//		<cfset Variables.WhoDidIt = CreateObject("component","cfc.history.ChartNotes_History").getWhoDidIt(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//		<cfset Variables.EditBy = #Trim(Variables.WhoDidIt.FName)# & ' ' & #Trim(Variables.WhoDidIt.MI)# & ' ' & #Trim(Variables.WhoDidIt.LName)#>
//
//		<tr>
//			<td width="7">&nbsp;</td>				
//			<td width="1%" style="#Variables.TextStyle#" align="right" nowrap><strong>Last Edited On:</strong></td>
//			<td width="4">&nbsp;</td>
//			<td style="#Variables.TextStyle#" >#DateFormat(Variables.HistoryInfo.MedicalEditedOn,'MM/DD/YYYY')#</td>
//			<td style="#Variables.TextStyle#" align="right" nowrap><strong>Last Edited By:</strong></td>
//			<td width="4">&nbsp;</td>
//			<td style="#Variables.TextStyle#" >#Variables.EditBy#</td>					
//		</tr>
//	</table>
//</cfoutput>

  $PertinentInfoCount = 0;
  $spacer = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

  if (isset($PertinentInfo_num) && $PertinentInfo_num > 0) {
    ?>

    <table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">	
      <?php if (isset($RefHeader)) { ?>
        <!--- This is being called from printreferrals.cfm and needs a header --->
        <tr>
          <td colspan="7" style="font-size: 14px; color: Maroon; font-weight: bold; font-face: Garamond, Arial, Helvetica;">#Attributes.RefHeader#</td>
        </tr>	
      <?php } ?>

      <!--- CASE 10,024 Added loop to find if there is any data to display & cfif to display the row only if there is data --->


      <?php
      foreach ($PertinentInfo_result as $pir) {
        if ($pir->Type == 'M' && $pir->SmartControlAnswer == "True") {
          $ShowField = 1;
        }
      }

      if ($ShowField == 1) {
        $ShowField = 0;
        ?>

        <tr>
          <td width="7">&nbsp;</td>				
          <td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top" nowrap><strong>Pertinent Positives:</strong></td>
          <td width="4">&nbsp;</td>
          <td colspan="4" style="<?php echo $TextStyle ?>"  valign="top" >
            <!---Create a list of all the answers for the Pertinent output to find the last one to change a ',' with an 'and' --->					

            <?php
            $pertlist = "";
            foreach ($PertinentInfo_result as $pir) {
              if ($pir->Type == 'M' && $pir->SmartControlAnswer == 'True') {
                $PertDisplayValue = '';
                if ($pir->SmartControlComment != '') {
                  $PertDisplayValue = $pir->DisplayName . "&nbsp;" . trim($pir->SmartControlComment);
                } else {
                  $PertDisplayValue = $pir->DisplayName;
                }

                if ($pertlist == "") {
                  $pertlist[] = $PertDisplayValue;
                } else {
                  $pertlist[] = $pertlist . "," . $PertDisplayValue;
                }
              }
            }

            $PertinentInfoCount = 0;
            $ItemOutput = 0;

            $n = 0;
            foreach ($pertlist as $v) {
              if ($PertinentInfoCount == 0) {
                $ItemOutput = $ItemOutput . "The patient has a history of " . $n + 1;
                $PertinentInfoCount = 1;
              } else {
                if ($pertlist[$n] == $pertlist[sizeof($pertlist) - 1]) {
                  $ItemOutput = $ItemOutput . " and ";
                } else {
                  $ItemOutput = $ItemOutput . ", ";
                }
                $ItemOutput = $ItemOutput . "" . $n + 1;
              }
              $n++;
            }

            if ($PertinentInfoCount == 0) {
              $ItemOutput = "&nbsp;";
            } else {
              $ItemOutput = $ItemOutput . ".";
            }
            echo $ItemOutput;
            ?>
          </td>
        </tr>	
      <?php } ?>

      <!--- CASE 10,024 Added loop to find if there is any data to display & cfif to display the row only if there is data --->
    <!--		<cfloop query="Variables.PertinentInfo">
          <cfif Variables.PertinentInfo.Type eq 'M' AND Variables.PertinentInfo.SmartControlAnswer eq 'False'>
              <cfset Variables.ShowField = 1>
          </cfif>
      </cfloop>-->

      <?php
      foreach ($PertinentInfo_result as $pir) {
        if ($pir->Type == 'M' && $pir->SmartControlAnswer == "False") {
          $ShowField = 1;
        }
      }
      ?>
      <?php
      if ($ShowField == 1) {
        $ShowField = 0;
        ?>
        <tr>
          <td width="7">&nbsp;</td>				
          <td width="1%" style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong>Pertinent Negatives:</strong></td>						
          <td width="4">&nbsp;</td>
          <td colspan="4" style="<?php echo $TextStyle; ?>"  valign="top" >
            <!---Create a list of all the answers for the Pertinent output to find the last one to change a ',' with an 'and' --->					

            <?php
            $pertlist = "";
            foreach ($PertinentInfo_result as $pir) {
              if ($pir->Type == "M" && $pir->SmartControlAnswer == 'False') {
                $PertDisplayValue = '';
                if ($pir->SmartControlComment != "") {
                  $PertDisplayValue = $pir->DisplayName . "&nbsp;" . trim($pir->SmartControlComment);
                } else {
                  $PertDisplayValue = $pir->DisplayName;
                }

                if ($pertlist == "") {
                  $pertlist[] = $PertDisplayValue;
                } else {
                  $pertlist[] = pertlist . "," . $PertDisplayValue;
                }
              }
            }

            $PertinentInfoCount = 0;
            $ItemOutput = 0;

            $n = 0;
            foreach ($pertlist as $v) {
              if ($PertinentInfoCount == 0) {
                $ItemOutput = $ItemOutput . " The patient denies any history of " . $n + 1;
                $PertinentInfoCount = 1;
              } else {
                if ($pertlist[$n] == $pertlist[sizeof($pertlist) - 1]) {
                  $ItemOutput = $ItemOutput . " and ";
                } else {
                  $ItemOutput = $ItemOutput . ", ";
                }
                $ItemOutput = $ItemOutput . "" . $n + 1;
              }
              $n++;
            }
            if ($PertinentInfoCount == 0) {
              $ItemOutput = "&nbsp;";
            } else {
              $ItemOutput = $ItemOutput . ".";
            }
            echo $ItemOutput;
            ?>

          </td>
        </tr>	
        <?php
      }
      ?>

      <!--- CASE 10,024 Added cfif to display only if there is data --->	
      <?php
      if ($HistoryInfo_row->MedicalNotes != "") {
        ?>
        <tr>
          <td width="7">&nbsp;</td>				
          <td width="1%" style="<?php echo $TextStyle ?>" align="right" valign="top"><strong>Notes:</strong></td>						
          <td width="4">&nbsp;</td>
          <td colspan="4" style="<?php echo $TextStyle ?>"  valign="top" ><?php echo trim($HistoryInfo->MedicalNotes); ?></td>
        </tr>	
        <?php
      }

      $sql = "SELECT	Top 1
					LName,
					FName,
					MI
			FROM	" . $user_db . ".dbo.Users
			WHERE	Id = $HistoryInfo_row->MedicalEditedUserPK";

      $WhoDidIt = $this->ReportModel->data_db->query($sql);
      $WhoDidIt_num = $WhoDidIt->num_rows();
      $WhoDidIt_row = $WhoDidIt->row();

      $EditBy = trim($WhoDidIt_row->FName) . " " . $WhoDidIt_row->MI . " " . trim($WhoDidIt_row->LName);
      ?>

      <tr>
        <td width="7">&nbsp;</td>				
        <td width="1%" style="<?php echo $TextStyle; ?>" align="right" nowrap><strong>Last Edited On:</strong></td>
        <td width="4">&nbsp;</td>
        <td style="<?php echo $TextStyle; ?>" ><?php echo date('m/d/Y', strtotime($HistoryInfo_row->MedicalEditedOn)); ?> </td>
        <td style="<?php echo $TextStyle; ?>" align="right" nowrap><strong>Last Edited By:</strong></td>
        <td width="4">&nbsp;</td>
        <td style="<?php echo $TextStyle; ?>" ><?php echo $EditBy; ?></td>					
      </tr>
    </table>

    <?php }
  ?>

<?php endif; ?>