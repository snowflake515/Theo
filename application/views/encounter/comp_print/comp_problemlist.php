<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="ProblemNotes">
//	Select 
//		ProblemDescription,
//		ProblemDate,
//		Chronic
//	From ProblemList
//	Where Problem_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And (Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR Hidden IS NULL)
//	Order By ProblemDate DESC
//</cfquery>


//$ComponentKey = "551";

$sql = "Select 
		ProblemDescription,
		ProblemDate,
		Chronic
	From " . $data_db . ".dbo.ProblemList
	Where Problem_Id In ($ComponentKey)
		And (Hidden<>1 OR Hidden IS NULL)
	Order By ProblemDate DESC";

$ProblemNotes = $this->ReportModel->data_db->query($sql);
$ProblemNotes_num = $ProblemNotes->num_rows();
$ProblemNotes_row = $ProblemNotes->row();

//<cfif ProblemNotes.RecordCount NEQ 0>
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
//		<cfoutput query="ProblemNotes">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="right" style="#variables.DefaultStyle#" valign="top">
//					#DateFormat(ProblemNotes.ProblemDate,"mm/dd/yyyy")#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//					#Trim(ProblemNotes.ProblemDescription)# <cfif ProblemNotes.Chronic EQ 1><span style="font-weight: bold;">(Chronic)</span></cfif>
//				</td>
//			</tr>
//		</cfoutput>
//	</table>
//</cfif>

if ($ProblemNotes_num != 0) {

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
      <td align="right" style="<?php echo $DefaultStyle; ?>" valign="top">
  <?php echo date('m/d/Y', strtotime($ProblemNotes_row->ProblemDate)); ?>
      </td>
      <td width="4">&nbsp;</td>
      <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
  <?php
  echo $ProblemNotes_row->ProblemDescription;
  if ($ProblemNotes_row->Chronic == 1) {
    echo '<span style="font-weight: bold;">(Chronic)</span>';
  }
  ?>
      </td>
    </tr>
  </table>

  <?php
}
?>