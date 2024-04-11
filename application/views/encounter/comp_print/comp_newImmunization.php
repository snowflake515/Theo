<?php
$stop = TRUE;
if(!$stop):
//<!--- Program Comp_newImmunization.cfm  --->
//<!--- CASE 10,018  CH  21 July 2011 --->
//
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.sUTC_DST=Session.UTC_DST>
//	<cfset Variables.sUTC_TimeOffset=Session.UTC_TimeOffset>
//	<cfset Variables.sUTC_TimeZoneID=Session.UTC_TimeZoneId>
//	<!---CASE 10,018 Added Variables.patientID--->		
//	<cfset Variables.patientID = Session.Patient_Id>	
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
//<cfset Variables.dataObj.orgTimeZoneOffset = Variables.sUTC_TimeOffset>
//<cfset Variables.dataObj.orgTimeZoneDST = Variables.sUTC_DST>
//<cfset Variables.dataObj.orgTimeZoneId = Variables.sUTC_TimeZoneID>
//<!---CASE 10,018 Added Variables.dataObj.patientId--->	
//<cfset Variables.dataObj.patientId = Variables.patientID>	
//	
//<cfif variables.bUseDetailKeys>
//	<cfset Variables.dataObj.immunizationDtlIds = ListToArray(Attributes.ComponentKey,",")>
//	<cfset Variables.Immunizations = CreateObject("component","cfc.immunizations.Immunizations").getImmHistoryListByDtlIdForEncounterComponentNewImmunization(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//<cfelse>
//	<cfset Variables.dataObj.immunizationHistIds = ListToArray(Attributes.ComponentKey,",")>
//	<cfset Variables.Immunizations = CreateObject("component","cfc.immunizations.Immunizations").getImmHistoryListByHistIdForEncounterComponentNewImmunization(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//</cfif>
//$ComponentKey = 1; //Embed param
//Ref Query from Immunizations.cfc
//SKIP dbo.UTCtoLocalTZ
if ($bUseDetailKeys) {
  $sql = "SELECT
		    im.Immunization,
			im.ImmDescription,
			id.DoseNumber,
			id.Dose,
			id.Notes,
			CASE When isNull(mum.Abbreviation,'') = '' Then isNull(mum.Definition,'')
			Else
				isNull(mum.Abbreviation,'')
			End As Units,
      id.AdministrationDate_UTC as AdminDate
			FROM Immunization_Dtl id
					left outer join ImmunizationsMaster im
						on im.ImmunizationsMaster_ID = id.ImmunizationsMaster_ID
					left outer join MedicationUnitsMap mum
						on mum.MedicationUnitsMap_ID = id.Dose_MedicationUnitsMap_ID
								
			WHERE id.Immunization_Dtl_ID IN ($ComponentKey)
  AND (id.Status = 'Administered' OR id.Status = 'Historical')
  Order By Immunization, AdminDate";
} else {
  $sql = "SELECT
		    im.Immunization,
			im.ImmDescription,
			id.DoseNumber,
			id.Dose,
			id.Notes,
			CASE When isNull(mum.Abbreviation,'') = '' Then isNull(mum.Definition,'')
			Else
				isNull(mum.Abbreviation,'')
			End As Units,
			id.AdministrationDate_UTC as AdminDate
			FROM Immunization_Hist ih
					left outer join Immunization_Dtl id
						on ih.Current_Immunization_Dtl_ID = id.Immunization_Dtl_ID
					left outer join ImmunizationsMaster im
						on im.ImmunizationsMaster_ID = id.ImmunizationsMaster_ID
					left outer join MedicationUnitsMap mum
						on mum.MedicationUnitsMap_ID = id.Dose_MedicationUnitsMap_ID
			WHERE id.Immunization_Hist_ID IN ($ComponentKey)
			AND (id.Status = 'Administered' OR id.Status = 'Historical')
			Order By Immunization, AdminDate";
}
$Immunizations = $this->ReportModel->data_db->query($sql);
$Immunizations_num = $Immunizations->num_rows();
$Immunizations_row = $Immunizations->row();


//<!---CASE 10,018 Added Reviewed by info--->
//<cfset Variables.ReviewedInfo = CreateObject("component","cfc.immunizations.Immunizations").getImmunizationReview(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//Ref Query from Immunizations.cfc
//SKIP dbo.UTCtoLocalTZ
//SKIP ImmunizationReviewedDateTZAbbr
//$PatientKey = 1000111996; //Embed param
$sql = "SELECT TOP 1
			Convert(varchar(50), ir.ReviewedOn_UTC) as ImmunizationReviewedDate,
			'ImmunizationReviewedDateTZAbbr EMBED' as ImmunizationReviewedDateTZAbbr,
			isNull(PP.ProviderTitle,'') + ' ' + u.FName + ' ' + u.LName + ' ' As FullName
			FROM ImmunizationReviewed ir
	          	INNER JOIN " . $user_db . ".dbo.Users u
	            	ON ir.ReviewedBy_Users_PK=u.Id
	            LEFT JOIN " . $data_db . ".dbo.ProviderProfile PP
	            	ON u.User_Id=PP.User_Id
			 Where ir.Patient_ID=$PatientKey
			 ORDER BY ImmunizationReviewed_Id desc";

$ReviewedInfo = $this->ReportModel->data_db->query($sql);
$ReviewedInfo_num = $ReviewedInfo->num_rows();
$ReviewedInfo_row = $ReviewedInfo->row();

//<cfset Variables.ImmNotes=0>
//<cfset Variables.Crlf=chr(13)&chr(10)>
//
//<cfif Immunizations.RecordCount NEQ 0>
//	<cfif caller.HeaderNeeded EQ True>
//		<cfmodule template="componentheaders.cfm"
//		 EMRDataSource="#Attributes.EMRDataSource#"
//		 HeaderKey="#Attributes.HeaderKey#"
//		 PatientKey="#Attributes.PatientKey#"
//		 HeaderMasterKey="#Attributes.HeaderMasterKey#"
//		 FreeTextKey="#Attributes.FreeTextKey#"
//		 SOHeaders="#Attributes.SOHeaders#">
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
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//		<!---CASE 10,018 Added Reviewed by info--->
//		<tr>
//			<td colspan="8">	
//				<cfoutput>
//					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
//					<font style="#variables.DefaultStyle#">
//						(Last Reviewed By: 
//						<cfif Variables.ReviewedInfo.Recordcount neq 0>
//							#Variables.ReviewedInfo.FullName# on #DateFormat(Variables.ReviewedInfo.ImmunizationReviewedDate,"MM/DD/YYYY")# #TimeFormat(Variables.ReviewedInfo.ImmunizationReviewedDate,"h:mm tt")#
//						</cfif>
//						)
//					</font>
//				</cfoutput>
//			</td>
//		</tr>
//		<tr>
//			<cfoutput>
//			<td width="7">&nbsp;</td>
//			<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//				Date
//			</td>
//			<td width="4">&nbsp;</td>
//			<td align="left" nowrap style="#variables.ColumnHeaderStyle#" valign="top">
//				Dosage
//			</td>
//			<td width="4">&nbsp;</td>
//			<td align="left" nowrap style="#variables.ColumnHeaderStyle#" valign="top">
//				Dose
//			</td>
//			<td width="4">&nbsp;</td>
//			<td align="left" style="width: 6.5in; #variables.ColumnHeaderStyle#" valign="top">
//				Vaccine
//			</td>
//			</cfoutput>
//		</tr>
//		<cfoutput query="Immunizations">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="left" style="#variables.DefaultStyle#" valign="top">
//					#DateFormat(Immunizations.AdminDate,"mm/dd/yyyy")#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#Immunizations.Dose# #Immunizations.Units#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#Immunizations.DoseNumber#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//					#Trim(Immunizations.ImmDescription)# (#Immunizations.Immunization#)
//				</td>
//			</tr>
//			<cfif Trim(Immunizations.Notes) NEQ "">
//				<cfset Variables.ImmNotes=1>
//			</cfif>
//		</cfoutput>
//		<cfif Variables.ImmNotes EQ 1>
//			<tr>
//				<td>&nbsp;</td>
//			</tr>
//			<tr>
//				<td width="7">&nbsp;</td>
//				<cfoutput>
//				<td align="left" colspan="7" style="width: 7.0in; #variables.ColumnHeaderStyle#" valign="top">
//				</cfoutput>
//					Notes
//				</td>
//			</tr>
//			<cfoutput query="Immunizations">
//				<cfif Trim(Immunizations.Notes) NEQ "">
//					<tr>
//						<td width="7">&nbsp;</td>
//						<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//							#DateFormat(Immunizations.AdminDate,"mm/dd/yyyy")#
//						</td>
//						<td width="4">&nbsp;</td>
//						<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//							(#Immunizations.Immunization#)
//						</td>
//						<td width="4">&nbsp;</td>
//						<td align="left" colspan="3" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//							<cfset Variables.INotes=ReplaceNoCase(Immunizations.Notes,Variables.Crlf,"<br>","ALL")>
//							#Trim(Variables.INotes)#
//						</td>
//					</tr>
//				</cfif>
//			</cfoutput>	
//			<tr>
//				<td>&nbsp;</td>
//			</tr>
//		</cfif>
//	</table>
//</cfif>

if ($Immunizations_num != 0) {

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
  ?>

                          <!--<cfinclude template="comp_newvaccinedue.cfm">-->
  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <!---CASE 10,018 Added Reviewed by info--->
    <tr>
      <td colspan="8">	

        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
        <span style="<?php echo $DefaultStyle; ?>">
          (Last Reviewed By: 
  <?php
  if ($ReviewedInfo_num != 0) {
    echo $ReviewedInfo_row->FullName . " on " . date('m/d/Y', strtotime($$ReviewedInfo_row->ImmunizationReviewedDate)) . " " . date(' H:i s', strtotime($ReviewedInfo_row->ImmunizationReviewedDate));
  }
  ?>
          )
        </span>

      </td>
    </tr>
    <tr>
      <td width="7">&nbsp;</td>
      <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
        Date
      </td>
      <td width="4">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
        Dosage
      </td>
      <td width="4">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
        Dose
      </td>
      <td width="4">&nbsp;</td>
      <td align="left" style="width: 6.5in; <?php echo $ColumnHeaderStyle; ?>" valign="top">
        Vaccine
      </td>
    </tr>
    <tr>
      <td width="7">&nbsp;</td>
      <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
  <?php echo date('m/d/Y', strtotime($Immunizations_row->AdminDate)); ?>
      </td>
      <td width="4">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
  <?php echo $Immunizations_row->Dose . " " . $Immunizations_row->Units; ?>
      </td>
      <td width="4">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
  <?php echo $Immunizations_row->DoseNumber; ?>
      </td>
      <td width="4">&nbsp;</td>
      <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
  <?php echo trim($Immunizations_row->ImmDescription) . " " . trim($Immunizations_row->Immunization); ?>
      </td>
    </tr>
  <?php
  if (!empty($Immunizations_row->Notes)) {
    $ImmNotes = 1;
    ?>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td width="7">&nbsp;</td>
      <cfoutput>
        <td align="left" colspan="7" style="width: 7.0in; <?php echo $ColumnHeaderStyle; ?>" valign="top">
      </cfoutput>
      Notes
    </td>
    </tr>
    <?php
    if ($Immunizations_row->Notes != "") {
      ?>
      <tr>
        <td width="7">&nbsp;</td>
        <td align="left" nowrap style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
      <?php echo date('m/d/Y', strtotime($Immunizations_row->AdminDate)); ?>
        </td>
        <td width="4">&nbsp;</td>
        <td align="left" nowrap style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
      <?php echo $Immunizations_row->Immunization; ?>
        </td>
        <td width="4">&nbsp;</td>
        <td align="left" colspan="3" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
      <?php echo $Immunizations_row->Notes; ?>
        </td>
      </tr>
      <?php
    }
    ?>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <?php
  }
  ?>

  </table>

  <?php
}

$data['HeaderKey'] = $HeaderKey;
$data['PatientKey'] = $PatientKey;
$data['data_db'] = $data_db;
$this->load->view('encounter/comp_print/comp_newvaccinedue', $data);
endif;
?>
  