<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="CompProtocols">
//	Select 
//		P.Name,
//		A.DueDate
//	From ProtocolAlerts A
//	Join Protocol P
//		On A.Protocol_Id=P.Protocol_Id
//	Where A.Patient_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PatientKey#">
//		And (A.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR A.Hidden IS NULL)
//		And A.CompleteDate IS NULL
//	Order By A.DueDate Desc
//</cfquery>



$sql = "	Select 
		P.Name,
		A.DueDate
	From " . $data_db . ".dbo.ProtocolAlerts A
	Join " . $data_db . ".dbo.Protocol P
		On A.Protocol_Id=P.Protocol_Id
	Where A.Patient_Id=$PatientKey
		And (A.Hidden<>1 OR A.Hidden IS NULL)
		And A.CompleteDate IS NULL
	Order By A.DueDate Desc";


$CompProtocols = $this->ReportModel->data_db->query($sql);
$CompProtocols_num = $CompProtocols->num_rows();
$CompProtocols_row = $CompProtocols->row();


//<cfif CompProtocols.RecordCount NEQ 0>
//	<br>
//	<cfif Caller.HeaderNeeded EQ True>
//		<cfmodule template="componentheaders.cfm"
//		 EMRDataSource="#Attributes.EMRDataSource#"
//		 HeaderKey="#Attributes.HeaderKey#"
//		 PatientKey="#Attributes.PatientKey#"
//		 HeaderMasterKey="#Attributes.HeaderMasterKey#"
//		 FreeTextKey="#Attributes.FreeTextKey#"
//		 SOHeaders="#Attributes.SOHeaders#">
//		<cfset Caller.HeaderNeeded = False>
//		<cfset Caller.NeedTemplateHeader = False>
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
//	<table cellpadding="0" cellspacing="0">
//		<cfoutput query="CompProtocols">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#DateFormat(CompProtocols.DueDate,"mm/dd/yyyy")#
//				</td>
//				<td width="10">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#CompProtocols.Name#
//				</td>
//			</tr>	
//		</cfoutput>	
//	</table>
//</cfif>



if ($CompProtocols_num != 0) {

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
  <table cellpadding="0" cellspacing="0">
    <cfoutput query="CompProtocols">
      <tr>
        <td width="7">&nbsp;</td>
        <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php echo date('m/d/Y', strtotime($CompProtocols_row->DueDate)); ?>
        </td>
        <td width="10">&nbsp;</td>
        <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php echo $CompProtocols_row->Name; ?>
        </td>
      </tr>	
    </cfoutput>	
  </table>
  <?php
}
?>