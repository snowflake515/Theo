<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="HistoryNotes">
//	Select 
//		EncounterDate,
//		PFSHText
//		From PastFamilySocialHistory
//	Where PFS_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And (Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR Hidden IS NULL)
//	Order By EncounterDate DESC
//</cfquery>
$ComponenKeyVar = $ComponentKey; //"6824,6825,6826"; 
$sql = "Select 
		EncounterDate,
		PFSHText
		From " . $data_db . ".dbo.PastFamilySocialHistory
	Where PFS_Id In ($ComponenKeyVar)
		And (Hidden<>1 OR Hidden IS NULL)
	Order By EncounterDate DESC";


$HistoryNotes = $this->ReportModel->data_db->query($sql);
$HistoryNotes_num = $HistoryNotes->num_rows();
$HistoryNotes_result = $HistoryNotes->result();

//<cfif HistoryNotes.RecordCount NEQ 0>
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
//	<cfset Variables.Crlf = chr(13)&chr(10)>
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//		<cfoutput query="HistoryNotes">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="right" style="#variables.DefaultStyle#" valign="top">
//					#DateFormat(HistoryNotes.EncounterDate,"mm/dd/yyyy")#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//					<cfset Variables.HNotes=ReplaceNoCase(HistoryNotes.PFSHText,Variables.Crlf,"<br>","ALL")>
//					#Trim(Variables.HNotes)#
//				</td>
//			</tr>
//		</cfoutput>
//	</table>
//</cfif>

if ($HistoryNotes_num != 0) {
  //  if (HeaderNeeded) { //BLM DIKETAHUI DATA TSB
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
    <?php foreach ($HistoryNotes_result as $val) { ?>
      <tr>
        <td width="7">&nbsp;</td>
        <td align="right" style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php echo date('m/d/Y', strtotime($val->EncounterDate)); ?>
        </td>
        <td width="4">&nbsp;</td>
        <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
          <?php
          $HNotes = $val->PFSHText;
          echo $HNotes;
          ?>	
        </td>
      </tr>
    <?php } ?>


  </table>
  <?php } ?>