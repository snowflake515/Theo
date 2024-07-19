<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="VisitNotes">
//	Select 
//		EncounterDate,
//		EncounterDescription,
//		ChiefComplaint,
//		EncounterNotes
//	From EncounterHistory
//	Where Encounter_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And (Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR Hidden IS NULL)
//		And Encounter_Id<><cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PrimaryKey#">
//	Order BY EncounterDate DESC
//</cfquery>
//$ComponentKey
$ComponenKeyVar = $ComponentKey; //"496549";

$sql = "Select 
		EncounterDate,
		EncounterDescription,
		ChiefComplaint,
		EncounterNotes
	From " . $data_db . ".dbo.EncounterHistory
	Where Encounter_Id In ($ComponenKeyVar)
		And (Hidden<>1 OR Hidden IS NULL)
		And Encounter_Id<>$PrimaryKey
	Order BY EncounterDate DESC";


$VisitNotes = $this->ReportModel->data_db->query($sql);
$VisitNotes_num = $VisitNotes->num_rows();
$VisitNotes_result = $VisitNotes->result();

//<cfif VisitNotes.RecordCount NEQ 0>
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
//	<cfset Variables.Crlf=chr(13)&chr(10)>
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//		<cfoutput query="VisitNotes">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//					#DateFormat(VisitNotes.EncounterDate,"mm/dd/yyyy")# &nbsp;&nbsp;&nbsp;&nbsp;#Trim(VisitNotes.EncounterDescription)#
//				</td>
//			</tr>
//			<cfif Trim(VisitNotes.ChiefComplaint) NEQ "">
//				<tr>
//					<td width="7">&nbsp;</td>
//					<td align="left" style="width: 7.0in; #variables.ColumnHeaderStyle#" valign="top">
//						#Trim(VisitNotes.ChiefComplaint)#
//					</td>
//				</tr>
//			</cfif>
//			<cfif Trim(VisitNotes.EncounterNotes) NEQ "">
//				<tr>
//					<td width="7">&nbsp;</td>
//					<td align="left" style="width: 7.0in;" valign="top">
//						<cfset Variables.ENotes=ReplaceNoCase(VisitNotes.EncounterNotes,Variables.Crlf,"<br>","ALL")>
//						#Trim(Variables.ENotes)#
//					</td>
//				</tr>
//			</cfif>
//			<cfif VisitNotes.RecordCount NEQ VisitNotes.CurrentRow>
//				<tr>
//					<td>&nbsp;</td>
//				</tr>
//			</cfif>
//		</cfoutput>
//	</table>
//</cfif>

if ($VisitNotes_num != 0) {
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
  $ColumnHeaderStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: bold; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  ?>

  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <?php foreach ($VisitNotes_result as $val) { ?>
      <tr>
        <td width="7">&nbsp;</td>
        <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
          <?php echo date('m/d/Y', strtotime($val->EncounterDate)); ?> &nbsp;&nbsp;&nbsp;&nbsp;
          <?php echo trim($val->EncounterDescription); ?>
        </td>
      </tr>
      <?php if ($val->ChiefComplaint != "") { ?>
        <tr>
          <td width="7">&nbsp;</td>
          <td align="left" style="width: 7.0in; <?php echo $ColumnHeaderStyle; ?>" valign="top">
            <?php echo trim($val->ChiefComplaint); ?>
          </td>
        </tr>
      <?php } ?>

      <?php if ($val->EncounterNotes != "") { ?>
        <tr>
          <td width="7">&nbsp;</td>
          <td align="left" style="width: 7.0in;" valign="top">
            <?php echo trim($val->ENotes); ?>
          </td>
        </tr>
      <?php } ?>
      <tr>
        <td>&nbsp;</td>
      </tr>
    <?php } ?>
  </table>

<?php } ?>