<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="CompResults">
//	SELECT 
//		RH.EncounterDate,
//		RD.ResultsTestName,
//		RD.ResultsTestResults,
//		RD.ResultsTestLowRange,
//		RD.ResultsTestHighRange, 
//		RD.ResultsTestNotes,
//		RD.ResultsUnits,
//		RD.ResultsTestComments
//	FROM ResultsHistory RH
//	JOIN ResultsDetails RD
//		ON RD.ResultsHistory_ID=RH.ResultsHistory_ID
//	WHERE RD.ResultsHistory_ID In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And (RD.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR RD.Hidden IS NULL)
//	Order By RH.EncounterDate DESC
//</cfquery>

//$ComponentKey = "3";
$sql = "	SELECT 
		RH.EncounterDate,
		RD.ResultsTestName,
		RD.ResultsTestResults,
		RD.ResultsTestLowRange,
		RD.ResultsTestHighRange, 
		RD.ResultsTestNotes,
		RD.ResultsUnits,
		RD.ResultsTestComments
	FROM " . $data_db . ".dbo.ResultsHistory RH
	JOIN " . $data_db . ".dbo.ResultsDetails RD
		ON RD.ResultsHistory_ID=RH.ResultsHistory_ID
	WHERE RD.ResultsHistory_ID In ($ComponentKey)
		And (RD.Hidden<>1 OR RD.Hidden IS NULL)
	Order By RH.EncounterDate DESC";

$CompResults = $this->ReportModel->data_db->query($sql);
$CompResults_num = $CompResults->num_rows();
$CompResults_row = $CompResults->row();

//<cfif CompResults.RecordCount NEQ 0>
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
//	<cfset Variables.RNote=0>
//	<cfset Variables.Crlf=chr(13)&chr(10)>
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;" border="0">
//		<cfoutput>
//		<tr>
//			<td width="7">&nbsp;</td>
//			<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//				Date
//			</td>
//			<td width="7">&nbsp;</td>
//			<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//				Test
//			</td>	
//			<td width="7">&nbsp;</td>
//			<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//				Results
//			</td>	
//			<td width="7">&nbsp;</td>
//			<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//				Low
//			</td>	
//			<td width="7">&nbsp;</td>
//			<td align="left" style="#variables.ColumnHeaderStyle#" valign="top">
//				High
//			</td>	
//			<td width="7">&nbsp;</td>
//			<td align="left" style="width: 6.5in; #variables.ColumnHeaderStyle#" valign="top">
//				Comments
//			</td>
//		</tr>
//		</cfoutput>
//
//		<cfoutput query="CompResults">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#DateFormat(CompResults.EncounterDate,"mm/dd/yyyy")#
//				</td>
//				<td width="7">&nbsp;</td>
//				<td align="left" style="#variables.DefaultStyle#" valign="top">
//					#CompResults.ResultsTestName#
//				</td>	
//				<td width="7">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#CompResults.ResultsTestResults#&nbsp;#Trim(Left(CompResults.ResultsUnits,10))#
//				</td>	
//				<td width="7">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#CompResults.ResultsTestLowRange#
//				</td>		
//				<td width="7">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#CompResults.ResultsTestHighRange#
//				</td>			
//				<td width="7">&nbsp;</td>
//				<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//					#Trim(CompResults.ResultsTestComments)#
//				</td>
//			</tr>
//			<cfif CompResults.ResultsTestNotes NEQ "">
//				<cfset Variables.RNote=1>
//			</cfif>
//		</cfoutput>
//	</table>
//
//	<cfif Variables.RNote EQ 1>
//		<table cellpadding="0" cellspacing="0" style="width: 7.0in;" border="0">
//			<tr>
//				<td width="7">&nbsp;</td>	
//				<cfoutput>
//				<td align="left" colspan="5" style="width: 7.0in; #variables.ColumnHeaderStyle#" valign="top">
//				</cfoutput>
//					Notes (Impressions)
//				</td>
//			</tr>
//			<cfoutput query="CompResults">
//				<cfif Trim(CompResults.ResultsTestNotes) NEQ "">
//					<tr>
//						<td width="7">&nbsp;</td>
//						<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//							#DateFormat(CompResults.EncounterDate,"mm/dd/yyyy")#
//						</td>
//						<td width="7">&nbsp;</td>
//						<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//							#CompResults.ResultsTestName#
//						</td>
//						<td width="7">&nbsp;</td>
//						<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//							<cfset Variables.Note=ReplaceNoCase(CompResults.ResultsTestNotes,Variables.Crlf,"<br>","ALL")>
//							#Trim(Variables.Note)#
//						</td>
//					</tr>
//				</cfif>
//			</cfoutput>
//		</table>
//	</cfif>
//</cfif>

if ($CompResults_num != 0) {

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
  $ColumnHeaderStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: bold; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
  ?>

  <table cellpadding="0" cellspacing="0" style="width: 7.0in;" border="0">
    <tr>
      <td width="7">&nbsp;</td>
      <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
        Date
      </td>
      <td width="7">&nbsp;</td>
      <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
        Test
      </td>	
      <td width="7">&nbsp;</td>
      <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
        Results
      </td>	
      <td width="7">&nbsp;</td>
      <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
        Low
      </td>	
      <td width="7">&nbsp;</td>
      <td align="left" style="<?php echo $ColumnHeaderStyle; ?>" valign="top">
        High
      </td>	
      <td width="7">&nbsp;</td>
      <td align="left" style="width: 6.5in; <?php echo $ColumnHeaderStyle; ?>" valign="top">
        Comments
      </td>
    </tr>

    <tr>
      <td width="7">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php echo date('m/d/Y', strtotime($CompResults_row->EncounterDate)); ?>
      </td>
      <td width="7">&nbsp;</td>
      <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php echo $CompResults_row->ResultsTestName; ?>
      </td>	
      <td width="7">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php echo $CompResults_row->ResultsTestResults . " " . trim($CompResults_row->ResultsUnits); ?>
      </td>	
      <td width="7">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php echo $CompResults_row->ResultsTestLowRange; ?>
      </td>		
      <td width="7">&nbsp;</td>
      <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php echo $CompResults_row->ResultsTestHighRange; ?>
      </td>			
      <td width="7">&nbsp;</td>
      <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
        <?php echo $CompResults_row->ResultsTestComments; ?>
      </td>
    </tr>

    <?php
    if ($CompResults_row->RNote != "") {
      ?>
      <table cellpadding="0" cellspacing="0" style="width: 7.0in;" border="0">
        <tr>
          <td width="7">&nbsp;</td>	
        <cfoutput>
          <td align="left" colspan="5" style="width: 7.0in; <?php echo $ColumnHeaderStyle; ?>" valign="top">
        </cfoutput>
        Notes (Impressions)
        </td>
        </tr>
        <?php
        if ($CompResults_row->ResultsTestNotes != "") {
          ?>
          <tr>
            <td width="7">&nbsp;</td>
            <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
              <?php echo date('m/d/Y', strtotime($CompResults_row->EncounterDate)); ?>
            </td>
            <td width="7">&nbsp;</td>
            <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
              <?php echo $CompResults_row->ResultsTestName; ?>
            </td>
            <td width="7">&nbsp;</td>
            <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
              <?php echo $CompResults_row->ResultsTestNotes; ?>
            </td>
          </tr>
          <?php
        }
        ?>

      </table>
      <?php
    }
    ?>
  </table>

  <?php
}
?>