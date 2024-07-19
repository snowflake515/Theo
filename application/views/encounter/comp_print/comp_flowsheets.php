<?php
//<!---
//		Program: Comp_FlowSheets.cfm
//
//		Change Log
//		CH 9/13/2011 - CASE 10,032(Sugar 112) (Created)
//		CH 2/9/2012 - CASE 487 Added Memo Field
//
//--->
//
//<cflock scope="Session" type="EXCLUSIVE" timeout="30">
//	<cfset variables.sPatientId=Session.Patient_Id>
//	<cfset variables.sOrgId=Session.Org_Id>
//	<cfset Variables.sUTC_DST = session.UTC_DST>
//	<cfset Variables.sUTC_TimeOffset = Session.UTC_TimeOffset>
//	<cfset Variables.sUTC_TimeZoneId = Session.UTC_TimeZoneId>	
//	
//	<cfset Variables.sUserId=Session.User_Id>
//	<cfset Variables.sId=Session.Id>	
//</cflock>
//
//
//<cfparam name="caller.HeaderNeeded" default="False">
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="CompFlowSheets">
//	SELECT 	f.Title,
//			MIN(d.datadate) as firstdate,
//			fs.Flowsheet_id,
//			<!--- CASE 487 Added FlowSheetNotes --->
//			fs.FlowSheetNotes
//	FROM	FlowSheetDefinitions f,
//			FlowSheetDates d,
//			FlowSheets fs
//	WHERE	fs.FlowSheet_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//			AND fs.FSDefinition_id = f.FSDefinition_id
//			AND fs.Flowsheet_id = d.Flowsheet_id
//			AND(fs.Hidden <> <cfqueryparam cfsqltype="cf_sql_bit" value="1"> OR fs.Hidden is NULL)
//	<!--- CASE 487 Added FlowSheetNotes to Group By--->		
//	GROUP BY    fs.FlowSheet_Id, Title, fs.FlowSheetNotes 
//	ORDER BY firstdate desc
//</cfquery>

//$ComponentKey
$ComponenKeyVar = $ComponentKey; //"77,75";

$sql = "	SELECT 	f.Title,
			MIN(d.datadate) as firstdate,
			fs.Flowsheet_id,
			fs.FlowSheetNotes
	FROM	
      " . $data_db . ".dbo.FlowSheetDefinitions f,
			" . $data_db . ".dbo.FlowSheetDates d,
			" . $data_db . ".dbo.FlowSheets fs
	WHERE	fs.FlowSheet_Id In ($ComponenKeyVar)
			AND fs.FSDefinition_id = f.FSDefinition_id
			AND fs.Flowsheet_id = d.Flowsheet_id
			AND(fs.Hidden <> 1 OR fs.Hidden is NULL)
	GROUP BY    fs.FlowSheet_Id, Title, fs.FlowSheetNotes 
	ORDER BY firstdate desc";

$CompFlowSheets = $this->ReportModel->data_db->query($sql);
$CompFlowSheets_num = $CompFlowSheets->num_rows();
$CompFlowSheets_result = $CompFlowSheets->result();


//<cfparam name="Variables.LoopCount" default="1">
//
//<cfif CompFlowSheets.RecordCount NEQ 0>
//
//	<cfif caller.HeaderNeeded EQ True>
//		<br>
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
//	<table cellpadding="0" cellspacing="0" border="0" style="width: 7.0in;">
//		<cfif isDefined('Attributes.DisplayTitle')>
//			<tr>
//				<!--- CASE 487 Changed colspan from 2 to 6 --->
//				<td colspan="6" nowrap align="left" style="font-size: 14px; color: Maroon; font-weight: bold; font-family: Times New Roman;" valign="top">
//					<cfoutput>
//						#Attributes.DisplayTitle#
//					</cfoutput>
//				</td>
//			</tr>
//		</cfif>
//	
//		<tr>
//			<cfoutput>
//				<td width="7">&nbsp;</td>
//				<td align="left" valign="top" style="#variables.ColumnHeaderStyle#">
//					Date
//				</td>
//				<td width="7">&nbsp;</td>
//				<!--- CASE 487 Changed width from 6.5 to 3.2 --->
//				<td align="left" valign="top" style="width: 3.2in; #variables.ColumnHeaderStyle#">
//					Description
//				</td>
//				<!--- CASE 487 Added tds --->
//				<td width="7">&nbsp;</td>
//				<td align="left" style="width: 3.2in; #variables.ColumnHeaderStyle#">
//					Header Information <p />
//				</td>
//			</cfoutput>
//		</tr>
//
//		<cfloop query="CompFlowSheets">
//			<cfoutput>
//				<tr>
//					<td width="7">&nbsp;</td>
//					<td align="left" valign="top" style="#variables.DefaultStyle#">
//						#DateFormat(CompFlowSheets.firstdate, "mm/dd/yyyy")#
//					</td>
//					<td width="7">&nbsp;</td>
//					<!--- CASE 487 Changed width from 6.5 to 3.2 --->
//					<td align="left" valign="top" style="width: 3.2in; #variables.DefaultStyle#">
//						#Trim(Left(CompFlowSheets.Title,100))#
//					</td>
//					<!--- CASE 487 Added tds --->
//					<td width="7">&nbsp;</td>
//					<td valign="top" align="left" style="width: 3.2in; #variables.DefaultStyle#">
//						#Trim(CompFlowSheets.FlowSheetNotes)#
//					</td>					
//				</tr>
//				<!--- CASE 487 Added tr --->
//				<tr><td>&nbsp;</td></tr>
//			</cfoutput>
//		</cfloop>
//	</table>
//	
//</cfif>

if ($CompFlowSheets_num != 0) {
  //if (HeaderNeeded) { //SKIPP
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
    <?php
    //SKIPP
    //if ($DisplayTitle) {
    ?>
    <tr>
      <!--- CASE 487 Changed colspan from 2 to 6 --->
      <td colspan="6" nowrap align="left" style="font-size: 14px; color: Maroon; font-weight: bold; font-family: Times New Roman;" valign="top">
        <?php echo $DisplayTitle; ?>
      </td>
    </tr>
    <?php //} ?>
    <tr>

      <td width="7">&nbsp;</td>
      <td align="left" valign="top" style="<?php echo $ColumnHeaderStyle; ?>">
        Date
      </td>
      <td width="7">&nbsp;</td>
      <!--- CASE 487 Changed width from 6.5 to 3.2 --->
      <td align="left" valign="top" style="width: 3.2in; <?php echo $ColumnHeaderStyle; ?>">
        Description
      </td>
      <!--- CASE 487 Added tds --->
      <td width="7">&nbsp;</td>
      <td align="left" style="width: 3.2in; <?php echo $ColumnHeaderStyle; ?>">
        Header Information <p />
      </td>
    </tr>
    <?php foreach ($CompFlowSheets_result as $val) { ?>
      <tr>
        <td width="7">&nbsp;</td>
        <td align="left" valign="top" style="<?php echo $DefaultStyle; ?>">
          <?php echo date('m/d/Y', strtotime($val->firstdate)); ?>
        </td>
        <td width="7">&nbsp;</td>
        <!--- CASE 487 Changed width from 6.5 to 3.2 --->
        <td align="left" valign="top" style="width: 3.2in; <?php echo $DefaultStyle; ?>">
          <!--#Trim(Left(CompFlowSheets.Title,100))#-->
          <?php echo trim(substr($val->Title, 0, 100)); ?>
        </td>
        <!--- CASE 487 Added tds --->
        <td width="7">&nbsp;</td>
        <td valign="top" align="left" style="width: 3.2in; <?php echo $DefaultStyle; ?>">
          <!--#Trim(CompFlowSheets.FlowSheetNotes)#-->
          <?php echo trim($val->FlowSheetNotes); ?>
        </td>					
      </tr>
      <!--- CASE 487 Added tr --->
      <tr><td>&nbsp;</td></tr>
    <?php } ?>
  </table>

<?php } ?>

