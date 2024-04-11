<?php
//<!--- 
//	<responsibilities>This is the page for the creation of a new 'History Reviewed' section of the Chart Notes pages using the data from the new Flex History Module</responsibilities>
//
//	<note author="Chris Hoffman" date="08 March 2011">File: comp_newHistoryReviewed.cfm
//													Case: 8899 - Created file
//	</note>
//	<io>
//		<in>
//		</in>
//
//		<out>
//		</out>
//	</io>
//
//  --->
//
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.OrgId=Session.Org_Id>
//	<cfset Variables.sTimeOffset=Session.UTC_TimeOffset>
//	<cfset Variables.sDST=Session.UTC_DST>
//	<cfset Variables.sTimeZoneID=Session.UTC_TimeZoneId>
//</cflock>
//
//<cfset Variables.Patient_Id=Attributes.PatientKey>
//<cfset Variables.dataObj = StructNew()>
//<cfset Variables.dataObj.patientId = Variables.Patient_Id>
//<cfset Variables.dataObj.orgTimeZoneOffset = Variables.sTimeOffset>
//<cfset Variables.dataObj.orgTimeZoneDST = Variables.sDST>
//<cfset Variables.dataObj.orgTimeZoneId = Variables.sTimeZoneID>	
//		
//<cfif IsDefined('Attributes.RefHeader')>
//	<cfset Variables.TextStyle = 'font-size: 12px; color: Black; font-weight: normal; font-face: Garamond, Arial, Helvetica;'>
//<cfelse>
//if($RefHeader){ //SKIP
$TextStyle = 'font-size: 12px; color: Black; font-weight: normal; font-face: Garamond, Arial, Helvetica;';
//}
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
//	<cfset Variables.TextStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: "& variables.BodyFontInfo.FontSize & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//</cfif>
//
//<cfif Trim(Attributes.UseDetailKeys) NEQ 1>
//	<cfset variables.bUseDetailKeys = false>
//	<cfset Variables.ReviewedBy = CreateObject("component","cfc.history.History").getHistoryReviewed(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//<cfelse>
//	<cfset variables.bUseDetailKeys = true>
//	<!--- the Encounter or Referral Locked--->
//	<cfif isDefined('Attributes.Referral')>
//		<!--- This is being called from printreferrals.cfm--->
//		<cfset Variables.dataObj.SearchId = Attributes.KeyValue>
//	<cfelse>	
//		<!--- This is being called from SaveChartNotes.cfm--->
//		<cfset Variables.dataObj.SearchId = Attributes.ComponentKey>
//	</cfif>	
//	<cfset Variables.ReviewedBy = CreateObject("component","cfc.history.ChartNotes_History").getHistoryReviewed(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Variables.dataObj)>
//</cfif>	
//$PatientKey = 2593155; //EMBED
if ($UseDetailKeys != 1) {
// dbo.UTCtoLocalTZ SKIP
  $sql = "Select Top 1 HistoryReviewed_ID,
						Patient_ID,
						Org_ID,
						DatePopulated_UTC,
						ReviewedBy_Users_PK,
						ReviewedOn_UTC,
						(Select top 1 CASE WHEN isNull(U.credentials,'') = '' THEN U.fname + ' ' + U.lname 
							   ELSE U.fname + ' ' + U.lname + ', ' + U.credentials 
						  END 
						 From " . $user_db . ".dbo.Users U 
						 Where U.ID=H.ReviewedBy_Users_PK) as DisplayReviewedBy,
						 H.ReviewedOn_UTC as ReviewedOnDate,
						(SELECT TOP 1 'embed') as ReviewedOnDateTZAbbr
			FROM  " . $data_db . ".dbo.HistoryReviewed H
			WHERE Patient_ID = $PatientKey
			ORDER BY HistoryReviewed_ID DESC";
} else {
  $bUseDetailKeys = TRUE;
  $sql = "Select Top 1 HistoryReviewed_ID,
						Patient_ID,
						Org_ID,
						DatePopulated_UTC,
						ReviewedBy_Users_PK,
						ReviewedOn_UTC,
						(Select top 1 CASE WHEN isNull(U.credentials,'') = '' THEN U.fname + ' ' + U.lname 
							   ELSE U.fname + ' ' + U.lname + ', ' + U.credentials 
						  END 
						 From " . $user_db . ".dbo.Users U 
						 Where U.ID=H.ReviewedBy_Users_PK) as DisplayReviewedBy,
						H.ReviewedOn_UTC as ReviewedOnDate,
						(SELECT TOP 1 'embed') as ReviewedOnDateTZAbbr
			FROM  " . $data_db . ".dbo.HistoryReviewed H
			WHERE Patient_ID = $PatientKey
			ORDER BY HistoryReviewed_ID DESC";
}

$ReviewedBy = $this->ReportModel->data_db->query($sql);
$ReviewedBy_num = $ReviewedBy->num_rows();
$ReviewedBy_row = $ReviewedBy->row();

//<p />
//<cfmodule template="componentheaders.cfm"
//		 EMRDataSource="#Attributes.EMRDataSource#"
//		 HeaderKey="#Attributes.HeaderKey#"
//		 PatientKey="#Attributes.PatientKey#"
//		 HeaderMasterKey="#Attributes.HeaderMasterKey#"
//		 FreeTextKey="#Attributes.FreeTextKey#"
//		 SOHeaders="#Attributes.SOHeaders#">
//		<cfset caller.HeaderNeeded = False>
//		<cfset caller.NeedTemplateHeader = False>
//
//
//<cfoutput query="Variables.ReviewedBy">
//	<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//		<cfif IsDefined('Attributes.RefHeader')>
//			<!--- This is being called from printreferrals.cfm and needs a header --->
//			<tr>
//				<td colspan="2" style="font-size: 14px; color: Maroon; font-weight: bold; font-face: Garamond, Arial, Helvetica;">#Attributes.RefHeader#</td>
//			</tr>	
//		</cfif>
//		<tr>
//			<td width="7">&nbsp;</td>				
//			<td style="#Variables.TextStyle#" align="left"><strong>History Last Reviewed By:</strong> #Trim(Variables.ReviewedBy.DisplayReviewedBy)# on #DateFormat(Variables.ReviewedBy.ReviewedOnDate,'MM/DD/YYYY')#,
//				#TimeFormat(Variables.ReviewedBy.ReviewedOnDate,'hh:mm tt')# #Variables.ReviewedBy.REVIEWEDONDATETZABBR# </td>
//		</tr>
//	</table>
//</cfoutput>
//
if ($ReviewedBy_num != 0) {

  $data['HeaderKey'] = $HeaderKey;
  $data['PatientKey'] = $PatientKey;
  $data['HeaderMasterKey'] = $HeaderMasterKey;
  $data['FreeTextKey'] = $FreeTextKey;
  $data['SOHeaders'] = $SOHeaders;
  $this->load->view('encounter/print/componentheaders', $data);
  ?>

  <table cellpadding="0" cellspacing="0" style="width: 7.0in;">
  <?php
//  if($RefHeader){ SKIPP
  ?>
    <tr>
      <td colspan="2" style="font-size: 14px; color: Maroon; font-weight: bold; font-face: Garamond, Arial, Helvetica;">RefHeader Embed</td>
    </tr>
  <?php
//  }
  ?>
    <tr>
      <td width="7">&nbsp;</td>				
      <td style="<?php echo $TextStyle; ?>" align="left"><strong>History Last Reviewed By:</strong> 
  <?php
  echo $ReviewedBy_row->DisplayReviewedBy . " on " . date('m/d/Y,  H:i s', strtotime($ReviewedBy_row->ReviewedOnDate)) . " " . $ReviewedBy_row->ReviewedOnDateTZAbbr;
  ?>
      </td>
    </tr>
  </table>

  <?php
}
?>