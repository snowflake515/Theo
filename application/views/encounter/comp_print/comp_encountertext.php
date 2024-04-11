<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="EText">
//	Select TOP 1
//		EncounterText,
//		ComponentKeys
//	From EncounterComponents
//	Where EncounterComponents_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.EncounterComponentKey#">
//</cfquery>


$sql = "Select TOP 1
		EncounterText,
		ComponentKeys
	From  " . $data_db . ".dbo.EncounterComponents
	Where EncounterComponents_Id=$EncounterComponentKey";

$EText = $this->ReportModel->data_db->query($sql);
$EText_num = $EText->num_rows();
$EText_row = $EText->row();


//<cfif trim(EText.EncounterText) NEQ 0>
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
//	<cfif EText.ComponentKeys EQ 0>
//		<br>
//	</cfif>
//
//	<cfset Variables.Crlf=chr(13)&chr(10)>
//	<cfif FindNoCase("<table", EText.EncounterText) NEQ 0> 
//		<cfset Variables.EncText = EText.EncounterText> 
//	<cfelse>
//		<cfset Variables.EncText = ReplaceNoCase(Etext.EncounterText,Variables.Crlf,"<br>","ALL")>
//	</cfif>
//	
//	<cfoutput>
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//		<tr>
//			<td width="7">&nbsp;</td>
//			<td style="#variables.DefaultStyle#">
//				#Trim(Variables.EncText)#
//			</td>
//		</tr>
//	</table>
//	</cfoutput>
//</cfif>

if (trim($EText_row->EncounterText) != "") {

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

  if ($EText_row->ComponentKeys == 0) {
//      echo "<br/>";
  }
  ?>
  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <tr>
      <td width="7">&nbsp;</td>
      <td style="<?php echo $DefaultStyle; ?>">
        <?php
        echo $EText_row->EncounterText;
        ?>
      </td>
    </tr>
  </table>
  <?php
}
?>
