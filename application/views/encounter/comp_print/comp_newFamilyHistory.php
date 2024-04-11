<?php
$stop = TRUE;
if(!$stop):
$dt = $this->EncounterHistoryModel->get_by_id($PrimaryKey)->row();
$Org_ID = $dt->Org_ID;
//<!--- 
//	<responsibilities>This is the page for the creation of a new 'Family History' section of the Chart Notes pages using the data from the new Flex History Module</responsibilities>
//
//	<note author="Chris Hoffman" date="08 March 2011">File: comp_newFamilyHistory.cfm
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
//<cfelse>	
//	<!--- the Encounter or Referral are NOT Locked--->
//		<cfset Variables.Ids = CreateObject("component","cfc.history.ChartNotes_History").CreateFinalOutput(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//		<cfloop query="Variables.Ids">
//			<cfif Variables.Ids.Type eq 134>
//				<cfset Variables.dataObj.SearchId = #Variables.Ids.ID#>
//			</cfif>	
//		</cfloop>
//</cfif>

  if($bUseDetailKeys == TRUE){
   if(isset($Referral)){ //SKIPP
     $SearchId = 0; 
   }else{
     $sql = "			SELECT	E.ComponentKeys
			FROM	" . $data_db . ".dbo.EncounterComponents E
			WHERE	E.Patient_ID = $PatientKey
					AND Encounter_ID = $PrimaryKey
					AND	HeaderMaster_ID = $HeaderMasterKey";
     
      $SearchIds = $this->ReportModel->data_db->query($sql);
      $SearchIds_num = $SearchIds->num_rows();
      $SearchIds_row = $SearchIds->row();
      
      $SearchId = $SearchIds_row->ComponentKeys;
   }
  }else{
    //default
    $SearchId = 0;
    
    //$SearchId not compeleted
    
//     SKIPP  UTCtoLocalTZ
     $sql_NewHistoryIDs = "SELECT	TOP	1
				H.History_Hist_ID, 
				H.HistoryReviewed_ID,
				H.HistoryMedical_Dtl_ID,
				M.LastEditedOn_UTC as MedicalEditedOn,
				H.HistorySurgical_Dtl_ID,
				P.LastEditedOn_UTC as ProceduralEditedOn,
				H.HistorySocial_Dtl_ID,
				S.LastEditedOn_UTC as SocialEditedOn,
				H.HistoryFamilyNotes_Dtl_ID,
				F.LastEditedOn_UTC as FamilyNotesEditedOn,
				H.HistoryObGyn_Dtl_ID,
				O.LastEditedOn_UTC as ObEditedOn				
			FROM	" . $data_db . ".dbo.History H, 
        " . $data_db . ".dbo.HistoryMedical_Dtl M, 
        " . $data_db . ".dbo.HistorySurgical_Dtl P, 
        " . $data_db . ".dbo.HistoryObGyn_Dtl O, 
        " . $data_db . ".dbo.HistorySocial_Dtl S, 
        " . $data_db . ".dbo.HistoryFamilyNotes_Dtl F, 
        " . $data_db . ".dbo.PatientProfile PP
			WHERE
				(H.Patient_ID = $PatientKey)
				AND (H.HistoryMedical_Dtl_ID = M.HistoryMedical_Dtl_Id)
				AND (H.HistorySurgical_Dtl_ID = P.HistorySurgical_Dtl_Id) 
				AND (H.HistoryObGyn_Dtl_ID = O.HistoryObGyn_Dtl_Id)
				AND (H.HistorySocial_Dtl_ID = S.HistorySocial_Dtl_Id) 
				AND (H.HistoryFamilyNotes_Dtl_ID = F.HistoryFamilyNotes_Dtl_ID)
				AND PP.Patient_ID = H.Patient_ID";  
      $NewHistoryIDs = $this->ReportModel->data_db->query($sql_NewHistoryIDs);
      $NewHistoryIDs_num = $NewHistoryIDs->num_rows();
      $NewHistoryIDs_row = $NewHistoryIDs->row();
      
      
      
      //     SKIPP  UTCtoLocalTZ
      $sql_HistoryReviewedDate = "SELECT		TOP 1 
						HistoryReviewed.ReviewedOn_utc as ReviewedOn,				
						HistoryReviewed.HistoryReviewed_ID				
			FROM		" . $data_db . ".dbo.HistoryReviewed		
			WHERE		HistoryReviewed.patient_id =  $PatientKey
			ORDER BY	HistoryReviewed.reviewedon_utc desc";
      $HistoryReviewedDate = $this->ReportModel->data_db->query($sql_NewHistoryIDs);
      $HistoryReviewedDate_num = $HistoryReviewedDate->num_rows();
      $HistoryReviewedDate_row = $HistoryReviewedDate->row();
    
      $sql_PatientSex = "SELECT		TOP 1 
						PatientProfile.Sex
			FROM		" . $data_db . ".dbo.PatientProfile
			WHERE		PatientProfile.patient_id = $PatientKey";
      
      $PatientSex = $this->ReportModel->data_db->query($sql_PatientSex);
      $PatientSex_num = $PatientSex->num_rows();
      $PatientSex_row = $PatientSex->row();      
     // var_dump($PatientSex_row);
      if($PatientSex_row->Sex != "M"){
//          SKIPP UTCtoLocalTZ
          $sql_Pregnancy = "SELECT	TOP 1 		
						HPD.LastEditedOn_UTC as PregEditOn,				
						HP.HistoryPregnancy_ID
				FROM	" . $data_db . ".dbo.HistoryPregnancy HP,	
						" . $data_db . ".dbo.HistoryPregnancy_Dtl HPD	
				WHERE	HP.Patient_ID = $PatientKey
						AND HP.HistoryPregnancy_Dtl_ID = HPD.HistoryPregnancy_Dtl_ID";
            $Pregnancy = $this->ReportModel->data_db->query($sql_NewHistoryIDs);
            $Pregnancy_num = $Pregnancy->num_rows();
            $Pregnancy_row = $Pregnancy->row(); 
      }
//      <cfset NewHistoryInfo = QueryNew("Date,Description,Type,id")> SKIPP
      
      if($NewHistoryIDs_num != 0){
//        <!--- History Reviewed  --->
//        <cfset QueryAddRow(NewHistoryInfo)>
//				<cfset QuerySetCell(NewHistoryInfo,"Date",HistoryReviewedDate.ReviewedOn)>
//				<cfset QuerySetCell(NewHistoryInfo,"Description","History Reviewed")>
//				<cfset QuerySetCell(NewHistoryInfo,"Type","131")>
//				<cfif isDefined('Arguments.dataObj.UseHistoryKey')>
//					<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.History_Hist_ID)>
//				<cfelse>
//					<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.HistoryReviewed_ID)>			
//				</cfif>
        
//        <!--- Medical  --->	
//			  <cfset QueryAddRow(NewHistoryInfo)>
//				<cfset QuerySetCell(NewHistoryInfo,"Date",NewHistoryIDs.MedicalEditedOn)>
//				<cfset QuerySetCell(NewHistoryInfo,"Description","Medical History")>
//				<cfset QuerySetCell(NewHistoryInfo,"Type","132")>
//				<cfif isDefined('Arguments.dataObj.UseHistoryKey')>
//					<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.History_Hist_ID)>
//				<cfelse>				
//					<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.HistoryMedical_Dtl_ID)>
//				</cfif>
        
//        <!--- Procedural  --->	
//			  <cfset QueryAddRow(NewHistoryInfo)>
//				<cfset QuerySetCell(NewHistoryInfo,"Date",NewHistoryIDs.ProceduralEditedOn)>
//				<cfset QuerySetCell(NewHistoryInfo,"Description","Procedural History")>
//				<cfset QuerySetCell(NewHistoryInfo,"Type","133")>
//				<cfif isDefined('Arguments.dataObj.UseHistoryKey')>
//					<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.History_Hist_ID)>
//				<cfelse>				
//					<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.HistorySurgical_Dtl_ID)>
//				</cfif>
        
//        <!---  Family --->	
//        <cfset QueryAddRow(NewHistoryInfo)>
//				<cfset QuerySetCell(NewHistoryInfo,"Date",NewHistoryIDs.FamilyNotesEditedOn)>
//				<cfset QuerySetCell(NewHistoryInfo,"Description","Family History")>
//				<cfset QuerySetCell(NewHistoryInfo,"Type","134")>
//				<cfif isDefined('Arguments.dataObj.UseHistoryKey')>
//					<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.History_Hist_ID)>
//				<cfelse>				
//					<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.HistoryFamilyNotes_Dtl_ID)>
//				</cfif>
        
        //hook the logic
        if(isset($UseHistoryKey) ){
          $SearchId = $NewHistoryIDs_row->History_Hist_ID;
        }else{
          $SearchId = $NewHistoryIDs_row->HistoryFamilyNotes_Dtl_ID;
        }
        
        
//        <cfif PatientSex.Sex neq "M">
//				<!--- ObGyn  --->				
//				<cfset QueryAddRow(NewHistoryInfo)>
//					<cfset QuerySetCell(NewHistoryInfo,"Date",NewHistoryIDs.ObEditedOn)>
//					<cfset QuerySetCell(NewHistoryInfo,"Description","Ob\Gyn History")>
//					<cfset QuerySetCell(NewHistoryInfo,"Type","135")>
//					<cfif isDefined('Arguments.dataObj.UseHistoryKey')>
//						<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.History_Hist_ID)>
//					<cfelse>					
//						<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.HistoryObGyn_Dtl_ID)>
//					</cfif>
//					
//				<cfif Pregnancy.RecordCount neq 0>
//					<!--- Pregnancy  --->				
//					<cfset QueryAddRow(NewHistoryInfo)>
//						<cfset QuerySetCell(NewHistoryInfo,"Date",Pregnancy.PregEditOn)>
//						<cfset QuerySetCell(NewHistoryInfo,"Description","Pregnancy History")>
//						<cfset QuerySetCell(NewHistoryInfo,"Type","136")>
//						<cfif isDefined('Arguments.dataObj.UseHistoryKey')>
//							<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.History_Hist_ID)>
//						<cfelse>						
//							<cfset QuerySetCell(NewHistoryInfo,"id",Pregnancy.HistoryPregnancy_ID)>
//						</cfif>	
//				</cfif>
//			</cfif>
//			<!--- Social  --->	
//			<cfset QueryAddRow(NewHistoryInfo)>
//				<cfset QuerySetCell(NewHistoryInfo,"Date",NewHistoryIDs.SocialEditedOn)>
//				<cfset QuerySetCell(NewHistoryInfo,"Description","Social History")>
//				<cfset QuerySetCell(NewHistoryInfo,"Type","137")>
//				<cfif isDefined('Arguments.dataObj.UseHistoryKey')>
//					<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.History_Hist_ID)>
//				<cfelse>				
//					<cfset QuerySetCell(NewHistoryInfo,"id",NewHistoryIDs.HistorySocial_Dtl_ID)>
//				</cfif>
      }
  }

//<cfset Variables.PertinentInfo = CreateObject("component","cfc.history.History").getHistoryFamilyRecords(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.OrgId,Variables.dataObj)> 
//<cfset Variables.FamilyNotesInfo = CreateObject("component","cfc.history.ChartNotes_History").getHistoryFamilyNotes(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//

  
$sql_HistoryConfigSetup = "Select TOP 1
		       HistoryConfigSetup_ID
		  From HistoryConfigSetup
		 Where Org_Id=$Org_ID";

$HistoryConfigSetup = $this->ReportModel->data_db->query($sql_HistoryConfigSetup);
$HistoryConfigSetup_num = $HistoryConfigSetup->num_rows();
$HistoryConfigSetup_result = $HistoryConfigSetup->result();

//SKIPP UTCtoLocalTZ IsDSTActiveTZ
//SKIPP (SELECT TOP 1 CASE WHEN <cfqueryparam cfsqltype="CF_SQL_BIT" value="#Arguments.dataObj.orgTimeZoneDST#"> = 1 THEN CASE WHEN dbo.IsDSTActiveTZ(D.LastEditedOn_UTC,<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Arguments.dataObj.orgTimeZoneId#">) = 1 THEN tz.tzAbbrDaylight ELSE tz.tzAbbrStandard END ELSE tz.tzAbbrStandard END FROM TimeZone tz where tz.timezone_id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Arguments.dataObj.orgTimeZoneId#">) as LastEditDateTZAbbr,

//$PatientKey = 2592105;  //EMBED
$sql_SelectHistoryDetailRecords = "SELECT H.HistoryFamily_ID, 
				H.Org_ID,  
				H.Patient_ID,  
				H.HistoryFamilyMember_Dtl_ID,  
				H.DatePopulated_UTC,  
				D.HistoryFamilyMember_Dtl_ID,  
				D.HistoryFamily_ID, D.Org_ID,  
				D.Relationship_HistoryDropdownMaster_ID, 
				D.Status_HistoryDropdownMaster_ID,  
				D.CauseOfDeath,  
				D.AgeatDeath_HistoryDropdownMaster_ID,  
				D.Notes,  
				D.LastEditedBy_Users_PK,  
				D.LastEditedOn_UTC,  
				D.Hidden, 
				(Select top 1 CASE WHEN isNull(U.credentials,'') = '' THEN U.fname + ' ' + U.lname 
					   ELSE U.fname + ' ' + U.lname + ', ' + U.credentials 
				  END 
				 From " . $user_db . ".dbo.Users U 
				 Where U.ID=D.LastEditedBy_Users_PK) as DisplayLastEditedBy,
				 D.LastEditedOn_UTC as LastEditDate,
				(SELECT TOP 1 '1989/10/13' from TimeZone)  as LastEditDateTZAbbr,
				(SELECT TOP 1 DisplayName FROM HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.Relationship_HistoryDropdownMaster_ID) as DisplayRelationship,
				(SELECT TOP 1 DisplayName FROM HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.Status_HistoryDropdownMaster_ID) as DisplayStatus,
				(SELECT TOP 1 DisplayName FROM HistoryDropdownMaster WHERE HistoryDropdownMaster_ID = D.AgeatDeath_HistoryDropdownMaster_ID) as DisplayAgeatDeath
			FROM      HistoryFamily H, HistoryFamilyMember_Dtl D
			WHERE     (H.Patient_ID = $PatientKey) AND (H.HistoryFamilyMember_Dtl_ID = D.HistoryFamilyMember_Dtl_ID)
					AND ((D.Hidden <> 1) OR (D.Hidden IS NULL))";

$DetailRecords = $this->ReportModel->data_db->query($sql_SelectHistoryDetailRecords);
$DetailRecords_num = $DetailRecords->num_rows();
$DetailRecords_result = $DetailRecords->result();
$MemberDetailIds = "";
foreach ($DetailRecords_result as $b){
  $MemberDetailIds = $MemberDetailIds.$b->HistoryFamilyMember_Dtl_ID." ";
}
$MemberDetailIds = str_replace(" ", ",", trim($MemberDetailIds));

if($MemberDetailIds != ""){
$sql_SelectSmartControls = "SELECT 	SC.SmartControlMaster_ID, SC.Type, SC.DisplayName, 
				          	SC.DatePopulated_UTC, SC.Hidden, SC.HiddenBy, 
				          	SC.HiddenOn_UTC, A.AnswerID, A.DetailID, 
				          	A.Org_ID, A.SmartControlAnswer, isNull(A.SmartControlComment, '') as SmartControlComment
				      FROM	HistorySmartControlsMaster SC LEFT OUTER JOIN
					    	(SELECT HistoryFamilyMember_Answer_ID as AnswerID, SmartControlMaster_ID, HistoryFamilyMember_Dtl_ID as DetailID, Org_ID, SmartControlAnswer, isNull(SmartControlComment, '') as SmartControlComment 
					    	FROM HistoryFamilyMember_Answer
					    	WHERE  	(HistoryFamilyMember_Dtl_ID in ($MemberDetailIds))) AS A 
					    	ON SC.SmartControlMaster_ID = A.SmartControlMaster_ID
					LEFT OUTER JOIN 
					(SELECT C.SmartControlMaster_ID, C.SortOrder
		                     FROM HistoryConfigSetup C
							 JOIN HistorySmartControlsMaster SM 
						       ON C.SmartControlMaster_ID=SM.SmartControlMaster_ID
		                    WHERE (Org_Id=$Org_ID)
							  AND (SM.Type= 'F')) AS C
							   ON SC.SmartControlMaster_ID = C.SmartControlMaster_ID
				     WHERE (SC.Hidden <> 1 OR SC.Hidden IS NULL) 
					   AND (SC.Type= 'F') ";
 if($HistoryConfigSetup_num != 0){
   $sql_SelectSmartControls .= " AND (SC.SmartControlMaster_ID IN (A.SmartControlMaster_ID))
					  OR (SC.SmartControlMaster_ID IN (C.SmartControlMaster_ID))
					  Order By C.SortOrder ";
 }else{
   $sql_SelectSmartControls .= " Order By SC.DisplayName";
 }

$SmartControls = $this->ReportModel->data_db->query($sql_SelectSmartControls);
$SmartControls_num = $SmartControls->num_rows();
$SmartControls_result = $SmartControls->result();
    
}
//SKIPP UTCtoLocalTZ
$sql_FamilyNotesInfo = "SELECT	F.Notes as FamilyNotes,
					F.LastEditedBy_Users_PK  as FamilyNotesEditedUserPK, 
					F.LastEditedOn_UTC as FamilyNotesEditedOn
			FROM  	HistoryFamilyNotes_Dtl F
			WHERE	HistoryFamilyNotes_Dtl_Id = $SearchId";

$FamilyNotesInfo = $this->ReportModel->data_db->query($sql_FamilyNotesInfo);
$FamilyNotesInfo_num = $FamilyNotesInfo->num_rows();
$FamilyNotesInfo_row = $FamilyNotesInfo->row();

  
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
echo "<p />";
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


if($DetailRecords_num != 0){
?>
		<table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">
      <?php
      if(isset($RefHeader)){
      ?>
				<!--- This is being called from printreferrals.cfm and needs a header --->
				<tr>
					<td colspan="7" style="font-size: 14px; color: Maroon; font-weight: bold; font-face: Garamond, Arial, Helvetica;"><?php echo $RefHeader; ?></td>
				</tr>	
      <?php
      }
//			<!--- CASE 10,024 Added cfif to display only if there is data --->
      if($FamilyNotesInfo_row->FamilyNotes != ""){
      ?>
				<tr>
					<td width="7">&nbsp;</td>				
					<td style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong>Notes:</strong></td>
					<td width="4">&nbsp;</td>
					<td colspan="4" style="<?php echo $TextStyle; ?>"  valign="top" ><?php echo $FamilyNotesInfo_row->FamilyNotes; ?></td>				
				</tr>
			<?php
      }
      ?>
			<tr>
				<td width="7">&nbsp;</td>				
				<td>&nbsp;</td>				
				<td width="4">&nbsp;</td>
				<td colspan="4" width="16%" style="<?php echo $TextStyle; ?>" align="left" nowrap><strong>No Family History data</strong></td>					
			</tr>
			
			
			<?php
//      <cfset Variables.dataObj.EditBy = Variables.FamilyNotesInfo.FamilyNotesEditedUserPK>
//			<cfset Variables.WhoDidIt = CreateObject("component","cfc.history.ChartNotes_History").getWhoDidIt(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//			<cfset Variables.EditBy = #Trim(Variables.WhoDidIt.FName)# & ' ' & #Trim(Variables.WhoDidIt.MI)# & ' ' & #Trim(Variables.WhoDidIt.LName)#>
          $sql = "SELECT	Top 1
					LName,
					FName,
					MI
			FROM	" . $user_db . ".dbo.Users
			WHERE	Id = $FamilyNotesInfo_row->FamilyNotesEditedUserPK";
          

    $WhoDidIt = $this->ReportModel->data_db->query($sql);
    $WhoDidIt_num = $WhoDidIt->num_rows();
    $WhoDidIt_row = $WhoDidIt->row();
    
    $EditBy = trim($WhoDidIt_row->FName)." ".$WhoDidIt_row->MI." ".trim($WhoDidIt_row->LName);
      ?>
      
        
      <tr>
				<td width="7">&nbsp;</td>				
				<td width="1%" style="<?php echo $TextStyle; ?>" align="right" nowrap><strong>Last Edited On:</strong></td>
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>" align="left" nowrap><?php echo date('m/d/Y', strtotime($FamilyNotesInfo_row->FamilyNotesEditedOn)); ?></td>
				<td style="<?php echo $TextStyle; ?>" align="right" nowrap><strong>Last Edited By:</strong></td>
				<td width="4">&nbsp;</td>
				<td style="<?php echo $TextStyle; ?>" align="left"><?php echo $EditBy; ?></td>					
			</tr>	
		</table>
		<p />
  <?php
}else{
  ?>
	
		
			<table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">
        <?php
        if(isset($RefHeader)){
        ?>
					<!--- This is being called from printreferrals.cfm and needs a header --->
					<tr>
						<td colspan="7" style="font-size: 14px; color: Maroon; font-weight: bold; font-face: Garamond, Arial, Helvetica;"><?php echo $RefHeader; ?></td>
					</tr>	
        <?php
        }
        
        if(!empty($FamilyNotesInfo_row->FamilyNotes)){
        ?>
				<!--- CASE 10,024 Added cfif to display only if there is data --->	
					<tr>
						<td width="7">&nbsp;</td>				
						<td width="1%" style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong>Notes:</strong></td>
						<td width="4">&nbsp;</td>
						<td colspan="4" style="<?php echo $TextStyle; ?>"  valign="top" ><?php echo $FamilyNotesInfo_row->FamilyNotes; ?></td>				
					</tr>
          
				
					
					<?php
//      <cfset Variables.dataObj.EditBy = Variables.FamilyNotesInfo.FamilyNotesEditedUserPK>
//			<cfset Variables.WhoDidIt = CreateObject("component","cfc.history.ChartNotes_History").getWhoDidIt(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//			<cfset Variables.EditBy = #Trim(Variables.WhoDidIt.FName)# & ' ' & #Trim(Variables.WhoDidIt.MI)# & ' ' & #Trim(Variables.WhoDidIt.LName)#>
          $sql = "SELECT	Top 1
                LName,
                FName,
                MI
            FROM	" . $user_db . ".dbo.Users
            WHERE	Id = $FamilyNotesInfo_row->FamilyNotesEditedUserPK";

          $WhoDidIt = $this->ReportModel->data_db->query($sql);
          $WhoDidIt_num = $WhoDidIt->num_rows();
          $WhoDidIt_row = $WhoDidIt->row();

          $EditBy = trim($WhoDidIt_row->FName)." ".$WhoDidIt_row->MI." ".trim($WhoDidIt_row->LName);
          ?>
            
          <tr>
						<td width="7">&nbsp;</td>				
						<td style="<?php echo $TextStyle; ?>" align="right" nowrap><strong>Last Edited On:</strong></td>
						<td width="4">&nbsp;</td>
						<td style="<?php echo $TextStyle; ?>" nowrap><?php echo date('m/d/Y', strtotime($FamilyNotesInfo_row->FamilyNotesEditedOn)); ?></td>
						<td style="<?php echo $TextStyle; ?>" align="right" nowrap><strong>Last Edited By:</strong></td>
						<td width="4">&nbsp;</td>
						<td style="<?php echo $TextStyle; ?>" ><?php echo $$EditBy ?></td>					
					</tr>
					<tr><td colspan="7">&nbsp;</td></tr>
        <?php
        }
        ?>
			</table>
		
    <?php
        foreach ($DetailRecords_result as $drc){
    ?>
			<table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">
			
				<tr>
					<td width="7">&nbsp;</td>				
					<td width="1%" style="<?php echo $TextStyle; ?>" align="right" ><strong>Relationship:</strong></td>
					<td width="4">&nbsp;</td>
					<td style="<?php echo $TextStyle; ?>" ><?php echo $drc->DisplayRelationship; ?> </td>
					<!---CASE 10,024 Only display the row if the variable has data--->
          <?php
          if($drc->DISPLAYSTATUS != 'Not Asked' && $drc->DISPLAYSTATUS != ""){
          ?>
						<td style="<?php echo $TextStyle; ?>" align="right" ><strong>Status:</strong></td>
						<td width="4">&nbsp;</td>
						<td style="<?php echo $TextStyle; ?>" ><?php echo $drc->DISPLAYSTATUS; ?></td>
					<?php
          }else{
          ?>
						<td style="<?php echo $TextStyle; ?>" align="right" >&nbsp;</td>
						<td width="4">&nbsp;</td>
						<td style="<?php echo $TextStyle; ?>" >&nbsp;</td>
          <?php
          }
          ?>
				</tr>
<!---			Original:
				<tr>
						<td  width="17%" style="#Variables.TextStyle#" align="right" >
								<strong>Cause of Death:</strong>
						</td>
						<td  width="32%" style="#Variables.TextStyle#" >&nbsp;
								#Variables.PertinentInfo.DetailRecords.CAUSEOFDEATH#
						</td>
						<td  width="16%" style="#Variables.TextStyle#" align="right" >
								<strong>Age at Death:</strong>
						</td>
						<td  width="33%" style="#Variables.TextStyle#" >&nbsp;
								#Variables.PertinentInfo.DetailRecords.DISPLAYAGEATDEATH#
						</td>
					</tr>--->				
				<!---CASE 10,024 Only display the row if either of the variables have data--->
        <?php
        if(trim($drc->CAUSEOFDEATH) != "" || trim($drc->DISPLAYAGEATDEATH) != ""){
          
//          <cfset Variables.H1 = ''>
//					<cfset Variables.D1 = ''>
//					<cfset Variables.H2 = ''>
//					<cfset Variables.D2 = ''>
//
//					<cfif Trim(Variables.PertinentInfo.DetailRecords.CAUSEOFDEATH) neq ''>
//						<cfset Variables.H1 = 'Cause of Death:'>
//						<cfset Variables.D1 = Trim(Variables.PertinentInfo.DetailRecords.CAUSEOFDEATH)>				
//					</cfif>
//
//					<cfif Trim(Variables.PertinentInfo.DetailRecords.DISPLAYAGEATDEATH) neq ''>
//						<cfif Variables.H1 eq ''>
//							<cfset Variables.H1 = 'Age at Death:'>
//							<cfset Variables.D1 = Trim(Variables.PertinentInfo.DetailRecords.DISPLAYAGEATDEATH)>
//						<cfelse>
//							<cfset Variables.H2 = 'Age at Death:'>
//							<cfset Variables.D2 = Trim(Variables.PertinentInfo.DetailRecords.DISPLAYAGEATDEATH)>
//						</cfif>
//					</cfif>	
          $H1 = "";
          $D1 = "";
          $H2 = "";
          $D2 = "";
          
          if(trim($drc->CAUSEOFDEATH) != ""){
            $H1= "Cause of Death:";
            $D1 = trim($drc->CAUSEOFDEATH);
          }
          if(trim($drc->DISPLAYAGEATDEATH) != ""){
            $H1= "Age at Death:";
            $D1 = trim($drc->DISPLAYAGEATDEATH);
          }else{
            $H2= "Age at Death:";
            $D2 = trim($drc->DISPLAYAGEATDEATH);
          }
        ?>
					<tr>
						<td width="7">&nbsp;</td>				
						<td style="<?php echo $TextStyle; ?>" align="right" valign="top" ><strong><?php echo $H1; ?></strong></td>
						<td width="4">&nbsp;</td>
						<td style="<?php echo $TextStyle; ?>" valign="top" ><?php echo $D1; ?></td>
						<td style="<?php echo $TextStyle; ?>" align="right" valign="top" ><strong><?php echo $H2; ?></strong></td>
						<td width="4">&nbsp;</td>
						<td style="<?php echo $TextStyle; ?>" valign="top" ><?php echo $D2; ?></td>							
					</tr>		
        <?php
        }
        ?>
				<!--- CASE 10,024 Added cfif to display only if there is data --->
        <?php
        if($drc->NOTES != ""){
        ?>
					<tr>
						<td width="7">&nbsp;</td>				
						<td style="<?php echo $TextStyle; ?>" align="right" valign="top" ><strong>Comments:</strong></td>
						<td width="4">&nbsp;</td>
						<td colspan="4" style="<?php echo $TextStyle; ?>"  valign="top" ><?php echo $drc->NOTES; ?></td>
					</tr>
        <?php
        }
        
//        <!--- CASE 10,024 Added loop to find if there is any data to display & cfif to display the row only if there is data --->
//				<cfloop query="Variables.PertinentInfo.SmartControls">
//					<cfif Variables.PertinentInfo.SMARTCONTROLS.SMARTCONTROLANSWER eq '1' AND Variables.PertinentInfo.DetailRecords.HISTORYFAMILYMEMBER_DTL_ID eq Variables.PertinentInfo.SmartControls.DETAILID>
//						<cfset Variables.ShowField = 1>
//					</cfif>
//				</cfloop>
        foreach ($SmartControls_result as $sr){
          if($sr->SMARTCONTROLANSWER == 1 && $drc->HISTORYFAMILYMEMBER_DTL_ID == $sr->DETAILID){
            $ShowField = 1;
          }
        }
        
        if($ShowField == 1){
          $ShowField = 0;
        ?>
					<tr>
						<td width="7">&nbsp;</td>				
						<td width="1%" style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong>Pertinent Positives:</strong></td>
						<td width="4">&nbsp;</td>
						<td colspan="4" style="<?php echo $TextStyle; ?>"  valign="top" >								
							<!---Create a list of all the answers for the Pertinent output to find the last one to change a ',' with an 'and' --->
							
              <?php
//              <cfset Variables.pertlist = ''>
//							<cfloop query="Variables.PertinentInfo.SmartControls">
//								<cfif Variables.PertinentInfo.SMARTCONTROLS.SMARTCONTROLANSWER eq '1' AND Variables.PertinentInfo.DetailRecords.HISTORYFAMILYMEMBER_DTL_ID eq Variables.PertinentInfo.SmartControls.DETAILID>
//									<cfset Variables.PertDisplayValue = ''>
//									<cfif #Trim(Variables.PertinentInfo.SMARTCONTROLS.SmartControlComment)# neq ''>
//										 <cfset Variables.PertDisplayValue = Variables.PertinentInfo.SMARTCONTROLS.DisplayName&'&nbsp;('&#Trim(Variables.PertinentInfo.SMARTCONTROLS.SmartControlComment)#&')'>	
//									<cfelse>
//										<cfset Variables.PertDisplayValue = Variables.PertinentInfo.SMARTCONTROLS.DisplayName>
//									</cfif>		
//									<cfif Variables.pertlist eq ''>
//										<cfset Variables.pertlist = Variables.PertDisplayValue>
//									<cfelse>				
//										<cfset Variables.pertlist = Variables.pertlist&','&Variables.PertDisplayValue>					
//									</cfif>
//								</cfif>	
//							</cfloop>
              $pertlist = array();
              foreach ($SmartControls_result as $scr){
                if($scr->SMARTCONTROLANSWER == 1 && $drc->HISTORYFAMILYMEMBER_DTL_ID == $scr->DETAILID){
                  $PertDisplayValue = $scr->DisplayName." &nbsp; (".$src->SmartControlComment.")";
                }else{
                  $PertDisplayValue = $scr->DisplayName;
                }
                
                if($pertlist != NULL){
                  $pertlist[] = $PertDisplayValue;
                }else{
                  $pertlist[] = $pertlist.", ".$PertDisplayValue;
                }
              }
              
//              <cfset Variables.PertinentInfoCount = 0>
//							<cfset variables.ItemOutput = ""> 
//							<cfloop list="#Variables.pertlist#" index="variables.I">
//								<cfif Variables.PertinentInfoCount eq 0>
//									<cfset variables.ItemOutput = variables.ItemOutput & "The patient has a family history of " & variables.I>
//									<cfset Variables.PertinentInfoCount = 1>
//								<cfelse>
//									<cfif variables.I eq ListLast(Variables.pertlist)>
//										<cfset variables.ItemOutput = variables.ItemOutput & " and ">
//									<cfelse>
//										<cfset variables.ItemOutput = variables.ItemOutput & ", ">
//									</cfif>
//									<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//								</cfif>
//							</cfloop>
//							<cfif Variables.PertinentInfoCount eq 0>
//								<cfset variables.ItemOutput = "&nbsp;">
//							<cfelse>
//								<cfset variables.ItemOutput = variables.ItemOutput & ".">
//							</cfif>
//							#variables.ItemOutput#
              
              $PertinentInfoCount = 0;
              $ItemOutput = "";
              
              foreach ($pertlist as $v){
                if($PertinentInfoCount == 0){
                  $ItemOutput = $ItemOutput."The patient has a family history of ".$v;
                  $PertinentInfoCount = 1;
                }else{
                   if($v == $pertlist[sizeof($pertlist)-1]){
                     $ItemOutput = $ItemOutput." and ";
                   }else{
                     $ItemOutput = $ItemOutput.", ";
                   }
                   $ItemOutput = $ItemOutput. $v;
                }
              }
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

//        <!--- CASE 10,024 Added loop to find if there is any data to display & cfif to display the row only if there is data --->
//				<cfloop query="Variables.PertinentInfo.SmartControls">
//					<cfif Variables.PertinentInfo.SMARTCONTROLS.SMARTCONTROLANSWER eq '0' AND Variables.PertinentInfo.DetailRecords.HISTORYFAMILYMEMBER_DTL_ID eq Variables.PertinentInfo.SmartControls.DETAILID>
//						<cfset Variables.ShowField = 1>
//					</cfif>
//				</cfloop>
                
        foreach ($SmartControls_result as $sr){
          if($sr->SMARTCONTROLANSWER == 0 && $drc->HISTORYFAMILYMEMBER_DTL_ID == $sr->DETAILID){
            $ShowField = 1;
          }
        }
        
        if($ShowField == 1){
          $ShowField = 0;
        ?>
					<tr>
						<td width="7">&nbsp;</td>				
						<td width="1%" style="<?php echo $TextStyle; ?>" align="right" valign="top" nowrap><strong>Pertinent Negatives:</strong></td>	
						<td width="4">&nbsp;</td>
						<td colspan="4" style="<?php echo $TextStyle; ?>"  valign="top" >		
							<!---Create a list of all the answers for the Pertinent output to find the last one to change a ',' with an 'and' --->
							<cfset Variables.pertlist = ''>
							<cfloop query="Variables.PertinentInfo.SmartControls">
								<cfif Variables.PertinentInfo.SMARTCONTROLS.SMARTCONTROLANSWER eq '0' AND Variables.PertinentInfo.DetailRecords.HISTORYFAMILYMEMBER_DTL_ID eq Variables.PertinentInfo.SmartControls.DETAILID>
									<cfset Variables.PertDisplayValue = ''>
									<cfif #Trim(Variables.PertinentInfo.SMARTCONTROLS.SmartControlComment)# neq ''>
										 <cfset Variables.PertDisplayValue = Variables.PertinentInfo.SMARTCONTROLS.DisplayName&'&nbsp;('&#Trim(Variables.PertinentInfo.SMARTCONTROLS.SmartControlComment)#&')'>	
									<cfelse>
										<cfset Variables.PertDisplayValue = Variables.PertinentInfo.SMARTCONTROLS.DisplayName>
									</cfif>		
									<cfif Variables.pertlist eq ''>
										<cfset Variables.pertlist = Variables.PertDisplayValue>
									<cfelse>				
										<cfset Variables.pertlist = Variables.pertlist&','&Variables.PertDisplayValue>					
									</cfif>
								</cfif>	
							</cfloop>
              <?php
              $pertlist = array();
              foreach ($SmartControls_result as $sr){
                if(trim($sr->SmartControlComment) != ""){
                  $PertDisplayValue = $sr->DisplayName." &nbsp; (".$sr->SmartControlComment.")";
                }else{
                  $PertDisplayValue = $sr->DisplayName;
                }
                  
                $pertlist[] = $PertDisplayValue;
              }
              
              
//              <cfset Variables.PertinentInfoCount = 0>
//							<cfset variables.ItemOutput = ""> 
//							<cfloop list="#Variables.pertlist#" index="variables.I">
//								<cfif Variables.PertinentInfoCount eq 0>
//									<cfset variables.ItemOutput = variables.ItemOutput & "The patient denies any family history of " & variables.I>
//									<cfset Variables.PertinentInfoCount = 1>
//								<cfelse>
//									<cfif variables.I eq ListLast(Variables.pertlist)>
//										<cfset variables.ItemOutput = variables.ItemOutput & " and ">
//									<cfelse>
//										<cfset variables.ItemOutput = variables.ItemOutput & ", ">
//									</cfif>	
//									<cfset variables.ItemOutput = variables.ItemOutput & variables.I>
//								</cfif>
//							</cfloop>
              $PertinentInfoCount = 0;
              $ItemOutput = "";
              foreach ($pertlist as $v){
                if($PertinentInfoCount == 0){
                  $ItemOutput = $ItemOutput." The patient denies any family history of ".$v;
                }else{
                  if($v == $pertlist[sizeof($pertlist)-1]){
                    $ItemOutput = $ItemOutput." and ";
                  }else{
                    $ItemOutput = $ItemOutput.", ";
                  }
                  $ItemOutput = $ItemOutput.$v; 
                }
              }
              
//              <cfif Variables.PertinentInfoCount eq 0>
//								<cfset variables.ItemOutput = "&nbsp;">
//							<cfelse>
//								<cfset variables.ItemOutput = variables.ItemOutput & ".">
//							</cfif>
//							#variables.ItemOutput#
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
				
				<tr>
					<td width="7">&nbsp;</td>				
					<td width="1%" style="<?php echo $TextStyle; ?>" align="right" nowrap><strong>Last Edited On:</strong></td>
					<td width="4">&nbsp;</td>
					<td style="<?php echo $TextStyle; ?>" > <?php  date('m/d/Y', strtotime($drc->LASTEDITDATE)) ?> </td>
					<td style="<?php echo $TextStyle; ?>" align="right" nowrap><strong>Last Edited By:</strong></td>
					<td width="4">&nbsp;</td>
					<td style="<?php echo $TextStyle; ?>" > <?php echo $drc->DISPLAYLASTEDITEDBY;  ?></td>
				</tr>
			</table>	
			<p />			
    <?php
        }
    ?>
	<?php
}
  ?>
<?php endif;?>