<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="CompPatEd">
//	Select 
//		P.Title,
//		H.Date,
//		H.Type,
//		H.Notes
//	From PVHealthHistory H,
//		PTED_Toc P
//	Where H.PTED_Toc_Id=P.PTED_Toc_Id
//		And H.PVH_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And (H.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR H.Hidden IS NULL)
//	Order By H.Date DESC
//</cfquery>

//$ComponentKey = "7";
$sql = "	Select 
		P.Title,
		H.Date,
		H.Type,
		H.Notes
	From " . $data_db . ".dbo.PVHealthHistory H,
		" . $data_db . ".dbo.PTED_Toc P
	Where H.PTED_Toc_Id=P.PTED_Toc_Id
		And H.PVH_Id In ($ComponentKey)
		And (H.Hidden<>1 OR H.Hidden IS NULL)
	Order By H.Date DESC ";

$CompPatEd = $this->ReportModel->data_db->query($sql);
$CompPatEd_num = $CompPatEd->num_rows();
$CompPatEd_row = $CompPatEd->row();

//<cfif CompPatEd.RecordCount NEQ 0>
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
//		<cfoutput query="CompPatEd">
//			<tr>
//				<td width="12">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#DateFormat(CompPatEd.Date,"mm/dd/yyyy")#
//				</td>
//				<td width="12">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#Trim(CompPatEd.Title)#
//				</td>	
//				<td width="12">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					<cfif CompPatEd.Type EQ "P">
//						Printed
//					<cfelseif CompPatEd.Type EQ "E">
//						Emailed
//					</cfif>
//				</td>		
//				<td width="12">&nbsp;</td>
//				<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//					<cfset Variables.CmpNotes=ReplaceNoCase(CompPatEd.Notes,Variables.Crlf,"<br>","ALL")>
//					#Trim(Variables.CmpNotes)#
//				</td>
//			</tr>
//		</cfoutput>
//	</table>
//</cfif>
if ($CompPatEd_num != 0) {

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
      <td width="12">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php echo date('m/d/Y', strtotime($CompPatEd_row->Date)); ?>
      </td>
      <td width="12">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php echo trim($CompPatEd_row->Title); ?>
      </td>	
      <td width="12">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php
        if ($CompPatEd_row->Type == "P") {
          echo "Printed";
        } else if ($CompPatEd_row->Type == "E") {
          echo "Emailed";
        }
        ?>
      </td>		
      <td width="12">&nbsp;</td>
      <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
        <?php
        $tab_chr = array(chr(13), chr(10));
        $tmp = str_replace($tab_chr, '', $CompPatEd_row->Notes);
        echo trim($tmp);
        ?>
      </td>
    </tr>
  </table>

  <?php
}
?>
