<?php
//<!--- CASE 10,018  CH  21 July 2011 --->
// 
//<cfparam name="Attributes.ConfigKey" default="0">
//<cfif Attributes.ConfigKey EQ ''>
//	<cfset Attributes.ConfigKey = 0>
//</cfif>

if ($ConfigKey == "") {
  $ConfigKey = 0;
}
//<!---CASE 10,018 Added For Reviewed by info--->
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.orgTimeZoneOffset = Session.UTC_TimeOffset>
//	<cfset Variables.orgTimeZoneDST = Session.UTC_DST>
//	<cfset Variables.orgTimeZoneId = Session.UTC_TimeZoneId>
//</cflock>
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="CustomConfig">
//	Select TOP 1
//		MedicationInstructions
//	From EncounterConfig
//	Where EncounterConfig_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.ConfigKey#">
//</cfquery>

$sql = "	Select TOP 1
		MedicationInstructions
	From  " . $data_db . ".dbo.EncounterConfig
	Where EncounterConfig_Id=$ConfigKey";

$CustomConfig = $this->ReportModel->data_db->query($sql);
$CustomConfig_num = $CustomConfig->num_rows();
$CustomConfig_row = $CustomConfig->row();

//<cfif CustomConfig.RecordCount EQ 0>
//	<cfset CustomConfig = QueryNew( "MedicationInstructions" )>
//	<cfset QueryAddRow( CustomConfig )>
//	<cfset QuerySetCell( CustomConfig, "MedicationInstructions", 0)>
//</cfif>

if ($CustomConfig_num == 0) {
  //SKIP
  //<cfset CustomConfig = QueryNew( "MedicationInstructions" )>
  //<cfset QueryAddRow( CustomConfig )>
  //<cfset QuerySetCell( CustomConfig, "MedicationInstructions", 0)>
}

//<cfquery datasource="#Attributes.EMRDataSource#" name="MedicationNotes">
//	Select 
//		Case When LTrim(RTrim(isNull(MH.MedicationDosage,''))) = LTrim(RTrim(isNull(MH.MedicationFreq,''))) Then 
//			LTrim(RTrim(isNull(MH.MedicationDosage,'')))
//		Else
//			LTrim(RTrim(isNull(MH.MedicationDosage,''))) + ' ' + LTrim(RTrim(isNull(MH.MedicationFreq,'')))
//		End As MedicationDosage,
//		MH.MedicationRefills,
//		isnull(MH.RenewalDate, MH.EncounterDate) as EncounterDate,
//		MH.MedicationInstructions,
//		MH.MedicationQuantity,
//		CASE When isNull((Select Top 1 Abbreviation From MedicationUnitsMap MUM Where MUM.MedicationUnitsMap_ID = MH.MedicationUnitsMap_ID),'') = '' Then
//	       rtrim(ltrim(isnull(MH.MedicationQuantity, 0))) + ' ' + isNull((Select Top 1 Definition From MedicationUnitsMap MUM Where MUM.MedicationUnitsMap_ID = MH.MedicationUnitsMap_ID and MUM.MedicationUnitsMap_Id != 19),'')
//	    Else
//	       rtrim(ltrim(isnull(MH.MedicationQuantity, 0))) + ' ' + isNull((Select Top 1 Abbreviation From MedicationUnitsMap MUM Where MUM.MedicationUnitsMap_ID = MH.MedicationUnitsMap_ID and MUM.MedicationUnitsMap_Id != 19),'')
//	    End As MedicationQuantityUnitsDisplay,
//		MM.MedicationName
//	From MedicationHistory MH,
//		MedicationList ML,
//		MedicationMaster MM
//	Where MedicationHistory_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And MH.Medication_Id=ML.Medication_Id
//		And ML.MedicationMaster_Id=MM.MedicationMaster_Id
//		And (MH.StopDate><cfqueryparam cfsqltype="CF_SQL_TIMESTAMP" value="#DateFormat(Now(),'mm/dd/yyyy')# 23:59:59"> OR MH.StopDate IS NULL)
//		And (MH.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR MH.Hidden IS NULL)
//Union All
//	Select 
//		Case When LTrim(RTrim(isNull(MH.MedicationDosage,''))) = LTrim(RTrim(isNull(MH.MedicationFreq,''))) Then 
//			LTrim(RTrim(isNull(MH.MedicationDosage,'')))
//		Else
//			LTrim(RTrim(isNull(MH.MedicationDosage,''))) + ' ' + LTrim(RTrim(isNull(MH.MedicationFreq,'')))
//		End As MedicationDosage,
//		MH.MedicationRefills,
//		isnull(MH.RenewalDate, MH.EncounterDate) as EncounterDate,
//		MH.MedicationInstructions,
//		MH.MedicationQuantity,
//		CASE When isNull((Select Top 1 Abbreviation From MedicationUnitsMap MUM Where MUM.MedicationUnitsMap_ID = MH.MedicationUnitsMap_ID),'') = '' Then
//	       rtrim(ltrim(isnull(MH.MedicationQuantity, 0))) + ' ' + isNull((Select Top 1 Definition From MedicationUnitsMap MUM Where MUM.MedicationUnitsMap_ID = MH.MedicationUnitsMap_ID and MUM.MedicationUnitsMap_Id != 19),'')
//	    Else
//	       rtrim(ltrim(isnull(MH.MedicationQuantity, 0))) + ' ' + isNull((Select Top 1 Abbreviation From MedicationUnitsMap MUM Where MUM.MedicationUnitsMap_ID = MH.MedicationUnitsMap_ID and MUM.MedicationUnitsMap_Id != 19),'')
//	    End As MedicationQuantityUnitsDisplay,
//		MU.MedicationName
//	From MedicationHistory MH,
//		MedicationsUncoded MU
//	Where MedicationHistory_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And MH.Uncoded_Id=MU.Uncoded_Id
//		And (MH.StopDate><cfqueryparam cfsqltype="CF_SQL_TIMESTAMP" value="#DateFormat(Now(),'mm/dd/yyyy')# 23:59:59"> OR MH.StopDate IS NULL)
//		And (MH.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR MH.Hidden IS NULL)
//Order By EncounterDate DESC
//</cfquery>

// $ComponentKey = "60,666,21303"; 

$sql = "	Select 
		Case When LTrim(RTrim(isNull(MH.MedicationDosage,''))) = LTrim(RTrim(isNull(MH.MedicationFreq,''))) Then 
			LTrim(RTrim(isNull(MH.MedicationDosage,'')))
		Else
			LTrim(RTrim(isNull(MH.MedicationDosage,''))) + ' ' + LTrim(RTrim(isNull(MH.MedicationFreq,'')))
		End As MedicationDosage,
		MH.MedicationRefills,
		isnull(MH.RenewalDate, MH.EncounterDate) as EncounterDate,
		MH.MedicationInstructions,
		MH.MedicationQuantity,
		CASE When isNull((Select Top 1 Abbreviation From MedicationUnitsMap MUM Where MUM.MedicationUnitsMap_ID = MH.MedicationUnitsMap_ID),'') = '' Then
	       rtrim(ltrim(isnull(MH.MedicationQuantity, 0))) + ' ' + isNull((Select Top 1 Definition From MedicationUnitsMap MUM Where MUM.MedicationUnitsMap_ID = MH.MedicationUnitsMap_ID and MUM.MedicationUnitsMap_Id != 19),'')
	    Else
	       rtrim(ltrim(isnull(MH.MedicationQuantity, 0))) + ' ' + isNull((Select Top 1 Abbreviation From MedicationUnitsMap MUM Where MUM.MedicationUnitsMap_ID = MH.MedicationUnitsMap_ID and MUM.MedicationUnitsMap_Id != 19),'')
	    End As MedicationQuantityUnitsDisplay,
		MM.MedicationName
	From MedicationHistory MH,
		MedicationList ML,
		MedicationMaster MM
	Where MedicationHistory_Id In ($ComponentKey)
		And MH.Medication_Id=ML.Medication_Id
		And ML.MedicationMaster_Id=MM.MedicationMaster_Id
		And (MH.StopDate> '" . date("Y-m-d 23:59:59") . "' OR MH.StopDate IS NULL)
		And (MH.Hidden<>1 OR MH.Hidden IS NULL)
Union All
	Select 
		Case When LTrim(RTrim(isNull(MH.MedicationDosage,''))) = LTrim(RTrim(isNull(MH.MedicationFreq,''))) Then 
			LTrim(RTrim(isNull(MH.MedicationDosage,'')))
		Else
			LTrim(RTrim(isNull(MH.MedicationDosage,''))) + ' ' + LTrim(RTrim(isNull(MH.MedicationFreq,'')))
		End As MedicationDosage,
		MH.MedicationRefills,
		isnull(MH.RenewalDate, MH.EncounterDate) as EncounterDate,
		MH.MedicationInstructions,
		MH.MedicationQuantity,
		CASE When isNull((Select Top 1 Abbreviation From MedicationUnitsMap MUM Where MUM.MedicationUnitsMap_ID = MH.MedicationUnitsMap_ID),'') = '' Then
	       rtrim(ltrim(isnull(MH.MedicationQuantity, 0))) + ' ' + isNull((Select Top 1 Definition From MedicationUnitsMap MUM Where MUM.MedicationUnitsMap_ID = MH.MedicationUnitsMap_ID and MUM.MedicationUnitsMap_Id != 19),'')
	    Else
	       rtrim(ltrim(isnull(MH.MedicationQuantity, 0))) + ' ' + isNull((Select Top 1 Abbreviation From MedicationUnitsMap MUM Where MUM.MedicationUnitsMap_ID = MH.MedicationUnitsMap_ID and MUM.MedicationUnitsMap_Id != 19),'')
	    End As MedicationQuantityUnitsDisplay,
		MU.MedicationName
	From " . $data_db . ".dbo.MedicationHistory MH,
		" . $data_db . ".dbo.MedicationsUncoded MU
	Where MedicationHistory_Id In ($ComponentKey)
		And MH.Uncoded_Id=MU.Uncoded_Id
		And (MH.StopDate> '" . date("Y-m-d 23:59:59") . "' OR MH.StopDate IS NULL)
		And (MH.Hidden<>1 OR MH.Hidden IS NULL)
Order By EncounterDate DESC";

$MedicationNotes = $this->ReportModel->data_db->query($sql);
$MedicationNotes_num = $MedicationNotes->num_rows();
$MedicationNotes_row = $MedicationNotes->row();

//<cfquery datasource="#Attributes.EMRDataSource#" name="DiscontinuedMedNotes">
//	Select 
//		Case When LTrim(RTrim(isNull(MH.MedicationDosage,''))) = LTrim(RTrim(isNull(MH.MedicationFreq,''))) Then 
//			LTrim(RTrim(isNull(MH.MedicationDosage,'')))
//		Else
//			LTrim(RTrim(isNull(MH.MedicationDosage,''))) + ' ' + LTrim(RTrim(isNull(MH.MedicationFreq,'')))
//		End As MedicationDosage,
//		MH.MedicationRefills,
//		isnull(MH.RenewalDate, MH.EncounterDate) as EncounterDate,
//		MH.MedicationInstructions,
//		MH.MedicationQuantity,
//		MM.MedicationName,
//		MH.DiscontinueReason,
//		MH.StopDate
//	From MedicationHistory MH,
//		MedicationList ML,
//		MedicationMaster MM
//	Where MedicationHistory_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And MH.Medication_Id=ML.Medication_Id
//		And ML.MedicationMaster_Id=MM.MedicationMaster_Id
//		And (MH.StopDate<=<cfqueryparam cfsqltype="CF_SQL_TIMESTAMP" value="#DateFormat(Now(),'mm/dd/yyyy')# 23:59:59">)
//		And (MH.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR MH.Hidden IS NULL)
//Union All
//	Select 
//		Case When LTrim(RTrim(isNull(MH.MedicationDosage,''))) = LTrim(RTrim(isNull(MH.MedicationFreq,''))) Then 
//			LTrim(RTrim(isNull(MH.MedicationDosage,'')))
//		Else
//			LTrim(RTrim(isNull(MH.MedicationDosage,''))) + ' ' + LTrim(RTrim(isNull(MH.MedicationFreq,'')))
//		End As MedicationDosage,
//		MH.MedicationRefills,
//		isnull(MH.RenewalDate, MH.EncounterDate) as EncounterDate,
//		MH.MedicationInstructions,
//		MH.MedicationQuantity,
//		MU.MedicationName,
//		MH.DiscontinueReason,
//		MH.StopDate
//	From MedicationHistory MH,
//		MedicationsUncoded MU
//	Where MedicationHistory_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And MH.Uncoded_Id=MU.Uncoded_Id
//		And (MH.StopDate<=<cfqueryparam cfsqltype="CF_SQL_TIMESTAMP" value="#DateFormat(Now(),'mm/dd/yyyy')# 23:59:59">)
//		And (MH.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR MH.Hidden IS NULL)
//Order By EncounterDate DESC
//</cfquery>


$sql = "	Select 
		Case When LTrim(RTrim(isNull(MH.MedicationDosage,''))) = LTrim(RTrim(isNull(MH.MedicationFreq,''))) Then 
			LTrim(RTrim(isNull(MH.MedicationDosage,'')))
		Else
			LTrim(RTrim(isNull(MH.MedicationDosage,''))) + ' ' + LTrim(RTrim(isNull(MH.MedicationFreq,'')))
		End As MedicationDosage,
		MH.MedicationRefills,
		isnull(MH.RenewalDate, MH.EncounterDate) as EncounterDate,
		MH.MedicationInstructions,
		MH.MedicationQuantity,
		MM.MedicationName,
		MH.DiscontinueReason,
		MH.StopDate
	From MedicationHistory MH,
		MedicationList ML,
		MedicationMaster MM
	Where MedicationHistory_Id In ($ComponentKey)
		And MH.Medication_Id=ML.Medication_Id
		And ML.MedicationMaster_Id=MM.MedicationMaster_Id
		And (MH.StopDate<= '" . date("Y-m-d 23:59:59") . "' )
		And (MH.Hidden<>1 OR MH.Hidden IS NULL)
Union All
	Select 
		Case When LTrim(RTrim(isNull(MH.MedicationDosage,''))) = LTrim(RTrim(isNull(MH.MedicationFreq,''))) Then 
			LTrim(RTrim(isNull(MH.MedicationDosage,'')))
		Else
			LTrim(RTrim(isNull(MH.MedicationDosage,''))) + ' ' + LTrim(RTrim(isNull(MH.MedicationFreq,'')))
		End As MedicationDosage,
		MH.MedicationRefills,
		isnull(MH.RenewalDate, MH.EncounterDate) as EncounterDate,
		MH.MedicationInstructions,
		MH.MedicationQuantity,
		MU.MedicationName,
		MH.DiscontinueReason,
		MH.StopDate
	From MedicationHistory MH,
		MedicationsUncoded MU
	Where MedicationHistory_Id In ($ComponentKey)
		And MH.Uncoded_Id=MU.Uncoded_Id
		And (MH.StopDate<= '" . date("Y-m-d 23:59:59") . "')
		And (MH.Hidden<>1 OR MH.Hidden IS NULL)
Order By EncounterDate DESC";

$DiscontinuedMedNotes = $this->ReportModel->data_db->query($sql);
$DiscontinuedMedNotes_num = $DiscontinuedMedNotes->num_rows();
$DiscontinuedMedNotes_row = $DiscontinuedMedNotes->row();


//<!---CASE 10,018 Added getReviewedMedsDB--->
//<cfquery datasource="#Attributes.EMRDataSource#" name="getReviewedMedsDB">
//	Select	TOP 1
//			dbo.UTCtoLocaltz(M.ReviewedOn_UTC,<cfqueryparam cfsqltype="cf_sql_numeric" scale="2" value="#Variables.orgTimeZoneOffset#">,<cfqueryparam cfsqltype="cf_sql_bit" value="#Variables.orgTimeZoneDST#">,<cfqueryparam cfsqltype="cf_sql_numeric" scale="2" value="#Variables.orgTimeZoneId#">) as ReviewedOn_UTC,
//			ReviewedBy_Users_PK,
//			isNull(PP.ProviderTitle,'') + ' ' + u.FName + ' ' + u.LName + ' ' As FullName
//	FROM	MedicationsReviewed M
//			INNER JOIN #Attributes.DSNPreFix#eCast_Data.dbo.Users U
//        	    	ON M.ReviewedBy_Users_PK=u.Id
//	        LEFT JOIN ProviderProfile PP
//	            	ON u.User_Id=PP.User_Id
//	 Where M.Patient_Id=<cfqueryparam cfsqltype="cf_sql_bigint" value="#Attributes.PatientKey#">
//	 Order By M.MedicationsReviewed_Id Desc
//</cfquery>

//$PatientKey = "1000020552"; //"60,666,21303"; //Embeded ;

$sql = "	Select	TOP 1
      M.ReviewedOn_UTC as ReviewedOn_UTC,
			ReviewedBy_Users_PK,
			isNull(PP.ProviderTitle,'') + ' ' + u.FName + ' ' + u.LName + ' ' As FullName
	FROM	MedicationsReviewed M
			INNER JOIN " . $user_db . ".dbo.Users U
        	    	ON M.ReviewedBy_Users_PK=u.Id
	        LEFT JOIN " . $data_db . ".dbo.ProviderProfile PP
	            	ON u.User_Id=PP.User_Id
	 Where M.Patient_Id=$PatientKey
	 Order By M.MedicationsReviewed_Id Desc";

$getReviewedMedsDB = $this->ReportModel->data_db->query($sql);
$getReviewedMedsDB_num = $getReviewedMedsDB->num_rows();
$getReviewedMedsDB_row = $getReviewedMedsDB->row();


//$AlertNotes
//<cfif MedicationNotes.RecordCount NEQ 0>
//
//	<cfif caller.HeaderNeeded EQ True>
//		<cfmodule template="componentheaders.cfm"
//			 EMRDataSource="#Attributes.EMRDataSource#"
//			 HeaderKey="#Attributes.HeaderKey#"
//			 PatientKey="#Attributes.PatientKey#"
//			 HeaderMasterKey="#Attributes.HeaderMasterKey#"
//			 FreeTextKey="#Attributes.FreeTextKey#"
//			 SOHeaders="#Attributes.SOHeaders#">
//		<cfset caller.HeaderNeeded = False>
//		<cfset caller.NeedTemplateHeader = False>
//	</cfif>
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
//	<cfset variables.DefaultStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//	<cfset variables.ColumnHeaderStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: bold; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//
//	<cfset Variables.MedNote=0>
//	<table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">
//		<!---CASE 10,018 Added Reviewed by info--->
//		<tr>
//			<td colspan="2">
//				<cfoutput>
//					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
//					<font style="#variables.DefaultStyle#">
//						(Last Reviewed By: 
//						<cfif getReviewedMedsDB.Recordcount neq 0>
//							#getReviewedMedsDB.FullName#
//							on
//							#DateFormat(getReviewedMedsDB.ReviewedOn_UTC,"MM/DD/YYYY")# #TimeFormat(getReviewedMedsDB.ReviewedOn_UTC,"h:mm tt")#
//						</cfif>
//						)
//					</font>
//				</cfoutput>
//			</td>
//		</tr>
//
//		<tr>
//			<td width="7">&nbsp;</td>
//			<td>
//				<table border="0" cellpadding="0" cellspacing="0">
//					<cfoutput>
//					<tr>
//						<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//							Date
//						</td>
//						<td width="4">&nbsp;</td>
//						<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//							Medication<img src="..\images\spacer.gif" width="100" height="1">
//						</td>
//						<td width="4">&nbsp;</td>
//						<td align="center" style="#variables.ColumnHeaderStyle#" valign="top">
//							Qty
//						</td>	
//						<td width="4">&nbsp;</td>
//						<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//							Dosage & Frequency
//						</td>	
//						<td width="4">&nbsp;</td>
//						<td align="center" style="#variables.ColumnHeaderStyle#" valign="top">
//							Rfl
//						</td>	
//					</tr>
//					</cfoutput>
//					
//					<cfoutput query="MedicationNotes">
//						<tr>
//							<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//								#DateFormat(MedicationNotes.EncounterDate,"mm/dd/yyyy")#
//							</td>
//							<td width="4">&nbsp;</td>
//							<td align="left" style="#variables.DefaultStyle#" valign="top">
//								#trim(MedicationNotes.MedicationName)#
//							</td>
//							<td width="4">&nbsp;</td>
//							<td align="center" nowrap style="#variables.DefaultStyle#;" valign="top">
//								#MedicationNotes.MedicationQuantityUnitsDisplay#
//							</td>	
//							<td width="4">&nbsp;</td>
//							<td align="left" style="#variables.DefaultStyle#" valign="top">
//								#trim("#MedicationNotes.MedicationDosage#")#&nbsp;
//							</td>	
//							<td width="4">&nbsp;</td>
//							<td align="center" nowrap style="#variables.DefaultStyle#" valign="top">
//								#MedicationNotes.MedicationRefills#
//							</td>	
//						</tr>	
//
//						<cfif MedicationNotes.MedicationInstructions NEQ "">
//							<cfset Variables.MedNote=1>
//						</cfif>
//
//					</cfoutput>
//				</table>
//			</td>
//		</tr>
//				
//		<cfif (Variables.MedNote EQ 1) and (CustomConfig.MedicationInstructions EQ 1)>
//			<tr>
//				<td>&nbsp;</td>
//			</tr>
//
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td>
//					<table border="0" cellpadding="0" cellspacing="0">
//						<cfoutput>
//						<tr>
//							<td align="left" colspan="5" style="#variables.ColumnHeaderStyle#" valign="top">
//								Medication Pharmacist Messages
//							</td>
//						</tr>
//
//						<tr>
//							<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//								Date
//							</td>
//							<td width="4">&nbsp;</td>
//							<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//								Medication<img src="..\images\spacer.gif" width="100" height="1">
//							</td>
//							<td width="4">&nbsp;</td>
//							<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//								Pharmacist Message
//							</td>	
//						</tr>
//						</cfoutput>
//						
//						<cfoutput query="MedicationNotes">
//							<cfif Trim(MedicationNotes.MedicationInstructions) NEQ "">
//								<tr>
//									<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//										#DateFormat(MedicationNotes.EncounterDate,"mm/dd/yyyy")#
//									</td>
//									<td width="4">&nbsp;</td>
//									<td align="left" style="#variables.DefaultStyle#" valign="top">
//										#MedicationNotes.MedicationName#
//									</td>
//									<td width="4">&nbsp;</td>
//									<td align="left" style="#variables.DefaultStyle#" valign="top">
//										#Trim(MedicationNotes.MedicationInstructions)#
//									</td>
//								</tr>
//							</cfif>
//						</cfoutput>
//					</table>
//				</td>
//			</tr>
//	
//		</cfif>
//
//	</table>
//
//<cfelse> <!--- Check to see if the box is checked, do not run this code for header_id 39 --->
//    <cfif Attributes.HeaderMasterKey EQ 29>
//		<cfquery name="NoCurrentMedicationsRS" datasource="#Attributes.EMRDataSource#">
//		Select TOP 1 
//			   NoCurrentMedications
//		From PatientProfile
//		Where patient_id=<cfqueryparam cfsqltype="cf_sql_bigint" value="#Attributes.PatientKey#">
//		</cfquery>
//		
//		<cfif NoCurrentMedicationsRS.NoCurrentMedications EQ 1>
//			<cfmodule template="componentheaders.cfm"
//				 EMRDataSource="#Attributes.EMRDataSource#"
//				 HeaderKey="#Attributes.HeaderKey#"
//				 PatientKey="#Attributes.PatientKey#"
//				 HeaderMasterKey="#Attributes.HeaderMasterKey#"
//				 FreeTextKey="#Attributes.FreeTextKey#"
//				 SOHeaders="#Attributes.SOHeaders#">
//			<cfset caller.HeaderNeeded = False>
//			<cfset caller.NeedTemplateHeader = False>
//			
//			<!---
//			The CFC call below returns the Font and Color information for the display of Chart Note body items.
//			Six items are returned:
//		
//			Variables.BodyFontInfo.FontColor = Body Font Color  (ex. 000000 for black)
//			Variables.BodyFontInfo.FontSize = Body Font Size (ex. 12)
//			Variables.BodyFontInfo.FontFace = Body Font Family (ex. Times New Roman)
//			Variables.BodyFontInfo.FontWeight = Bold or Normal
//			Variables.BodyFontInfo.FontStyle = Italics or Normal
//			Variables.BodyFontInfo.FontDecoration = Underline or None
//			--->
//			<cfset variables.BodyFontInfo = CreateObject("component","cfc.chartnote.chartnote").getBodyFontInfo(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Attributes.HeaderKey)>
//			<cfset variables.DefaultStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//			<cfset variables.ColumnHeaderStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: bold; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//		
//			<table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">
//				<!---CASE 10,018 Added Reviewed by info--->
//				<tr>
//					<td colspan="2">&nbsp;</td>
//				</tr>
//				<tr>
//					<td colspan="2">
//						<cfoutput>
//							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
//							<font style="#variables.DefaultStyle#">
//								(Last Reviewed By: 
//								<cfif getReviewedMedsDB.Recordcount neq 0>
//									#getReviewedMedsDB.FullName#
//									on
//									#DateFormat(getReviewedMedsDB.ReviewedOn_UTC,"MM/DD/YYYY")# #TimeFormat(getReviewedMedsDB.ReviewedOn_UTC,"h:mm tt")#
//								</cfif>
//								)
//							</font>
//						</cfoutput>
//					</td>
//				</tr>
//				<tr>
//					<td width="7">&nbsp;</td>
//					<cfoutput>
//					<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					</cfoutput>
//						This patient currently takes no medications.
//					</td>
//				</tr>
//				<tr><td>&nbsp;</td></tr>
//			</table>
//		</cfif>
//	</cfif> 
//</cfif>
//
//<cfif DiscontinuedMedNotes.RecordCount NEQ 0>
//
//	<cfif caller.HeaderNeeded EQ True>
//		<cfmodule template="componentheaders.cfm"
//			 EMRDataSource="#Attributes.EMRDataSource#"
//			 HeaderKey="#Attributes.HeaderKey#"
//			 PatientKey="#Attributes.PatientKey#"
//			 HeaderMasterKey="#Attributes.HeaderMasterKey#"
//			 FreeTextKey="#Attributes.FreeTextKey#"
//			 SOHeaders="#Attributes.SOHeaders#">
//		<cfset caller.HeaderNeeded = False>
//		<cfset caller.NeedTemplateHeader = False>
//	</cfif>
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
//	<cfset variables.DefaultStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//	<cfset variables.ColumnHeaderStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: bold; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//
//	<table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">
//		<cfif MedicationNotes.RecordCount NEQ 0>
//			<tr>
//				<td>&nbsp;</td>
//			</tr>
//		</cfif>
//
//		<tr>
//			<td width="7">&nbsp;</td>
//			<td>
//				<table border="0" cellpadding="0" cellspacing="0">
//					<cfoutput>
//					<tr>
//						<td align="left" colspan="9" style="#variables.ColumnHeaderStyle#" valign="top">
//							Discontinued Medications
//						</td>
//					</tr>
//
//					<tr>
//						<td align="left" style="#variables.ColumnHeaderStyle#;" valign="top">
//							Date
//						</td>
//						<td width="4">&nbsp;</td>
//						<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//							Stop Date
//						</td>
//						<td width="4">&nbsp;</td>
//						<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//							Medication<img src="..\images\spacer.gif" width="100" height="1">
//						</td>
//						<td width="4">&nbsp;</td>
//						<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//							Discontinued Reason
//						</td>	
//					</tr>
//					</cfoutput>			
//					
//					<cfoutput query="DiscontinuedMedNotes">
//						<tr>
//							<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//								#DateFormat(DiscontinuedMedNotes.EncounterDate,"mm/dd/yyyy")#
//							</td>
//							<td width="4">&nbsp;</td>
//							<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//								#DateFormat(DiscontinuedMedNotes.StopDate,"mm/dd/yyyy")#
//							</td>
//							<td width="4">&nbsp;</td>
//							<td align="left" style="#variables.DefaultStyle#;" valign="top">
//								#trim(DiscontinuedMedNotes.MedicationName)#
//							</td>
//							<td width="4">&nbsp;</td>
//							<td align="left" style="#variables.DefaultStyle#" valign="top">
//								#trim("#DiscontinuedMedNotes.DiscontinueReason#")#&nbsp;
//							</td>	
//						</tr>	
//					</cfoutput>
//				</table>
//			</td>
//		</tr>
//	</table>
//</cfif>

if ($MedicationNotes_num != 0) {

//  if (HeaderNeeded) { //SKIPP
  $data['HeaderKey'] = $HeaderKey;
  $data['PatientKey'] = $PatientKey;
  $data['HeaderMasterKey'] = $HeaderMasterKey;
  $data['FreeTextKey'] = $FreeTextKey;
  $data['SOHeaders'] = $SOHeaders;
  $this->load->view('encounter/print/componentheaders', $data);
//  }
  $data['data_db'] = $data_db;
  $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
  $DefaultStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  $ColumnHeaderStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: bold; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  
  $MedNote = 0;
  ?>
  <table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <!---CASE 10,018 Added Reviewed by info--->
    <tr>
      <td colspan="2">
    <cfoutput>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
      <font style="<?php echo $DefaultStyle; ?>">
      (Last Reviewed By: 
      <?php
      if ($getReviewedMedsDB_row->Recordcount != 0) {
        echo $getReviewedMedsDB_row->FullName . " on ";
        echo date('m/d/Y H:i s', strtotime($getReviewedMedsDB_row->ReviewedOn_UTC));
      }
      ?>
      )
      </font>
    </cfoutput>
  </td>
  </tr>

  <tr>
    <td width="7">&nbsp;</td>
    <td>
      <table border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
            Date
          </td>
          <td width="4">&nbsp;</td>
          <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
            Medication
  <!--              <img src="..\images\spacer.gif" width="100" height="1">-->
          </td>
          <td width="4">&nbsp;</td>
          <td align="center" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
            Qty
          </td>	
          <td width="4">&nbsp;</td>
          <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
            Dosage & Frequency
          </td>	
          <td width="4">&nbsp;</td>
          <td align="center" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
            Rfl
          </td>	
        </tr>

          <tr>
            <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
              <?php echo date('m/d/Y', strtotime($MedicationNotes_row->ReviewedOn_UTC)); ?>
            </td>
            <td width="4">&nbsp;</td>
            <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
              <?php echo trim($MedicationNotes_row->MedicationName); ?>
            </td>
            <td width="4">&nbsp;</td>
            <td align="center" nowrap style="<?php echo $DefaultStyle; ?>;" valign="top">
              <?php echo trim($MedicationNotes_row->MedicationQuantityUnitsDisplay); ?>
            </td>	
            <td width="4">&nbsp;</td>
            <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
              <?php echo trim($MedicationNotes_row->MedicationDosage); ?>&nbsp;
            </td>	
            <td width="4">&nbsp;</td>
            <td align="center" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
              <?php echo trim($MedicationNotes_row->MedicationRefills); ?>
            </td>	
          </tr>	

          <?php
          if ($MedicationNotes_row->MedicationInstructions != "") {
            $MedNote = 1;
          }
          ?>

      </table>
    </td>
  </tr>

  <?php
  if ($MedNote == 1 && $CustomConfig_row->MedicationInstructions == 1) {
    ?>

    <tr>
      <td>&nbsp;</td>
    </tr>

    <tr>
      <td width="7">&nbsp;</td>
      <td>
        <table border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td align="left" colspan="5" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
              Medication Pharmacist Messages
            </td>
          </tr>

          <tr>
            <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
              Date
            </td>
            <td width="4">&nbsp;</td>
            <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
              Medication
              <!--<img src="..\images\spacer.gif" width="100" height="1">-->
            </td>
            <td width="4">&nbsp;</td>
            <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
              Pharmacist Message
            </td>	
          </tr>
          <?php
          if ($MedicationNotes_row->MedicationInstructions != "") {
            ?>
            <tr>
              <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
                <?php echo date('m/d/Y', strtotime($MedicationNotes_row->EncounterDate)); ?>
              </td>
              <td width="4">&nbsp;</td>
              <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
                <?php echo trim($MedicationNotes_row->MedicationName); ?>
              </td>
              <td width="4">&nbsp;</td>
              <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
                <?php echo trim($MedicationNotes_row->MedicationInstructions); ?>
              </td>
            </tr>
            <?php
          }
          ?>
        </table>
      </td>
    </tr>

    <?php
  }
  ?>

  </table>

  <?php
  if ($HeaderMasterKey == 29) {
    $sql = "Select TOP 1 
			   NoCurrentMedications
		From PatientProfile
		Where patient_id=$PatientKey";


    $NoCurrentMedicationsRS = $this->ReportModel->data_db->query($sql);
    $NoCurrentMedicationsRS_num = $NoCurrentMedicationsRS->num_rows();
    $NoCurrentMedicationsRS_row = $NoCurrentMedicationsRS->row();

    if ($NoCurrentMedicationsRS->NoCurrentMedications == 1) {
      //  if (HeaderNeeded) { //BLM DIKETAHUI DATA TSB
      $data['HeaderKey'] = $HeaderKey;
      $data['PatientKey'] = $PatientKey;
      $data['HeaderMasterKey'] = $HeaderMasterKey;
      $data['FreeTextKey'] = $FreeTextKey;
      $data['SOHeaders'] = $SOHeaders;
      $this->load->view('encounter/print/componentheaders', $data);
      //}
      $DefaultStyle = ""; //PROCESS 
      ?>
      <table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">
        <!---CASE 10,018 Added Reviewed by info--->
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            <font style="<?php echo $DefaultStyle; ?>">
            (Last Reviewed By: 
            <?php
            if ($getReviewedMedsDB_num != 0) {
              echo $getReviewedMedsDB_row->FullName . " on ";
              echo date('m/d/Y H:i s', strtotime($getReviewedMedsDB_row->ReviewedOn_UTC));
            }
            ?>
            )
            </font>
          </td>
        </tr>
        <tr>
          <td width="7">&nbsp;</td>
        <cfoutput>
          <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        </cfoutput>
        This patient currently takes no medications.
      </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
      </table>
      <?php
    }
  }

  if ($DiscontinuedMedNotes_num != 0) {
    //  if (HeaderNeeded) { //BLM DIKETAHUI DATA TSB
    $data['HeaderKey'] = $HeaderKey;
    $data['PatientKey'] = $PatientKey;
    $data['HeaderMasterKey'] = $HeaderMasterKey;
    $data['FreeTextKey'] = $FreeTextKey;
    $data['SOHeaders'] = $SOHeaders;
    $this->load->view('encounter/print/componentheaders', $data);

    $DefaultStyle = ""; //SKIP
    $ColumnHeaderStyle = ""; //SKIP
    ?>
    <table border="0" cellpadding="0" cellspacing="0" style="width: 7.0in;">
      <cfif MedicationNotes.RecordCount NEQ 0>
            <tr>
          <td>&nbsp;</td>
        </tr>
      </cfif>

      <tr>
        <td width="7">&nbsp;</td>
        <td>
          <table border="0" cellpadding="0" cellspacing="0">
            <cfoutput>
              <tr>
                <td align="left" colspan="9" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
                  Discontinued Medications
                </td>
              </tr>

              <tr>
                <td align="left" style="<?php echo $ColumnHeaderStyle; ?>;" valign="top">
                  Date
                </td>
                <td width="4">&nbsp;</td>
                <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
                  Stop Date
                </td>
                <td width="4">&nbsp;</td>
                <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
                  Medication 
                  <!--<img src="..\images\spacer.gif" width="100" height="1">-->
                </td>
                <td width="4">&nbsp;</td>
                <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
                  Discontinued Reason
                </td>	
              </tr>
            </cfoutput>			

            <cfoutput query="DiscontinuedMedNotes">
              <tr>
                <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
                  <?php echo date('m/d/Y H:i s', strtotime($DiscontinuedMedNotes_row->EncounterDate)); ?>
                </td>
                <td width="4">&nbsp;</td>
                <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
                  <?php echo date('m/d/Y H:i s', strtotime($DiscontinuedMedNotes_row->StopDate)); ?>
                </td>
                <td width="4">&nbsp;</td>
                <td align="left" style="<?php echo $DefaultStyle; ?>;" valign="top">
                  <?php echo trim($DiscontinuedMedNotes_row->MedicationName); ?>
                </td>
                <td width="4">&nbsp;</td>
                <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
                   <?php echo trim($DiscontinuedMedNotes_row->DiscontinueReason); ?>&nbsp;
                </td>	
              </tr>	
            </cfoutput>
          </table>
        </td>
      </tr>
    </table>
    <?php
  }
}
?>