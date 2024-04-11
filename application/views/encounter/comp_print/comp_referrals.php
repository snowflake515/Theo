<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="CorrNotes">
//	Select 
//		RF.EncounterDate,
//		RF.Description,
//		RF.EmailAddress,
//		RPL.RefProvFirst,
//		RPL.RefProvLast,
//		DestinationCode = case
//			when RF.DestinationCode is null Then '0'
//			when RF.DestinationCode = '' Then '0'
//			when RF.DestinationCode = ' ' Then '0'
//			else RF.DestinationCode
//			end,
//		PP.FirstName + ' ' + PP.LastName as PatientName,
//		IC.Ins_Name
//	From Referrals RF Left Join ReferringProviderList RPL
//		ON RPL.RefProv_Id=RF.RefProvider_Id	
//	left outer JOIN PatientProfile PP
//		ON RF.Patient_Id=PP.Patient_Id
//	left outer JOIN InsuranceCompanies IC
//		ON RF.Insurance_Id=IC.Ins_Id
//	Where RF.Referral_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And (RF.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR RF.Hidden IS NULL)
//	Order By RF.EncounterDate DESC
//</cfquery>

//$ComponentKey = "1";

$sql = "Select 
		RF.EncounterDate,
		RF.Description,
		RF.EmailAddress,
		RPL.RefProvFirst,
		RPL.RefProvLast,
		DestinationCode = case
			when RF.DestinationCode is null Then '0'
			when RF.DestinationCode = '' Then '0'
			when RF.DestinationCode = ' ' Then '0'
			else RF.DestinationCode
			end,
		PP.FirstName + ' ' + PP.LastName as PatientName,
		IC.Ins_Name
	From " . $data_db . ".dbo.Referrals RF Left Join " . $data_db . ".dbo.ReferringProviderList RPL
		ON RPL.RefProv_Id=RF.RefProvider_Id	
	left outer JOIN " . $data_db . ".dbo.PatientProfile PP
		ON RF.Patient_Id=PP.Patient_Id
	left outer JOIN " . $data_db . ".dbo.InsuranceCompanies IC
		ON RF.Insurance_Id=IC.Ins_Id
	Where RF.Referral_Id In ($ComponentKey)
		And (RF.Hidden<>1 OR RF.Hidden IS NULL)
	Order By RF.EncounterDate DESC";


$CorrNotes = $this->ReportModel->data_db->query($sql);
$CorrNotes_num = $CorrNotes->num_rows();
$CorrNotes_row = $CorrNotes->row();

//<cfif CorrNotes.RecordCount NEQ 0>
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
//		<cfoutput query="CorrNotes">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="left" style="#variables.DefaultStyle#" valign="top" nowrap>
//					#DateFormat(CorrNotes.EncounterDate,"mm/dd/yyyy")#
//				</td>
//				<td width="10">&nbsp;</td>
//				<td align="left" style="#variables.DefaultStyle#" valign="top" nowrap>
//					<cfif (CorrNotes.DestinationCode EQ '1') or (CorrNotes.DestinationCode EQ '2') or (CorrNotes.DestinationCode EQ '6')>
//						#Trim(CorrNotes.Refprovfirst)# #Trim(CorrNotes.Refprovlast)#&nbsp;
//					<cfelseif (CorrNotes.DestinationCode EQ '3') or (CorrNotes.DestinationCode EQ '4') or (CorrNotes.DestinationCode EQ '7')>
//						#Trim(CorrNotes.Ins_Name)#&nbsp;
//					<cfelseif (CorrNotes.DestinationCode EQ '5') or (CorrNotes.DestinationCode EQ '8')>
//						#Trim(CorrNotes.PatientName)#&nbsp;
//					<cfelseif (CorrNotes.DestinationCode EQ 'A')>
//						#Trim(CorrNotes.EmailAddress)#&nbsp;
//					<cfelse>
//						&nbsp;
//					</cfif>
//				</td>
//				<td width="10">&nbsp;</td>
//				<td align="left" style="width: 6.5in; #variables.DefaultStyle#" valign="top">
//					#Trim(CorrNotes.Description)#
//				</td>
//<!---
//				<td align="left" style="width: 6.5in; color: black; font-size: 12px; font-weight: normal; font-family: Times New Roman;" valign="top">
//					<cfset Variables.CNotes=ReplaceNoCase(CorrNotes.Notes,Variables.Crlf,"<br>","ALL")>
//					#Trim(Variables.CNotes)#
//				</td>
//--->
//			</tr>
//		</cfoutput>
//	</table>
//</cfif>

if ($CorrNotes_num != 0) {

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
      <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top" nowrap>
        <?php echo date('m/d/Y', strtotime($CorrNotes_row->EncounterDate)); ?>
      </td>
      <td width="10">&nbsp;</td>
      <td align="left" style="<?php echo $DefaultStyle; ?>" valign="top" nowrap>


        <?php
        if ($CorrNotes_row->DestinationCode == '1' || $CorrNotes_row->DestinationCode == '2' || $CorrNotes_row->DestinationCode == '6') {
          echo trim($CorrNotes_row->Refprovfirst) . " " . trim($CorrNotes_row->Refprovlast) . " &nbsp; ";
        } else if ($CorrNotes_row->DestinationCode == '3' || $CorrNotes_row->DestinationCode == '4' || $CorrNotes_row->DestinationCode == '7') {
          echo trim($CorrNotes_row->Ins_Name) . " &nbsp;";
        } else if ($CorrNotes_row->DestinationCode == '5' || $CorrNotes_row->DestinationCode == '8') {
          echo trim($CorrNotes_row->PatientName) . " &nbsp;";
        } else if ($CorrNotes_row->DestinationCode == 'A') {
          echo trim($CorrNotes_row->EmailAddress) . " &nbsp;";
        } else {
          echo "&nbsp;";
        }
        ?>

      </td>
      <td width="10">&nbsp;</td>
      <td align="left" style="width: 6.5in; <?php echo $DefaultStyle; ?>" valign="top">
        <?php echo trim($CorrNotes_row->Description) . " &nbsp;"; ?>
      </td>

    </tr>
  </table>
  <?php
}
?>
