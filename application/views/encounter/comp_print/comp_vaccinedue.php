<?php
//<!--- Program Comp_VaccineDue.cfm
//
//	JWY 4/28/08 - Change query so that the Injections items are in "Abbreviated Name / Date Due" order
//
//--->
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="VacDue">
//	Select 
//		V.VaccineName,
//		V.Abbreviation,
//		D.DueDate,
//		D.Measurement,
//		D.DosageDue,
//		D.DoseNumber,
//		D.Notes
//	From VaccinesDue D,
//		Vaccines V
//	Where Patient_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PatientKey#">
//		And VaccineGiven=0
//		And D.Vaccine_Id=V.Vaccine_Id
//		And (V.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR V.Hidden IS NULL)
//		And (D.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR D.Hidden IS NULL)
//	Order By V.Abbreviation, D.DueDate
//</cfquery>

//$PatientKey = 2594400;
$sql = "Select 
		V.VaccineName,
		V.Abbreviation,
		D.DueDate,
		D.Measurement,
		D.DosageDue,
		D.DoseNumber,
		D.Notes
	From VaccinesDue D,
		Vaccines V
	Where Patient_Id=$PatientKey
		And VaccineGiven=0
		And D.Vaccine_Id=V.Vaccine_Id
		And (V.Hidden<>1 OR V.Hidden IS NULL)
		And (D.Hidden<>1 OR D.Hidden IS NULL)
	Order By V.Abbreviation, D.DueDate";

$VacDue = $this->ReportModel->data_db->query($sql);
$VacDue_num = $VacDue->num_rows();
$VacDue_row = $VacDue->row();

//<cfif VacDue.RecordCount NEQ 0>
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
//	<cfset Variables.Vacnotes=0>
//	<cfset Variables.Crlf=chr(13)&chr(10)>
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//		<cfoutput>
//		<tr>
//			<td width="7">&nbsp;</td>
//			<td align="left" colspan="7" style="#variables.ColumnHeaderStyle#" valign="top">
//				Vaccines Due
//			</td>
//		</tr>
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
//				Vaccine
//			</td>
//		</tr>
//		</cfoutput>
//		
//		<cfoutput query="VacDue">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="left" style="#variables.DefaultStyle#" valign="top">
//					#DateFormat(VacDue.DueDate,"mm/dd/yyyy")#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#VacDue.DosageDue# #VacDue.Measurement#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#VacDue.DoseNumber#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//					#Trim(VacDue.VaccineName)# (#VacDue.Abbreviation#)
//				</td>
//			</tr>
//			<cfif Trim(VacDue.Notes) NEQ "">
//				<cfset Variables.VacNotes=1>
//			</cfif>
//		</cfoutput>
//
//		<cfif Variables.VacNotes EQ 1>
//			<tr>
//				<td width="7">&nbsp;</td>
//				<cfoutput>
//				<td align="left" colspan="7" style="width: 7.0in; #variables.ColumnHeaderStyle#" valign="top">
//				</cfoutput>
//					Notes
//				</td>
//			</tr>
//			<cfoutput query="VacDue">
//				<cfif Trim(VacDue.Notes) NEQ "">
//					<tr>
//						<td width="7">&nbsp;</td>
//						<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//							#DateFormat(VacDue.DueDate,"mm/dd/yyyy")#
//						</td>
//						<td width="4">&nbsp;</td>
//						<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//							(#VacDue.Abbreviation#)
//						</td>
//						<td width="4">&nbsp;</td>
//						<td align="left" colspan="3" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//							<cfset Variables.VNotes=ReplaceNoCase(VacDue.Notes,Variables.Crlf,"<br>","ALL")>
//							#Trim(Variables.VNotes)#
//						</td>
//					</tr>
//				</cfif>
//			</cfoutput>
//		</cfif>
//	</table>
//</cfif>
if ($VacDue_num != 0) {
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
    <tr>
      <td width="7">&nbsp;</td>
      <td align="left" colspan="7" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
        Vaccines Due
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
        <?php echo date('m/d/Y', strtotime($VacDue_row->DueDate)); ?>
      </td>
      <td width="4">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php echo $VacDue_row->DosageDue . " " . $VacDue_row->Measurement; ?>
      </td>
      <td width="4">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php echo $VacDue_row->DoseNumber; ?>
      </td>
      <td width="4">&nbsp;</td>
      <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
        <?php echo $VacDue_row->VaccineName . " " . $VacDue_row->Abbreviation; ?>
      </td>
    </tr>

    <?php
    if ($VacDue_row->VaccineName != "") {
      $VacNotes = 1;
      ?>
      <tr>
        <td width="7">&nbsp;</td>
      <cfoutput>
        <td align="left" colspan="7" style="width: 7.0in; <?php echo $ColumnHeaderStyle; ?>" valign="top">
      </cfoutput>
      Notes
    </td>
    </tr>
    <cfif Trim(VacDue.Notes) NEQ "">
    <?php
    if ($VacDue_row->Notes != "") {
      ?>
            <tr>
          <td width="7">&nbsp;</td>
          <td align="left" nowrap style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
            <?php echo date('m/d/Y', strtotime($VacDue_row->DueDate)); ?>
          </td>
          <td width="4">&nbsp;</td>
          <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
            <?php echo $VacDue_row->Abbreviation; ?>
          </td>
          <td width="4">&nbsp;</td>
          <td align="left" colspan="3" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
            <?php echo trim($VacDue_row->Notes); ?>
          </td>
        </tr>

        <?php
      }
    }
    ?>
  </table>
  <?php
}
?>