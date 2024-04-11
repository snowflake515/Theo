<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="RecallNotes">
//	Select 
//		DateEntered,
//		DateNotified,
//		Description,
//		Notes
//	From Recall
//	Where Recall_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And (Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR Hidden IS NULL)
//	Order By DateEntered DESC
//</cfquery>

//$ComponentKey = "2095";
$sql = "	Select 
		DateEntered,
		DateNotified,
		Description,
		Notes
	From " . $data_db . ".dbo.Recall
	Where Recall_Id In ($ComponentKey)
		And (Hidden<>1 OR Hidden IS NULL)
	Order By DateEntered DESC";


$RecallNotes = $this->ReportModel->data_db->query($sql);
$RecallNotes_num = $RecallNotes->num_rows();
$RecallNotes_row = $RecallNotes->row();


//<cfif RecallNotes.RecordCount NEQ 0>
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
//	<cfset Variables.Crlf=chr(13)&chr(10)>
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//		<cfoutput query="RecallNotes">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="right" nowrap style="#variables.DefaultStyle#" valign="top">
//					#DateFormat(RecallNotes.DateEntered,"mm/dd/yyyy")# - #DateFormat(RecallNotes.DateNotified,"mm/dd/yyyy")#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="right" nowrap style="#variables.DefaultStyle#" valign="top">
//					#Trim(RecallNotes.Description)#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//					<cfset CNotes=ReplaceNoCase(RecallNotes.Notes,Variables.Crlf,"<br>","ALL")>
//					#Trim(Variables.CNotes)#
//				</td>
//			</tr>
//		</cfoutput>
//	</table>
//</cfif>


if ($RecallNotes_num != 0) {

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
  ?>
  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <tr>
      <td width="7">&nbsp;</td>
      <td align="right" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php echo date('m/d/Y', strtotime($RecallNotes_row->DateEntered)) . " - " . date('m/d/Y', strtotime($RecallNotes_row->DateNotified)); ?>
      </td>
      <td width="4">&nbsp;</td>
      <td align="right" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php echo trim($RecallNotes_row->Description); ?>
      </td>
      <td width="4">&nbsp;</td>
      <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
        <?php
        $tab_chr = array(chr(13), chr(10));
        $tmp = str_replace($tab_chr, '', $RecallNotes_row->Notes);
        echo trim($tmp);
        ?>
      </td>
    </tr>
  </table>
  <?php
}
?>