<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="AlertNotes">
//Select AlertDate,
//       AlertNote
//  From Alerts
// Where Alert_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
// Order By AlertDate DESC
//</cfquery>

$ComponenKeyVar = $ComponentKey;
//$ComponenKeyVar = "60,666,21303"; //Embeded ;

$sql = "Select AlertDate,
       AlertNote
  From " . $data_db . ".dbo.Alerts
 Where Alert_Id In ($ComponenKeyVar)
 Order By AlertDate DESC";

$AlertNotes = $this->ReportModel->data_db->query($sql);
$AlertNotes_num = $AlertNotes->num_rows();
$AlertNotes_result = $AlertNotes->result();

//<cfif AlertNotes.RecordCount NEQ 0>
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
//		<cfoutput query="AlertNotes">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="right" style="#variables.DefaultStyle#" valign="top">
//					#DateFormat(AlertNotes.AlertDate,"mm/dd/yyyy")#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//					<cfset Variables.ANotes=ReplaceNoCase(AlertNotes.AlertNote,Variables.Crlf,"<br>","ALL")>
//					#Trim(Variables.ANotes)#
//				</td>
//			</tr>
//		</cfoutput>
//	</table>
//</cfif>

if ($AlertNotes_num != 0) {

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
    <?php foreach ($AlertNotes_result as $val) { ?>
      <tr>
        <td width="7">&nbsp;</td>
        <td align="right" style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php echo date('m/d/Y', strtotime($val->AlertDate)); ?>
        </td>
        <td width="4">&nbsp;</td>
        <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
          <!--<cfset Variables.ANotes=ReplaceNoCase(AlertNotes.AlertNote,Variables.Crlf,"<br>","ALL")>-->
          <?php
          $ANotes = $val->AlertNote;
          echo $ANotes;
          ?>				
        </td>
      </tr>
    <?php } ?>
  </table>


  <?php
}

?>