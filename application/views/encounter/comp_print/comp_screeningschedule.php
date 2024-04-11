<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="getAWACSScreening">
//	Select
//		Category,
//		Year1,
//		Year2,
//		Year3,
//		Year4,
//		Year5
//	From ecastmaster.dbo.AWACSScreeningMaster
//	Where (Severity = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="0">)
//		And (Hidden = <cfqueryparam cfsqltype="CF_SQL_BIT" value="0">)
//	Order by SortOrder
//</cfquery>

$sql = "	Select
		Category,
		Year1,
		Year2,
		Year3,
		Year4,
		Year5
	From ecastmaster.dbo.AWACSScreeningMaster
	Where (Severity = 0)
		And (Hidden = 0)
	Order by SortOrder";

$getAWACSScreening = $this->ReportModel->data_db->query($sql);
$getAWACSScreening_num = $getAWACSScreening->num_rows();
$getAWACSScreening_result = $getAWACSScreening->result();

//<!---
//<cfoutput>
//<cfdump var="#getAWACSResults#">
//</cfoutput>
//--->
//
//<cfif getAWACSScreening.RecordCount NEQ 0>
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
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//		<tr>
//			<td width="7">&nbsp;</td>
//			<cfoutput>
//			<td align="left" style="#variables.DefaultStyle#" valign="top">
//			</cfoutput>
//				The patient's written screening schedule and 5-year plan is as follows.
//			</td>
//		</tr>
//		<tr>
//			<td width="7">&nbsp;</td>
//			<td>
//				<table border="0" cellpadding="0" cellspacing="0" style="width: 6.75in; border-style:solid; border-collapse:collapse; border-width:3px; border-color: #999999; border-spacing:2px;">
//					<cfoutput>
//					<tr>
//						<td nowrap align="left" style="#variables.ColumnHeaderStyle# border-style:solid; border-width:3px; border-color: 999999; padding:2px;" valign="top">
//							CATEGORY
//						</td>
//						<td align="left" style="#variables.ColumnHeaderStyle# border-style:solid; border-width:3px; border-color: 999999; padding:2px;" valign="top">
//							YEAR1
//						</td>
//						<td align="left" style="#variables.ColumnHeaderStyle#; border-style:solid; border-width:3px; border-color: 999999; padding:2px;" valign="top">
//							YEAR2
//						</td>
//						<td align="left" style="#variables.ColumnHeaderStyle# border-style:solid; border-width:3px; border-color: 999999; padding:2px;" valign="top">
//							YEAR3
//						</td>
//						<td align="left" style="#variables.ColumnHeaderStyle#; border-style:solid; border-width:3px; border-color: 999999; padding:2px;" valign="top">
//							YEAR4
//						</td>
//						<td align="left" style="#variables.ColumnHeaderStyle# border-style:solid; border-width:3px; border-color: 999999; padding:2px;" valign="top">
//							YEAR5
//						</td>
//					</tr>
//					</cfoutput>
//					<cfoutput query="getAWACSScreening">
//						<tr>
//							<td nowrap align="left" style="#variables.DefaultStyle# border-style:solid; border-width:3px; padding:2px;" valign="center">
//								#getAWACSScreening.Category#&nbsp;
//							</td>
//							<td align="left" style="#variables.DefaultStyle# border-style:solid; border-width:3px; padding:2px;" valign="top">
//								#getAWACSScreening.Year1#&nbsp;
//							</td>
//							<td align="left" style="#variables.DefaultStyle# border-style:solid; border-width:3px; padding:2px;" valign="top">
//								#getAWACSScreening.Year2#&nbsp;
//							</td>
//							<td align="left" style="#variables.DefaultStyle# border-style:solid; border-width:3px; padding:2px;" valign="top">
//								#getAWACSScreening.Year3#&nbsp;
//							</td>
//							<td align="left" style="#variables.DefaultStyle# border-style:solid; border-width:3px; padding:2px;" valign="top">
//								#getAWACSScreening.Year4#&nbsp;
//							</td>
//							<td align="left" style="#variables.DefaultStyle# border-style:solid; border-width:3px; padding:2px;" valign="top">
//								#getAWACSScreening.Year5#&nbsp;
//							</td>
//						</tr>
//					</cfoutput>
//
//				</table>
//			</td>
//		</tr>
//	</table>
//</cfif>

if ($getAWACSScreening_num != 0) {

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
  $DefaultStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  $ColumnHeaderStyle = "color: #" . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: bold; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  ?>
  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
    <tr>
      <td width="7">&nbsp;</td>
    <cfoutput>
      <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
        The patient's written screening schedule and 5-year plan is as follows.
      </td>
      </tr>
      <tr>
        <td width="7">&nbsp;</td>
        <td>
          <table border="0" cellpadding="0" cellspacing="0" style="width: 6.75in; border-style:solid; border-collapse:collapse; border-width:3px; border-color: #999999; border-spacing:2px;">
            <tr>
              <td nowrap align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:3px;  padding:2px;" valign="top">
                CATEGORY
              </td>
              <?php
              //backup
              /*$b = '              <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:3px; border-color: 999999; padding:2px;" valign="top">
                YEAR1
              </td>
              <td align="left" style="<?php echo $ColumnHeaderStyle; ?>; border-style:solid; border-width:3px; border-color: 999999; padding:2px;" valign="top">
                YEAR2
              </td>
              <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:3px; border-color: 999999; padding:2px;" valign="top">
                YEAR3
              </td>
              <td align="left" style="<?php echo $ColumnHeaderStyle; ?>; border-style:solid; border-width:3px; border-color: 999999; padding:2px;" valign="top">
                YEAR4
              </td>
              <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:3px; border-color: 999999; padding:2px;" valign="top">
                YEAR5
              </td>';*/
              ?>
                <td align="left" style="<?php echo $ColumnHeaderStyle; ?> border-style:solid; border-width:3px;  padding:2px;" valign="top">
                GOALS
              </td>
            </tr>

            <?php foreach ($getAWACSScreening_result as $val) { ?>
              <tr>
                <td nowrap align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="center">
                  <?php echo $val->Category; ?>&nbsp;
                </td>
                <?php
                //backup
               /* $b = '<td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="top">
                  <?php echo $val->Year1; ?>&nbsp;
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="top">
                  <?php echo $val->Year2; ?>&nbsp;
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="top">
                  <?php echo $val->Year3; ?>&nbsp;
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="top">
                  <?php echo $val->Year4; ?>&nbsp;
                </td>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="top">
                  <?php echo $val->Year5; ?>&nbsp;
                </td>'; */
                ?>
                <td align="left" style="<?php echo $DefaultStyle; ?> border-style:solid; border-width:3px; padding:2px;" valign="top">
                  <?php echo $val->Year1; ?>&nbsp;
                </td>
              </tr>
            <?php } ?>

          </table>
          <?php
        }
        ?>
