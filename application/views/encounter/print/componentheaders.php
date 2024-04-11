<?php

//
//<!--- Module: ComponentHeaders.cfm
//
//Change Log
//JWY 2/26/08 - Do not allow Caller.Records to be set back to 0 after having another value. If this
//happens, the rest of the template records are ignored and it goes right to the signature.
//
//--->
//<cfquery datasource = "#Attributes.EMRDataSource#" name = "HeaderSettings">
//Select TOP 1
//H.Header_Id,
// H.HeaderMaster_Id,
// H.HeaderText,
// H.HeaderStyle,
// H.HeaderSize,
// H.HeaderColor,
// F.FontName
//From EncounterHeaders H
//Join Fonts F
//On H.Font_Id = F.Font_Id
//<cfif Attributes.HeaderMasterKey NEQ 1>
//Join EncounterComponents C
//On H.HeaderMaster_Id = C.HeaderMaster_Id
//</cfif>
//<cfif Attributes.FreeTextKey EQ 0>
//And C.HeaderMaster_Id = <cfqueryparam cfsqltype = "CF_SQL_INTEGER" value = "#Attributes.HeaderMasterKey#">
//And C.Patient_Id = <cfqueryparam cfsqltype = "CF_SQL_INTEGER" value = "#Attributes.PatientKey#">
//</cfif>
//Where H.Header_Id = <cfqueryparam cfsqltype = "CF_SQL_INTEGER" value = "#Attributes.HeaderKey#">
//<cfif Attributes.SOHeaders EQ 0>
//And (H.Hidden<><cfqueryparam cfsqltype = "CF_SQL_BIT" value = "1"> OR H.Hidden IS NULL)
//</cfif>
//</cfquery>

$sql = "Select TOP 1
H.Header_Id,
 H.HeaderMaster_Id,
 H.HeaderText,
 H.HeaderStyle,
 H.HeaderSize,
 H.HeaderColor,
 F.FontName
From  " . $data_db . ".dbo.EncounterHeaders H
Join  " . $data_db . ".dbo.Fonts F
On H.Font_Id = F.Font_Id";

if ($HeaderMasterKey != 1) {
  $sql .= " Join EncounterComponents C
On H.HeaderMaster_Id = C.HeaderMaster_Id";
}

if ($FreeTextKey == 0) {
  $sql .= " And C.HeaderMaster_Id = $HeaderMasterKey AND
C.Patient_Id = $PatientKey";
}
$sql .= " Where H.Header_Id = $HeaderKey";

if ($SOHeaders == 0) {
  $sql .= " And (H.Hidden<> 1 OR H.Hidden IS NULL)";
}
$Records = $this->ReportModel->data_db->query($sql);
$Records_num = $Records->num_rows();
$Records_row = $Records->row();

//echo $Records_row->HeaderMaster_Id;
//<!---
//<cfif Caller.Records EQ 0>
//<cfset Caller.Records = HeaderSettings.RecordCount>
//</cfif>
//<cfset Caller.OutputMasterKey = 0>
//--->
//<cfif HeaderSettings.RecordCount NEQ 0>
//<!--- <cfset Caller.OutputMasterKey = HeaderSettings.HeaderMaster_Id> --->
//<cfoutput>
//<cfset FontColor = "color: #HeaderSettings.HeaderColor#;">
//<cfset FontSize = "font-size: #HeaderSettings.HeaderSize#px;">
//<cfset FontFace = "font-family: #HeaderSettings.FontName#;">
//
//<cfif HeaderSettings.HeaderStyle Contains "B">
//<cfset Variables.FontWeight = "font-weight: bold;">
//<cfelse>
//<cfset Variables.FontWeight = "">
//</cfif>
//<cfif HeaderSettings.HeaderStyle Contains "I">
//<cfset Variables.FontStyle = "font-style: italic;">
//<cfelse>
//<cfset Variables.FontStyle = "">
//</cfif>
//<cfif HeaderSettings.HeaderStyle Contains "U">
//<cfset Variables.FontDecoration = "text-decoration: underline;">
//<cfelse>
//<cfset Variables.FontDecoration = "">
//</cfif>
//<cfif HeaderSettings.HeaderMaster_Id EQ 1><br></cfif>
//<span style = "#Trim(Variables.FontColor)# #Trim(Variables.FontSize)# #Trim(Variables.FontFace)# #Trim(Variables.FontWeight)# #Trim(Variables.FontStyle)# #Variables.FontDecoration#">
//#Trim(HeaderSettings.HeaderText)#
//<cfif Trim(HeaderSettings.HeaderText) NEQ ""><br></cfif>
//</span>
//</cfoutput>
//</cfif>

if ($Records_num != 0) {
  $FontColor = "color : #".$Records_row->HeaderColor.'; ';
  $FontSize = "font-size: $Records_row->HeaderSize" . "px;";
  $FontFace = "font-family: $Records_row->FontName;";

  $FontWeight = "";
  if ($Records_row->HeaderStyle == "B") {
    $FontWeight = "font-weight: bold;";
  } else if ($Records_row->HeaderStyle == "I") {
    $FontWeight = "font-style: italic;";
  } else if ($Records_row->HeaderStyle == "U") {
    $FontWeight = "text-decoration: underline;";
  }

  if ($Records_row->HeaderMaster_Id == 1) {
    echo "<br>";
  }
  echo "<span style = '" . trim($FontColor) . " " . trim($FontSize) . " " . trim($FontFace) . " " . trim($FontWeight) . " '>";
  echo trim($Records_row->HeaderText);
  if ($Records_row->HeaderText != "") {
    echo "<br>";
  }
  echo "</span>";
}
?>
