<?php
//<cfquery datasource="#Attributes.EMRDataSource#" name="OrdersComp">
//	Select 
//		O.EncounterDate,
//		OL.Description As ListDescription
//	From Orders O
//	LEFT JOIN OrdersICD9 I
//		ON O.Order_Id=I.Order_Id
//	LEFT JOIN OrdersList OL
//		ON I.OrderList_Id=OL.OrderList_Id	
//	LEFT JOIN OrderItems OI
//		ON OL.OrderList_Id=OI.OrderList_Id
//	Where O.Order_Id In (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//		And (O.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR O.Hidden IS NULL)
//	Order By O.EncounterDate Desc
//</cfquery>

//$ComponentKey = 1;
$sql = "	Select 
		O.EncounterDate,
		OL.Description As ListDescription
	From " . $data_db . ".dbo.Orders O
	LEFT JOIN " . $data_db . ".dbo.OrdersICD9 I
		ON O.Order_Id=I.Order_Id
	LEFT JOIN " . $data_db . ".dbo.OrdersList OL
		ON I.OrderList_Id=OL.OrderList_Id	
	LEFT JOIN " . $data_db . ".dbo.OrderItems OI
		ON OL.OrderList_Id=OI.OrderList_Id
	Where O.Order_Id In ($ComponentKey)
		And (O.Hidden<>1 OR O.Hidden IS NULL)
	Order By O.EncounterDate Desc";


$OrdersComp = $this->ReportModel->data_db->query($sql);
$OrdersComp_num = $OrdersComp->num_rows();
$OrdersComp_row = $OrdersComp->row();

//<cfif OrdersComp.RecordCount NEQ 0>
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
//		<cfoutput query="OrdersComp"  group="ListDescription">
//			<tr>
//				<td width="7">&nbsp;</td>
//				<td align="left" nowrap style="#variables.DefaultStyle#" valign="top">
//					#DateFormat(OrdersComp.EncounterDate,"mm/dd/yyyy")#
//				</td>
//				<td width="4">&nbsp;</td>
//				<td align="left" nowrap style="width: 6.0in; #variables.DefaultStyle#" valign="top">
//					#Trim(OrdersComp.Listdescription)#
//				</td>
//			</tr>
//		</cfoutput>
//	</table>
//</cfif>

if ($OrdersComp_num != 0) {

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
      <td align="left" nowrap style="<?php echo $DefaultStyle; ?>" valign="top">
        <?php echo date('m/d/Y', strtotime($OrdersComp_row->EncounterDate)); ?>
      </td>
      <td width="4">&nbsp;</td>
      <td align="left" nowrap style="width: 6.0in; <?php echo $DefaultStyle; ?>" valign="top">
        <?php echo trim($OrdersComp_row->Listdescription); ?>
      </td>
    </tr>
  </table>
  <?php
}
?>