<?php

//<!--- Module: HeaderNeeded.cfm
//
//--->
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="HeaderSettings">
//Select TOP 1
//       H.Header_Id,
//       H.HeaderMaster_Id,
//       H.HeaderText,
//	   H.HeaderStyle,
//	   H.HeaderSize,
//	   H.HeaderColor,
//	   F.FontName
//  From EncounterHeaders H
//  Join Fonts F
//    On H.Font_Id=F.Font_Id
//  <cfif Attributes.HeaderMasterKey NEQ 1>
//  Join EncounterComponents C
//    On H.HeaderMaster_Id=C.HeaderMaster_Id
//  </cfif>
//  <cfif Attributes.FreeTextKey EQ 0>
//   And C.HeaderMaster_Id=<cfqueryparam cfsqltype="CF_SQL_INTEGER" value="#Attributes.HeaderMasterKey#">
//   And C.Patient_Id=<cfqueryparam cfsqltype="CF_SQL_INTEGER" value="#Attributes.PatientKey#">
//  </cfif>
// Where H.Header_Id=<cfqueryparam cfsqltype="CF_SQL_INTEGER" value="#Attributes.HeaderKey#">
// <cfif Attributes.SOHeaders EQ 0>
//   And (H.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> OR H.Hidden IS NULL)
// </cfif>
//</cfquery>
//
//
$sql_add1 = NULL;
if ($HeaderMasterKey != 1) {
  $sql_add1 = "Join " . $data_db . ".dbo.EncounterComponents C
    On H.HeaderMaster_Id=C.HeaderMaster_Id";
}

$sql_add2 = NULL;
if ($FreeTextKey == 0) {
  $sql_add2 = "  And C.HeaderMaster_Id=$HeaderMasterKey
   And C.Patient_Id=$PatientKey ";
}

$sql_add3 = NULL;
if ($SOHeaders == 0) {
  $sql_add3 = "And (H.Hidden <> 1 OR H.Hidden IS NULL)";
}

$sql = "Select TOP 1
       H.Header_Id,
       H.HeaderMaster_Id,
       H.HeaderText,
	   H.HeaderStyle,
	   H.HeaderSize,
	   H.HeaderColor,
	   F.FontName
  From " . $data_db . ".dbo.EncounterHeaders H
  Join " . $data_db . ".dbo.Fonts F
    On H.Font_Id=F.Font_Id
  $sql_add1
  $sql_add2
 Where H.Header_Id=$HeaderKey
  $sql_add3";

$HeaderSettings = $this->ReportModel->data_db->query($sql);
$HeaderSettings_num_rows = $HeaderSettings->num_rows();
$HeaderSettings_row = $HeaderSettings->row();




//
//<cfif Caller.Records EQ 0>
//	<cfset Caller.Records=HeaderSettings.RecordCount>
//</cfif>
//<cfset Caller.OutputMasterKey=0>
//
//<cfif HeaderSettings.RecordCount NEQ 0>
//	<cfif Attributes.HeaderMasterKey EQ 1>
//		<cfset Caller.OutputMasterKey=HeaderSettings.HeaderMaster_Id>
//		<cfoutput>
//		<cfset FontColor="color: #HeaderSettings.HeaderColor#;">
//		<cfset FontSize="font-size: #HeaderSettings.HeaderSize#px;">
//		<cfset FontFace="font-family: #HeaderSettings.FontName#;">
//
//		<cfif HeaderSettings.HeaderStyle Contains "B">
//			<cfset Variables.FontWeight="font-weight: bold;">
//		<cfelse>
//			<cfset Variables.FontWeight="">
//		</cfif>
//		<cfif HeaderSettings.HeaderStyle Contains "I">
//			<cfset Variables.FontStyle="font-style: italic;">
//		<cfelse>
//			<cfset Variables.FontStyle="">
//		</cfif>
//		<cfif HeaderSettings.HeaderStyle Contains "U">
//			<cfset Variables.FontDecoration="text-decoration: underline;">
//		<cfelse>
//			<cfset Variables.FontDecoration="">
//		</cfif>
//		<cfif HeaderSettings.HeaderMaster_Id EQ 1><br></cfif>
//		<span style="#Trim(Variables.FontColor)# #Trim(Variables.FontSize)# #Trim(Variables.FontFace)# #Trim(Variables.FontWeight)# #Trim(Variables.FontStyle)# #Variables.FontDecoration#">
//		#Trim(HeaderSettings.HeaderText)#
//		<cfif Trim(HeaderSettings.HeaderText) NEQ ""><br></cfif>
//		</span>
//		</cfoutput>
//	<cfelse>
//		<cfset caller.HeaderNeeded = True>
//	</cfif>
//</cfif>
//
//


if ($HeaderSettings_num_rows > 0) {
  if ($HeaderMasterKey == 1) {

//		<cfoutput>
//		<cfset FontColor="color: #HeaderSettings.HeaderColor#;">
//		<cfset FontSize="font-size: #HeaderSettings.HeaderSize#px;">
//		<cfset FontFace="font-family: #HeaderSettings.FontName#;">

    $this->FontColor = "color: #" . $HeaderSettings_row->HeaderColor . ";";
    $this->FontSize = "font-size: " . $HeaderSettings_row->HeaderSize . "px;";
    $this->FontFace = "font-family: " . $HeaderSettings_row->FontName . ";";

//		<cfif HeaderSettings.HeaderStyle Contains "B">
//			<cfset Variables.FontWeight="font-weight: bold;">
//		<cfelse>
//			<cfset Variables.FontWeight="">
//		</cfif>

    $this->FontWeight = "";
    if ($HeaderSettings_row->HeaderStyle == "B") {
      $this->FontWeight = "font-weight: bold;";
    }
//		<cfif HeaderSettings.HeaderStyle Contains "I">
//			<cfset Variables.FontStyle="font-style: italic;">
//		<cfelse>
//			<cfset Variables.FontStyle="">
//		</cfif>

    $this->FontStyle = "";
    if ($HeaderSettings_row->HeaderStyle == "I") {
      $this->FontStyle = "font-style: italic;";
    }
//		<cfif HeaderSettings.HeaderStyle Contains "U">
//			<cfset Variables.FontDecoration="text-decoration: underline;">
//		<cfelse>
//			<cfset Variables.FontDecoration="">
//		</cfif>

    $this->FontDecoration = "";
    if ($HeaderSettings_row->HeaderStyle == "U") {
      $this->FontStyle = "text-decoration: underline;";
    }

//		<cfif HeaderSettings.HeaderMaster_Id EQ 1><br></cfif>

    if ($HeaderSettings_row->HeaderMaster_Id == 1) {
      echo "<br/>";
    }

//    	<span style="#Trim(Variables.FontColor)# #Trim(Variables.FontSize)# #Trim(Variables.FontFace)# #Trim(Variables.FontWeight)# #Trim(Variables.FontStyle)# #Variables.FontDecoration#">
//		#Trim(HeaderSettings.HeaderText)#
//		<cfif Trim(HeaderSettings.HeaderText) NEQ ""><br></cfif>
//		</span>

    echo "<span style='" . trim($this->FontColor) . " " . trim($this->FontSize) . " " . trim($this->FontFace) . " " . trim($this->FontWeight) . " " . trim($this->FontStyle) . " " . trim($this->FontDecoration) . "'>";
    echo trim($HeaderSettings_row->HeaderText);
    if ($HeaderSettings_row->HeaderText != "") {
      echo "<br/>";
    }
    echo "</span>";

//		</cfoutput>
  }else{
    $this->HeaderNeeded = TRUE;
  }
}


//
//
//
?>
