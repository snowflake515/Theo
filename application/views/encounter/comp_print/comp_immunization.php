<?php
//<!--- Program Comp_Immunization.cfm
//
//	JWY 4/28/08 - Change query so that the Injections item are in "Abbreviated Name / Date Due" order
//	JWY 5/19/08 - Change query so that the join to VaccineInventory is on VaccineInv_Id, not Vaccine_Id
//
//--->
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.sUTC_DST=Session.UTC_DST>
//	<cfset Variables.sUTC_TimeOffset=Session.UTC_TimeOffset>
//</cflock>
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="ImmunizationNotes">
//	Select Distinct
//		I.Immunization_Id,
//		dbo.UTCToLocal(I.EncounterDate_UTC, <cfqueryparam cfsqltype="CF_SQL_NUMERIC" scale="2" value="#variables.sUTC_TimeOffset#">, <cfqueryparam cfsqltype="CF_SQL_BIT" value="#variables.sUTC_DST#">) as EncounterDate,
//		I.Notes,
//		I.Dosage,
//		I.DoseNumber,
//		V.VaccineName,
//		V.Abbreviation,
//		VI.Measurement
//	From Immunizations I,
//		Vaccines V,
//		VaccineInventory VI
//	Where I.Immunization_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And I.Vaccine_Id=V.Vaccine_Id
//		And I.VaccineInv_Id=VI.VaccineInv_Id
//		And (I.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR I.Hidden IS NULL)
//	Order By V.Abbreviation, EncounterDate
//</cfquery>

$ComponenKeyVar = $ComponentKey;  //"550";
$sql = "	Select Distinct
		I.Immunization_Id,
		I.EncounterDate_UTC as EncounterDate,
		I.Notes,
		I.Dosage,
		I.DoseNumber,
		V.VaccineName,
		V.Abbreviation,
		VI.Measurement
	From 
    " . $data_db . ".dbo.Immunizations I,
		" . $data_db . ".dbo.Vaccines V,
		" . $data_db . ".dbo.VaccineInventory VI
	Where I.Immunization_Id In ($ComponenKeyVar)
		And I.Vaccine_Id=V.Vaccine_Id
		And I.VaccineInv_Id=VI.VaccineInv_Id
		And (I.Hidden<>1 OR I.Hidden IS NULL)
	Order By V.Abbreviation, EncounterDate";

$ImmunizationNotes = $this->ReportModel->data_db->query($sql);
$ImmunizationNotes_num = $ImmunizationNotes->num_rows();
$ImmunizationNotes_row = $ImmunizationNotes->row();

//<cfset Variables.ImmNotes=0>
//<cfset Variables.Crlf=chr(13)&chr(10)>
//
//<cfif ImmunizationNotes.RecordCount NEQ 0>
//
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
//		<cfoutput>
//		<tr>
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
//				Injection
//			</td>
//		</tr>
//		</cfoutput>
//
//		<cfoutput query="ImmunizationNotes">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="left" style="#variables.DefaultStyle#" valign="top">
//					#DateFormat(ImmunizationNotes.EncounterDate,"mm/dd/yyyy")#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#ImmunizationNotes.Dosage# #ImmunizationNotes.Measurement#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#ImmunizationNotes.DoseNumber#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//					#Trim(ImmunizationNotes.VaccineName)# (#ImmunizationNotes.Abbreviation#)
//				</td>
//			</tr>
//			<cfif Trim(ImmunizationNotes.Notes) NEQ "">
//				<cfset Variables.ImmNotes=1>
//			</cfif>
//		</cfoutput>
//
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
//			<cfoutput query="ImmunizationNotes">
//				<cfif Trim(ImmunizationNotes.Notes) NEQ "">
//					<tr>
//						<td width="7">&nbsp;</td>
//						<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//							#DateFormat(ImmunizationNotes.EncounterDate,"mm/dd/yyyy")#
//						</td>
//						<td width="4">&nbsp;</td>
//						<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//							(#ImmunizationNotes.Abbreviation#)
//						</td>
//						<td width="4">&nbsp;</td>
//						<td align="left" colspan="3" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//							<cfset Variables.INotes=ReplaceNoCase(ImmunizationNotes.Notes,Variables.Crlf,"<br>","ALL")>
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
//
//<cfinclude template="comp_vaccinedue.cfm">

if ($ImmunizationNotes_num != 0) {
  //if (HeaderNeeded) { //SKIPP
  $data['HeaderKey'] = $HeaderKey;
  $data['PatientKey'] = $PatientKey;
  $data['HeaderMasterKey'] = $HeaderMasterKey;
  $data['FreeTextKey'] = $FreeTextKey;
  $data['SOHeaders'] = $SOHeaders;
  $this->load->view('encounter/print/componentheaders', $data);
  //}
  $data['data_db'] = $data_db;
  $BodyFontInfo = getBodyFontInfo($data, $HeaderKey);
  $DefaultStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  $ColumnHeaderStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: bold; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  ?>
  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <cfoutput>
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
          Injection
        </td>
      </tr>

      <tr>
        <td width="7">&nbsp;</td>
        <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php echo date('m/d/Y', strtotime($ImmunizationNotes_row->EncounterDate)); ?>
        </td>
        <td width="4">&nbsp;</td>
        <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php echo $ImmunizationNotes_row->Dosage . " " . $ImmunizationNotes_row->Measurement; ?>
        </td>
        <td width="4">&nbsp;</td>
        <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php echo $ImmunizationNotes_row->DoseNumber; ?>
        </td>
        <td width="4">&nbsp;</td>
        <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
          <?php echo trim($ImmunizationNotes_row->VaccineName) . " " . trim($ImmunizationNotes_row->Abbreviation); ?>
        </td>
      </tr>

      <?php
      if ($ImmunizationNotes_row->Notes != "") {
        $ImmNotes = 1;
      }
      if ($ImmNotes == 1) {
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
        if ($ImmunizationNotes_row->Notes != "") {
          ?>
          <tr>
            <td width="7">&nbsp;</td>
            <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
              <?php echo date('m-d-Y', strtotime($ImmunizationNotes_row->EncounterDate)); ?>
            </td>
            <td width="4">&nbsp;</td>
            <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
              <?php
              echo $ImmunizationNotes_row->Abbreviation;
              ?>
            </td>
            <td width="4">&nbsp;</td>
            <td align="left" colspan="3" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
              <?php
              echo trim($ImmunizationNotes_row->Notes);
              ?>
            </td>
          </tr>
        <?php } ?>
        <tr>
          <td>&nbsp;</td>
        </tr>

        <?php
      }
    }

    $this->load->view('encounter/comp_print/comp_vaccinedue', $data);
    ?>     
