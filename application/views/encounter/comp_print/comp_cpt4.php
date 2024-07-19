<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="CompCPT4">
//	Select 
//		CPT4_Id,
//		CPT4Code,
//		CPT4Description
//	From CPT4Master
//	Where CPT4_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//	Order By CPT4Code
//</cfquery>

//$ComponentKey
$ComponenKeyVar = $ComponentKey; //"30737, 30738";

$sql = "Select 
		CPT4_Id,
		CPT4Code,
		CPT4Description
	From " . $data_db . ".dbo.CPT4Master
	Where CPT4_Id In ($ComponenKeyVar)
	Order By CPT4Code";

$CPT4KeyCode = $this->ReportModel->data_db->query($sql);
$CPT4KeyCode_num = $CPT4KeyCode->num_rows();
$CPT4KeyCode_result = $CPT4KeyCode->result();


//<cfset CPT4KeyCode=StructNew()>
//<cfset CPT4KeyDesc=StructNew()>
//<cfset Variables.RowCount=1>
//<cfloop list="#Attributes.ComponentKey#" index="idx">
//	<cfloop query="CompCPT4">
//		 <cfif Variables.idx EQ CompCPT4.CPT4_Id> 
//		 	<cfset Temp=StructInsert(CPT4KeyCode,Variables.RowCount,CompCPT4.CPT4Code,TRUE)>	
//		 	<cfset Temp=StructInsert(CPT4KeyDesc,Variables.RowCount,CompCPT4.CPT4Description,TRUE)>
//		 </cfif>
//	</cfloop>
//	<cfset Variables.RowCount=Variables.RowCount+1>
//</cfloop>
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
//		<cfoutput>
//			<cfloop from="1" to="#ListLen(Attributes.ComponentKey,',')#" index="cidx">
//				<tr>
//					<td width="7">&nbsp;</td>
//					<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//						<cfif StructKeyExists(CPT4KeyCode,Variables.cidx)>
//							#StructFind(CPT4KeyCode,Variables.cidx)# - 
//						</cfif>
//					</td>
//					<td width="4">&nbsp;</td>
//					<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//						<cfif StructKeyExists(CPT4KeyDesc,Variables.cidx)>
//							#StructFind(CPT4KeyDesc,Variables.cidx)#
//						</cfif>
//					</td>
//				</tr>
//			</cfloop>
//		</cfoutput>
//	</table>
//</cfif>


if ($CPT4KeyCode_num != 0) {
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
  ?>
  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">

    <?php foreach ($CPT4KeyCode_result as $val) { ?>
      <tr>
        <td width="7">&nbsp;</td>
        <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
          <?php
          echo trim($val->CPT4Code) . " - ";
          ?>
        </td>
        <td width="4">&nbsp;</td>
        <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
          <?php
          echo trim($val->CPT4Description);
          ?>
        </td>
      </tr>
    <?php } ?>
  </table>
<?php } ?>