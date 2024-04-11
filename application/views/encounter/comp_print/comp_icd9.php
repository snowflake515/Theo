<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="CompICD9">
//	Select 
//		I.ICD9Code,
//		I.ICD9Description
//	From ICD9Master I,
//		EncounterAssessment E
//	Where I.ICD9_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And (E.Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PrimaryKey#">)
//		And (I.ICD9_Id=E.ICD9_Id)
//	Order By E.DX_Pointer
//</cfquery>

$ComponenKeyVar = $ComponentKey; //"4420,8069";
//$PrimaryKey = 474406;

$sql = "	Select 
		I.ICD9Code,
		I.ICD9Description
	From " . $data_db . ".dbo.ICD9Master I,
		" . $data_db . ".dbo.EncounterAssessment E
	Where I.ICD9_Id In ($ComponenKeyVar)
		And (E.Encounter_Id=$PrimaryKey) 
		And (I.ICD9_Id=E.ICD9_Id)
	Order By E.DX_Pointer";

$CompICD9 = $this->ReportModel->data_db->query($sql);
$CompICD9_num = $CompICD9->num_rows();
$CompICD9_result = $CompICD9->result();

//<cfif CompICD9.RecordCount NEQ 0>
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
//	
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//		<cfoutput query="CompICD9">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="right" nowrap style="#variables.DefaultStyle#" valign="top">
//					#Trim(CompICD9.ICD9Code)# - 
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//					#Trim(CompICD9.ICD9Description)#
//				</td>
//			</tr>
//		</cfoutput>
//	</table>
//</cfif>

if ($CompICD9_num != 0) {
  //  if (HeaderNeeded) { //SKIPP
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
  ?>

  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
  <?php foreach ($CompICD9_result as $val) { ?>
      <tr>
        <td width="7">&nbsp;</td>
        <td align="right" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
    <?php echo trim($val->ICD9Code) . " - "; ?>
        </td>
        <td width="4">&nbsp;</td>
        <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
    <?php echo trim($val->ICD9Description); ?>
        </td>
      </tr>
  <?php } ?>
  </table>

<?php } ?>
