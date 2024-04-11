<?php

//<!--- Program Name: Comp_LabGraph.cfm
//
//		Change Log
//		JWY 3/28/2008 - Remove CFX _GraphicsServer tag
//		
//--->
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="GraphOptions">
//Select TOP 1
//       GraphType,
//       GraphSort
//  From ProviderProfile
// Where Provider_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.ProviderKey#">
//</cfquery>
//
$sql = "Select TOP 1
       GraphType,
       GraphSort
  From " . $data_db . ".dbo.ProviderProfile
 Where Provider_Id=$ProviderKey";
$GraphOptions = $this->ReportModel->data_db->query($sql);
$GraphOptions_num = $GraphOptions->num_rows();
$GraphOptions_row = $GraphOptions->row();

//
//<cfif GraphOptions.GraphType EQ 1>
//	<cfset Variables.GraphType=6>
//	<cfset Variables.GraphName="Line">
//	<cfset Variables.GraphStyle=4>
//<cfelse>
//	<cfset Variables.GraphType=4>
//	<cfset Variables.GraphName="Bar">
//	<cfset Variables.GraphStyle=0>
//</cfif>
//
if ($GraphOptions_row->GraphType == 1) {
  $GraphType = 6;
  $GraphName = "line";
  $GraphStyle = 4;
} else {
  $GraphType = 4;
  $GraphName = "bar";
  $GraphStyle = 0;
}
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="GraphData">
//Select D.ResultsTestName,
//       D.ResultsTestResults,
//       D.ResultsTestLowRange,
//       D.ResultsTestHighRange,
//       D.ResultsTestAbnormal,
//	   D.ResultsTestNotes,
//	   D.ResultsTestComments,
//	   H.ResultsHistoryNotes,
//       Convert(Char,H.EncounterDate,101) As EncounterDate
//  From ResultsDetails D,
//       ResultsHistory H
// Where D.ResultsDetails_Id IN (<cfqueryparam list="Yes" separator="," value="#Attributes.ComponentKey#">)
//   And D.ResultsHistory_Id=H.ResultsHistory_Id
//   And (D.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR D.Hidden IS NULL)
//</cfquery>
//
$sql = "Select D.ResultsTestName,
       D.ResultsTestResults,
       D.ResultsTestLowRange,
       D.ResultsTestHighRange,
       D.ResultsTestAbnormal,
	   D.ResultsTestNotes,
	   D.ResultsTestComments,
	   H.ResultsHistoryNotes,
       Convert(Char,H.EncounterDate,101) As EncounterDate
  From " . $data_db . ".dbo.ResultsDetails D,
       " . $data_db . ".dbo.ResultsHistory H
 Where D.ResultsDetails_Id IN ($ComponentKey)
  And D.ResultsHistory_Id=H.ResultsHistory_Id
 And (D.Hidden<>1 OR D.Hidden IS NULL)";
$GraphData = $this->ReportModel->data_db->query($sql);
$GraphData_num = $GraphData->num_rows();
$GraphData_result = $GraphData->result();
$GraphData_row = $GraphData->row();

//
//<cfif GraphData.RecordCount EQ 1>
//	<cfset Variables.LegendBySet="Low Range,Result,High Range">
//	<cfset Variables.BottomTitle=DateFormat(GraphData.EncounterDate,'mm/dd/yyyy')>
//	<cfif Trim(GraphData.ResultsTestAbnormal) EQ "Y">
//		<cfif IsNumeric(GraphData.ResultsTestResults)>
//			<cfif (GraphData.ResultsTestResults GT GraphData.ResultsTestHighRange)>
//				<cfset Variables.StatusMessage="High">
//			<cfelseif (GraphData.ResultsTestResults LT GraphData.ResultsTestLowRange)> 
//				<cfset Variables.StatusMessage="Low">
//			<cfelse>
//				<cfset Variables.StatusMessage="High">
//			</cfif>
//		<cfelse>
//			<cfset Variables.StatusMessage="High">
//		</cfif>
//	<cfelse>
//		<cfif IsNumeric(GraphData.ResultsTestResults)>
//			<cfif (GraphData.ResultsTestResults GT GraphData.ResultsTestHighRange)>
//				<cfset Variables.StatusMessage="High">
//			<cfelseif (GraphData.ResultsTestResults LT GraphData.ResultsTestLowRange)> 
//				<cfset Variables.StatusMessage="Low">
//			<cfelse>
//				<cfset Variables.StatusMessage="Normal">
//			</cfif> 
//		<cfelse>
//			<cfset Variables.StatusMessage="">
//		</cfif>
//	</cfif>
//	
//	<cfif Variables.StatusMessage NEQ "">
//		<cfif Variables.StatusMessage EQ "High">
//			<cfset Variables.ColorBySet="1,4,3">
//			<cfset Variables.Label="#GraphData.ResultsTestLowRange#,#GraphData.ResultsTestResults#,#GraphData.ResultsTestHighRange#">
//			<cfset Variables.Data="#GraphData.ResultsTestLowRange#,#GraphData.ResultsTestResults#,#GraphData.ResultsTestHighRange#">
//		<cfelseif Variables.StatusMessage EQ "Low">
//			<cfset Variables.ColorBySet="1,14,3">
//			<cfset Variables.Label="#GraphData.ResultsTestLowRange#,#GraphData.ResultsTestResults#,#GraphData.ResultsTestHighRange#">
//			<cfset Variables.Data="#GraphData.ResultsTestLowRange#,#GraphData.ResultsTestResults#,#GraphData.ResultsTestHighRange#">
//		<cfelse> 
//			<cfset Variables.ColorBySet="1,2,3">
//			<cfset Variables.Label="#GraphData.ResultsTestLowRange#,#GraphData.ResultsTestResults#,#GraphData.ResultsTestHighRange#">
//			<cfset Variables.Data="#GraphData.ResultsTestLowRange#,#GraphData.ResultsTestResults#,#GraphData.ResultsTestHighRange#">
//		</cfif>
//	<cfelse>
//		<cfset Variables.ColorBySet="0,0,0">
//		<cfset Variables.Label="0,#GraphData.ResultsTestResults#,0">
//		<cfset Variables.Data="0,#GraphData.ResultsTestResults#,0">
//	</cfif>
//<cfelse>
//	<cfloop query="GraphData">
//		<cfif GraphData.CurrentRow GT 1>
//			<cfset Variables.ColorBySet=Variables.ColorBySet&","&GraphData.CurrentRow>
//		<cfelse>
//			<cfset Variables.ColorBySet="1">
//		</cfif>
//	</cfloop>
//	<cfset Variables.BottomTitle="">
//	<cfset Variables.Data=ValueList(GraphData.ResultsTestResults,",")>
//	<cfset Variables.Label=ValueList(GraphData.EncounterDate,",")>
//	<cfset Variables.LegendBySet=ValueList(GraphData.EncounterDate,",")>	
//</cfif>
//
if ($GraphData_num == 1) {
  $LegendBySet = 'Low Range,Result,High Range';
  $BottomTitle = date('m/d/Y', strtotime($GraphData_row->EncounterDate));
  if (trim($GraphData_row->ResultsTestAbnormal) == 'Y') {



    if (is_numeric($GraphData_row->ResultsTestResults)) {
      if ($GraphData_row->ResultsTestResults > $GraphData_row->ResultsTestHighRange) {
        $StatusMessage = 'High';
      } elseif ($GraphData_row->ResultsTestResults < $GraphData_row->ResultsTestHighRange) {
        $StatusMessage = 'Low';
      } else {
        $StatusMessage = 'High';
      }
    } else {
      $StatusMessage = 'High';
    }
  } else {

    if (is_numeric($GraphData_row->ResultsTestResults)) {
      if ($GraphData_row->ResultsTestResults > $GraphData_row->ResultsTestHighRange) {
        $StatusMessage = 'High';
      } elseif ($GraphData_row->ResultsTestResults < $GraphData_row->ResultsTestHighRange) {
        $StatusMessage = 'Low';
      } else {
        $StatusMessage = 'High';
      }
    } else {
      $StatusMessage = 'High';
    }
  }
//	
//	<cfif Variables.StatusMessage NEQ "">
//		<cfif Variables.StatusMessage EQ "High">
//			<cfset Variables.ColorBySet="1,4,3">
//			<cfset Variables.Label="#GraphData.ResultsTestLowRange#,#GraphData.ResultsTestResults#,#GraphData.ResultsTestHighRange#">
//			<cfset Variables.Data="#GraphData.ResultsTestLowRange#,#GraphData.ResultsTestResults#,#GraphData.ResultsTestHighRange#">
//		<cfelseif Variables.StatusMessage EQ "Low">
//			<cfset Variables.ColorBySet="1,14,3">
//			<cfset Variables.Label="#GraphData.ResultsTestLowRange#,#GraphData.ResultsTestResults#,#GraphData.ResultsTestHighRange#">
//			<cfset Variables.Data="#GraphData.ResultsTestLowRange#,#GraphData.ResultsTestResults#,#GraphData.ResultsTestHighRange#">
//		<cfelse> 
//			<cfset Variables.ColorBySet="1,2,3">
//			<cfset Variables.Label="#GraphData.ResultsTestLowRange#,#GraphData.ResultsTestResults#,#GraphData.ResultsTestHighRange#">
//			<cfset Variables.Data="#GraphData.ResultsTestLowRange#,#GraphData.ResultsTestResults#,#GraphData.ResultsTestHighRange#">
//		</cfif>
//	<cfelse>
//		<cfset Variables.ColorBySet="0,0,0">
//		<cfset Variables.Label="0,#GraphData.ResultsTestResults#,0">
//		<cfset Variables.Data="0,#GraphData.ResultsTestResults#,0">
//	</cfif>
  if ($StatusMessage != "") {
    if ($StatusMessage == 'High') {
      $ColorBySet = "1,14,3";
      $Label = $GraphData_row->ResultsTestLowRange.',' . $GraphData_row->ResultsTestResults . ','.$GraphData_row->ResultsTestHighRange;
      $Data = $GraphData_row->ResultsTestLowRange.',' . $GraphData_row->ResultsTestResults . ','.$GraphData_row->ResultsTestHighRange;
    } elseif ($StatusMessage == 'Low') {
      $ColorBySet = "1,2,3";
      $Label =  $GraphData_row->ResultsTestLowRange.',' . $GraphData_row->ResultsTestResults . ','.$GraphData_row->ResultsTestHighRange;
      $Data =  $GraphData_row->ResultsTestLowRange.',' . $GraphData_row->ResultsTestResults . ','.$GraphData_row->ResultsTestHighRange;
    } else {
      $ColorBySet = "0,0,0";
      $Label =  $GraphData_row->ResultsTestLowRange.',' . $GraphData_row->ResultsTestResults . ','.$GraphData_row->ResultsTestHighRange;
      $Data =  $GraphData_row->ResultsTestLowRange.',' . $GraphData_row->ResultsTestResults . ','.$GraphData_row->ResultsTestHighRange;
    }
  } else {
    $ColorBySet = "0,0,0";
    $Label = '0,' . $GraphData_row->ResultsTestResults . ',0';
    $Data = '0,' . $GraphData_row->ResultsTestResults . ',0';
  }
}
//
//<cfif Attributes.HeaderMasterKey EQ 68>
//	<cfset Width="450">
//	<cfset Height="250">
//	<cfset GraphType="4">
//	<cfset GraphStyle="0">
//	<cfset LegendPos="0">
//<cfelseif Attributes.HeaderMasterKey EQ 67>
//	<cfset Width="450">
//	<cfset Height="250">
//	<cfset GraphType="2">
//	<cfset GraphStyle="2">
//	<cfset LegendPos="0">
//	<cfset Label="">
//<cfelseif Attributes.HeaderMasterKey EQ 69>
//	<cfset Width="450">
//	<cfset Height="250">
//	<cfset GraphType="6">
//	<cfset GraphStyle="0">
//	<cfset LegendPos="0">
//</cfif>

if($HeaderMasterKey == 68){
  $Width= 450;
  $Height = 250;
  $GraphType = 4;
  $GraphStyle = 0;
  $LegendPos = 0;          
}elseif ($HeaderMasterKey == 67) {
  $Width= 450;
  $Height = 250;
  $GraphType = 2;
  $GraphStyle = 2;
  $LegendPos = 0;
}elseif ($HeaderMasterKey == 69) {
  $Width= 450;
  $Height = 250;
  $GraphType = 6;
  $GraphStyle = 0;
  $LegendPos = 0;
}
//
//<cfif caller.HeaderNeeded EQ True>
//	<cfmodule template="componentheaders.cfm"
//	 EMRDataSource="#Attributes.EMRDataSource#"
//	 HeaderKey="#Attributes.HeaderKey#"
//	 PatientKey="#Attributes.PatientKey#"
//	 HeaderMasterKey="#Attributes.HeaderMasterKey#"
//	 FreeTextKey="#Attributes.FreeTextKey#"
//	 SOHeaders="#Attributes.SOHeaders#">
//	<cfset caller.HeaderNeeded = False>
//	<cfset caller.NeedTemplateHeader = False>
//</cfif>
//
  $data['HeaderKey'] = $HeaderKey;
  $data['PatientKey'] = $PatientKey;
  $data['HeaderMasterKey'] = $HeaderMasterKey;
  $data['FreeTextKey'] = $FreeTextKey;
  $data['SOHeaders'] = $SOHeaders;
  $this->load->view('encounter/print/componentheaders', $data);
//
//<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
//<tr>
//<td width="7"></td>
//<td>
//<cfinclude template="v8comp_labgraph.cfm">
//</td>
//<cfif GraphData.RecordCount GT 1>
//<td valign="middle">
//	<table cellpadding="4" cellspacing="0" border="1">
//	<tr>
//	<td align="center" style="color: black; font-size: 12px; font-weight: bold; font-family: Times New Roman;" valign="top">
//	Date
//	</td>
//	<td align="left" style="color: black; font-size: 12px; font-weight: bold; font-family: Times New Roman;" valign="top">
//	Result
//	</td>
//	<td align="left" nowrap style="color: black; font-size: 12px; font-weight: bold; font-family: Times New Roman;" valign="top">
//	Low
//	</td>
//	<td align="left" nowrap style="color: black; font-size: 12px; font-weight: bold; font-family: Times New Roman;" valign="top">
//	High
//	</td>
//	</tr>
//	<cfoutput query="GraphData">
//	<tr>
//	<td align="left" style="color: black; font-size: 12px; font-weight: normal; font-family: Times New Roman;" valign="top">
//	#GraphData.EncounterDate#
//	</td>
//	<td align="center" style="color: black; font-size: 12px; font-weight: normal; font-family: Times New Roman;" valign="top">
//	#GraphData.ResultsTestResults#
//	</td>
//	<td align="left" style="color: black; font-size: 12px; font-weight: normal; font-family: Times New Roman;" valign="top">
//	#GraphData.ResultsTestLowRange#
//	</td>
//	<td align="left" style="color: black; font-size: 12px; font-weight: normal; font-family: Times New Roman;" valign="top">
//	#GraphData.ResultsTestHighRange#
//	</td>
//	</tr>
//	</cfoutput>
//	</table>
//</td>
//</tr>
//</cfif>
//</table>
  $v8comp_labgraph = $this->load->view('encounter/comp_print/v8comp_labgraph', '', TRUE);
  $html ='<table cellpadding="0" cellspacing="0" style="width: 7.0in;">
<tr>
<td width="7"></td>
<td>
'.$v8comp_labgraph.'
</td>
<cfif GraphData.RecordCount GT 1>
<td valign="middle">
	<table cellpadding="4" cellspacing="0" border="1">
	<tr>
	<td align="center" style="color: black; font-size: 12px; font-weight: bold; font-family: Times New Roman;" valign="top">
	Date
	</td>
	<td align="left" style="color: black; font-size: 12px; font-weight: bold; font-family: Times New Roman;" valign="top">
	Result
	</td>
	<td align="left" nowrap style="color: black; font-size: 12px; font-weight: bold; font-family: Times New Roman;" valign="top">
	Low
	</td>
	<td align="left" nowrap style="color: black; font-size: 12px; font-weight: bold; font-family: Times New Roman;" valign="top">
	High
	</td>
	</tr>
	<cfoutput query="GraphData">
	<tr>
	<td align="left" style="color: black; font-size: 12px; font-weight: normal; font-family: Times New Roman;" valign="top">
	  '.$GraphData_row->EncounterDate.'
	</td>
	<td align="center" style="color: black; font-size: 12px; font-weight: normal; font-family: Times New Roman;" valign="top">
	  '.$GraphData_row->ResultsTestResults.'
	</td>
	<td align="left" style="color: black; font-size: 12px; font-weight: normal; font-family: Times New Roman;" valign="top">
	'.$GraphData_row->ResultsTestLowRange.' 
	</td>
	<td align="left" style="color: black; font-size: 12px; font-weight: normal; font-family: Times New Roman;" valign="top">
	'.$GraphData_row->ResultsTestHighRange.'
	</td>
	</tr>
	</cfoutput>
	</table>
</td>
</tr>
</cfif>
</table>';
?>
