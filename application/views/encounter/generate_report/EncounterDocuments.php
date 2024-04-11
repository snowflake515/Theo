<?php
// <!---
// 09/22/2011 CH  - CASE 10,032(Sugar 112)
// 2/6/12   CH  CASE 250 - Added needed url flowSheet_Id variable  value for flowsheetdisplay.cfm
// --->
//
// <cfparam name="URL.EncounterId">
// <cfparam name="URL.Mode" default="VIEW">
// <cfparam name="URL.BatchJob" default="0">
//
// <cflock scope="Session" type="EXCLUSIVE" timeout="10">
// <cfset Variables.sOrgId=Session.Org_Id>
// <cfset Variables.sId=Session.Id>
// <!---CASE 10,032(Sugar 112) Added sUserId--->
// <cfset Variables.sUserId=Session.User_Id>
// </cflock>
//
// <cfif URL.MODE EQ "WRITE">
// <cfset Variables.EncounterTempFileName="#CreateUUID()##RandRange(1,100000)#.html">
// <cfset Variables.EncounterTempFileNameZip="#CreateUUID()##RandRange(1,100000)#.zip">
// <cfset Variables.EncounterTempDirectory="#CreateUUID()##RandRange(1,100000)#">
//
// <cfif DirectoryExists("#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#")>
//   <cfdirectory directory="#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#" name="qDirectoryList0" action="LIST">
//   <cfloop query="qDirectoryList0">
//     <cffile action="DELETE" file="#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#\#qDirectoryList0.Name#">
//   </cfloop>
//   <cfdirectory action = "delete" directory = "#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#">
//   <cfdirectory action = "create" directory = "#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#">
// <cfelse>
//   <cfdirectory action = "create" directory = "#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#">
// </cfif>
//
// <cfquery name="GetEncounter" datasource="#Variables.EMRDataSource#">
// Select Top 1
//    Provider_Id,
//    Patient_Id,
//    Dept_Id,
//    EncounterDescription_Id,
//    Convert(Char,EncounterDate,101) AS TheDate
// From EncounterHistory
// Where Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Trim(URL.EncounterId)#">
// </cfquery>
//
// <cfsavecontent variable="content">
//     <cfset Url.DeptKey="#GetEncounter.Dept_Id#">
//   <cfset Url.ProviderKey="#GetEncounter.Provider_Id#">
//   <cfset Url.PatientKey="#GetEncounter.Patient_Id#">
//   <cfset Url.PrimaryKey="#Trim(URL.EncounterId)#">
//   <cfset Url.EncounterDescriptionKey="#GetEncounter.EncounterDescription_Id#">
//   <cfset Url.DateKey="#Trim(GetEncounter.TheDate)#">
//   <cfset Variables.TempEncounterDocumentDirectory = "#Variables.TempFilesDirectory#\#Variables.EncounterTempDirectory#">
//   <cfset Variables.CreateLockedNote = 1>
//   <cfinclude template="printchartnotes.cfm">
// </cfsavecontent>

$this->load->view('encounter/printchartnotes');
//
// <cffile action = "write" file = "#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#\#EncounterTempFileName#" output = "#Variables.content#">
//
// <!--- CASE 10,032(Sugar 112) - Get List of Requested Flowsheets for this  --->
// <cfquery name="FlowEncountersKeys" datasource="#Variables.EMRDataSource#">
//   SELECT	ComponentKeys,
//       Patient_id,
//       EncounterDate,
//       Encounter_Id
//   FROM	EncounterComponents
//   WHERE	Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Trim(URL.EncounterId)#">
//       AND HeaderMaster_Id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="153">
// </cfquery>
//
// <cfif FlowEncountersKeys.RecordCount neq 0>
//   <!---CASE 10,032(Sugar 112) Added Cfif to make sure these functions are only created once. The information are done as includes since CF seems to process <scripts> even
//      if inluded in a cfif.  --->
//     <cfif Not IsDefined('Variables.RunOnlyOnce')>
//       <cfinclude template="ChartNoteAttachmentPrinting2Functions.cfm">
//       <cfinclude template="VitalsConversion.cfm">
//       <cfset Variables.RunOnlyOnce = 'True'>
//     </cfif>
//     <cfset Variables.FlowSheetOnChartNote = 'Yes'>
//     <cfparam name="Attributes.EMRDATASOURCE" default="#Variables.EMRDataSource#">
//     <cfloop index="FSNum" list="#FlowEncountersKeys.ComponentKeys#" delimiters="," >
//       <cfquery datasource="#Variables.EMRDataSource#" name="CheckDisplayFormat">
//         SELECT
//             isnull(FSD.DisplayFormat, 0) as DisplayFormat,
//             isnull(FSD.DateDisplayDefault, 0) as DateDisplayDefault,
//             FS.FlowSheet_id,
//             FSD.Title
//         FROM 	Flowsheets FS
//               JOIN FlowSheetDefinitions FSD
//                   ON FS.FSDefinition_Id=FSD.FSDefinition_Id
//         WHERE 	FS.FlowSheet_Id = #FSNum#
//       </cfquery>
//
//       <cfquery datasource="#Variables.EMRDataSource#" name="FlowSheetDate">
//         SELECT 		MIN(d.datadate) as firstdate
//         FROM		FlowSheetDefinitions f,
//               FlowSheetDates d,
//               FlowSheets fs
//         WHERE		fs.FlowSheet_Id = #FSNum#
//               AND fs.FSDefinition_id = f.FSDefinition_id
//               AND fs.Flowsheet_id = d.Flowsheet_id
//               AND(fs.Hidden <> <cfqueryparam cfsqltype="cf_sql_bit" value="1"> OR fs.Hidden is NULL)
//       </cfquery>
//
//       <cfset Variables.FSNum = CheckDisplayFormat.FlowSheet_id>
//       <cfset Variables.RootFileName1=Trim(Variables.sUserId)&Variables.sOrgId&Variables.sId&Month(Now())&Day(Now())&Year(Now())&TimeFormat(Now(),"hh")&TimeFormat(Now(),"mm")&TimeFormat(Now(),"ss")&RandRange(1,1000)&".xls">
//       <cfset Variables.FileName1=Variables.RootFileName1>
//       <cfinclude template="patientprofilequery.cfm">
//       <cfset variables.mode='excel'>
//       <cfset variables.ChartNotePrintLandscape = "Yes">
//       <cfif IsDefined('url.DisplayDates')>
//         <cfset variables.DisplayDates = url.DisplayDates>
//       <cfelse>
//         <cfset variables.DisplayDates = -1>
//       </cfif>
//       <cfinclude template="CompFlowSheetDataLoad.cfm">
//       <!--- Capture the flowsheet data --->
//       <!--- CASE 250 - Added cfset to provide needed url variable value for flowsheetdisplay.cfm--->
//       <cfset url.flowSheet_Id = #FSNum#>
//       <cfsavecontent variable="Variables.ExcelOut" >
//         <cfinclude template="flowsheetdisplay.cfm">
//       </cfsavecontent>
//
//       <cffile file="#variables.TempFilesDirectory##Variables.FileName1#" action="Write" output="#Variables.ExcelOut#">
//       <cffile action="readbinary" file="#variables.TempFilesDirectory##Variables.FileName1#" variable="Local.binaryFile">
//
//       <cfquery name="GetDeptInfo" datasource="#Variables.EMRDataSource#" >
//         SELECT 	Top 1
//             Dept_Id
//         FROM	EncounterHistory
//         WHERE	Encounter_Id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Trim(URL.EncounterId)#">
//       </cfquery>
//
//       <!---NOTE: PatientProfile. items are from query PatientProfile in cfinclude in EncounterDocuments
//             CheckDisplayFormat. items are from query CheckDisplayFormat in 	 --->
//       <cfquery name="InsertFlowSheetAttachment" datasource="#Variables.EMRDataSource#" >
//         SET NoCount On;
//         INSERT INTO	Attachments
//               (
//               Patient_Id,
//               Org_Id,
//               Provider_Id,
//               Dept_Id,
//               EncounterDate,
//               Description,
//               User_Id,
//               Encounter_Id,
//               NomMaster_Id,
//               Signedoff,
//               Hidden,
//               DateLastAccessed,
//               EncounterFlowSheet
//               )
//         VALUES
//               (
//               <cfqueryparam cfsqltype="cf_sql_bigint" value="#FlowEncountersKeys.Patient_id#">,
//               <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfile.Org_ID#">,
//               <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfile.Provider_ID#">,
//               <cfqueryparam cfsqltype="cf_sql_bigint" value="#GetDeptInfo.Dept_Id#">,
//               <cfqueryparam cfsqltype="cf_sql_timestamp" value="#CreateODBCDate(FlowEncountersKeys.EncounterDate)#">,
//               <cfqueryparam cfsqltype="cf_sql_varchar" value="#Trim(Left(CheckDisplayFormat.Title,50))#">,
//               <cfqueryparam cfsqltype="cf_sql_varchar" value="#Variables.sUserId#">,
//               <cfqueryparam cfsqltype="cf_sql_bigint" value="#FlowEncountersKeys.Encounter_Id#">,
//               <cfqueryparam cfsqltype="cf_sql_bigint" value="0">,
//               <cfqueryparam cfsqltype="cf_sql_bit" value="1">,
//               <cfqueryparam cfsqltype="cf_sql_bit" value="0">,
//               <cfqueryparam cfsqltype="cf_sql_timestamp" value="#CreateODBCDateTime(FlowSheetDate.firstdate)#">,
//               <cfqueryparam cfsqltype="cf_sql_bit" value="1">
//               )
//         Select Scope_Identity() as 'NewId'
//         Set NoCount Off;
//       </cfquery>
//
//       <cfquery datasource="#Variables.ImageDataSource#" name="InsertFlowSheetFile">
//         INSERT INTO	AttachmentsImages
//               (
//               Attachments_Id,
//               ImageFile,
//               ImageType,
//               tnImage
//               )
//         VALUES
//               (
//               <cfqueryparam cfsqltype="cf_sql_bigint" value="#InsertFlowSheetAttachment.NewId#">,
//               <cfqueryparam cfsqltype="cf_sql_blob" value="#Local.binaryFile#">,
//               <cfqueryparam cfsqltype="cf_sql_varchar" value="xls">,
//               NULL
//               )
//       </cfquery>
//
//       <!---Find if there is an existing Attachments encounter componant record --->
//       <cfquery name="ExistingAttachmentKeys" datasource="#Variables.EMRDataSource#">
//         SELECT	TOP 1
//             ComponentKeys
//         FROM	EncounterComponents
//         WHERE	Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Trim(URL.EncounterId)#">
//             AND HeaderMaster_Id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="2">
//       </cfquery>
//
//       <cfif ExistingAttachmentKeys.RecordCount neq 0>
//         <!---There are already existing attachments for this encounter, so add this one --->
//         <cfset Variables.NewKeys = ListAppend(ExistingAttachmentKeys.ComponentKeys,InsertFlowSheetAttachment.NewId)>
//
//         <cfquery name="UpdateAttachmentKeys" datasource="#Variables.EMRDataSource#">
//           UPDATE	EncounterComponents
//           SET		ComponentKeys = <cfqueryparam cfsqltype="cf_sql_varchar" value="#Variables.NewKeys#">
//           WHERE	Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Trim(URL.EncounterId)#">
//               AND HeaderMaster_Id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="2">
//         </cfquery>
//       <cfelse>
//         <cfquery name="AddNewAttachmentKey" datasource="#Variables.EMRDataSource#">
//           INSERT INTO	EncounterComponents
//                 (
//                 Patient_ID,
//                 Encounter_ID,
//                 EncounterDate,
//                 HeaderMaster_ID,
//                 ComponentKeys,
//                 DateCreated
//                 )
//           VALUES		(
//                 <cfqueryparam cfsqltype="cf_sql_integer" value="#FlowEncountersKeys.Patient_id#">,
//                 <cfqueryparam cfsqltype="cf_sql_integer" value="#FlowEncountersKeys.Encounter_Id#">,
//                 <cfqueryparam cfsqltype="cf_sql_timestamp" value="#CreateODBCDate(FlowEncountersKeys.EncounterDate)#">,
//                 <cfqueryparam cfsqltype="cf_sql_bigint" value="2">,
//                 <cfqueryparam cfsqltype="cf_sql_varchar" value="#InsertFlowSheetAttachment.NewId#">,
//                 <cfqueryparam cfsqltype="cf_sql_timestamp" value="#CreateODBCDateTime(Now())#">
//                 )
//         </cfquery>
//       </cfif>
//     </cfloop>
//   </cfif>
//
//  <cfzip
// action="zip"
// source="#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#\"
// file="#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#\#EncounterTempFileNameZip#"
// overwrite="true">
//
// <cffile action="readbinary" file="#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#\#EncounterTempFileNameZip#" variable="BinDoc">
//
// <cfquery datasource="#Variables.ImageDataSource#" name="CheckForRecordExists">
// Select TOP 1
//        Encounter_id
// From EncounterDocuments
// where encounter_id=<cfqueryparam value="#Trim(URL.EncounterId)#" cfsqltype="cf_sql_bigint">
// </cfquery>
//
// <cfif CheckForRecordExists.RecordCount EQ 0>
//   <cfquery name="InsertFile" datasource="#Variables.ImageDataSource#">
//     Insert Into EncounterDocuments (Encounter_Id,ImageFile,ImageType,BatchJob)
//   VALUES
//   (<cfqueryparam value="#Trim(URL.EncounterId)#" cfsqltype="cf_sql_bigint">,
//    <cfqueryparam value="#Variables.BinDoc#" cfsqltype="cf_sql_blob">,
//      <cfqueryparam value="zip" cfsqltype="cf_sql_varchar">,
//    <cfqueryparam cfsqltype="CF_SQL_BIT" value="#Trim(URL.BatchJob)#">
//      )
//   </cfquery>
// </cfif>
// <!--- delete the temp directory --->
// <cfif DirectoryExists("#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#")>
//   <cfdirectory directory="#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#" name="qDirectoryDeleteList" action="LIST">
//   <cfloop query="qDirectoryDeleteList">
//     <cffile action="DELETE" file="#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#\#qDirectoryDeleteList.Name#">
//   </cfloop>
//   <cfdirectory action = "delete" directory = "#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#">
// </cfif>
// <cfelse> <!--- View Document --->
// <cfquery name="getFile" datasource="#Variables.ImageDataSource#">
// Select ImageFile
// From EncounterDocuments
// Where Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Trim(URL.EncounterId)#">
// </cfquery>
//
// <cfset Variables.EncounterTempDirectory="#CreateUUID()##RandRange(1,100000)#">
// <cfset Variables.EncounterTempFileNameZip="#CreateUUID()##RandRange(1,100000)#.zip">
//
// <cfif DirectoryExists("#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#")>
//   <cfdirectory directory="#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#" name="qDirectoryList0" action="LIST">
//   <cfloop query="qDirectoryList0">
//     <cffile action="DELETE" file="#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#\#qDirectoryList0.Name#">
//   </cfloop>
//   <cfdirectory action = "delete" directory = "#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#">
//   <cfdirectory action = "create" directory = "#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#">
// <cfelse>
//   <cfdirectory action = "create" directory = "#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#">
// </cfif>
//
// <cffile action="WRITE" file="#Variables.TempFilesDirectory##Variables.EncounterTempFileNameZip#" output="#getFile.ImageFile#">
//
// <cfzip
//   action = "unzip"
//   file = "#Variables.TempFilesDirectory##Variables.EncounterTempFileNameZip#"
//   destination = "#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#\">
//
// <cffile action = "delete" file = "#Variables.TempFilesDirectory##Variables.EncounterTempFileNameZip#">
//
// <cfdirectory filter="*.html" directory="#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#" name="qFileList" action="LIST">
//
// <cffile action = "read" file = "#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#\#qFileList.name#" variable = "TempFileContent">
//
// <cfset Variables.TempFileContent = ReplaceNoCase(Variables.TempFileContent,"$$SOMEECASTABSOLUTEPATH$$","","ALL")>
//
// <cffile action="DELETE" file="#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#\#qFileList.Name#">
//
// <cffile action = "write" file = "#Variables.TempFilesDirectory##Variables.EncounterTempDirectory#\#qFileList.Name#" output = "#Variables.TempFileContent#">
//
// <html>
// <cfoutput>
// <script>
//   self.location.href='#Variables.RelativeTempFilesDirectory##Variables.EncounterTempDirectory#/#qFileList.name#';
// </script>
// </cfoutput>
// </html>
// </cfif>
?>
