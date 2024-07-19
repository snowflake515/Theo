<?php
//<!--- CASE 10,032 (Sugar 112) - Do not print attachements that are flowsheets --->
//
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.sOrgId=Session.Org_Id>
//	<cfset Variables.sUserId=Session.User_Id>
//	<cfset Variables.sId=Session.Id>
//</cflock>
//
//<cfparam name="Attributes.DocumentDirectory" default="">
//
//<cfquery datasource="#Attributes.ImageDataSource#" name="CompAttachment">
//	SELECT	I.Attachments_Id,
//       		I.ImageType,
//	   		A.Description,
//	   		A.DateLastAccessed
//  	FROM	#Attributes.DSNPreFix#eCastEMR_Images.dbo.AttachmentsImages I,
//       		#Attributes.DSNPreFix#eCastEMR_Data.dbo.Attachments A
// 	WHERE	I.Attachments_Id IN (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//   			AND I.Attachments_Id=A.Attachments_Id
//			<!--- CASE 10,032 (Sugar 112) - Do not print attachements that are flowsheets --->
//			AND (A.EncounterFlowSheet IS NULL  OR A.EncounterFlowSheet <> <cfqueryparam cfsqltype="cf_sql_bit" value="1">)
//   
//</cfquery>

$ComponenKeyVar = $ComponentKey; //"3130,3141,3142"; 

$sql = "	SELECT	I.Attachments_Id,
       		I.ImageType,
	   		A.Description,
	   		A.DateLastAccessed
  	FROM " . $image_db . ".dbo.AttachmentsImages I,
       		" . $data_db . ".dbo.Attachments A
 	WHERE	I.Attachments_Id IN ($ComponenKeyVar)
   			AND I.Attachments_Id=A.Attachments_Id
			AND (A.EncounterFlowSheet IS NULL  OR A.EncounterFlowSheet <> 1)";

$CompAttachment = $this->ReportModel->data_db->query($sql);
$CompAttachment_num = $CompAttachment->num_rows();
$CompAttachment_result = $CompAttachment->result();

//<cfif CompAttachment.RecordCount NEQ 0>
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
//	<cfset Variables.LoopCount = 1>
//	<table cellpadding="0" cellspacing="0" border="0" style="width: 7.0in;">
//		<tr>
//			<cfoutput>
//			<td width="7">&nbsp;</td>
//			<td align="left" style="#variables.ColumnHeaderStyle#">
//				Date
//			</td>
//			<td align="left" style="width: 6.5in; #variables.ColumnHeaderStyle#">
//				Description
//			</td>
//			</cfoutput>
//		</tr>
//
//		<cfloop query="CompAttachment">
//			<cfoutput>
//				<tr>
//					<td width="7">&nbsp;</td>
//					<td align="left" style="#variables.DefaultStyle#">
//						#DateFormat(CompAttachment.DateLastAccessed, "mm/dd/yyyy")#
//					</td>
//					<td align="left" style="width: 6.5in; #variables.DefaultStyle#">
//						#Trim(Left(CompAttachment.Description,100))#
//					</td>
//				</tr>
//			</cfoutput>
//		</cfloop>
//	</table>
//</cfif>

if ($CompAttachment_num != 0) {
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
  $ColumnHeaderStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: bold; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  ?>
  <table cellpadding="0" cellspacing="0" border="0" style="width: 7.0in;">
    <tr>
      <td width="7">&nbsp;</td>
      <td align="left" style="<?php echo $ColumnHeaderStyle; ?>">
        Date
      </td>
      <td align="left" style="width: 6.5in; <?php echo $ColumnHeaderStyle; ?>">
        Description
      </td>
  </tr>

  <?php foreach ($CompAttachment_result as $val) { ?>
    <tr>
      <td width="7">&nbsp;</td>
      <td align="left" style="<?php echo $DefaultStyle; ?>">
        <?php echo date('m/d/Y', strtotime($val->DateLastAccessed)); ?>
      </td>
      <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>">
        <?php echo trim(substr($val->Description, 0, 100)); ?>
      </td>
    </tr>
  <?php } ?>

  </table>
<?php } ?>