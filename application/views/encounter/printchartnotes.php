<?php
//================ this from
// printchartnotes.cfm
//================
//
//
//
//
//
//
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.sId=Session.Id>
//	<cfset Variables.sUserId=Session.User_Id>
//	<cfset Variables.sOrgId=Session.Org_Id>
//</cflock>
//
//<cfparam name="Url.FaxKey" default="0">
//<cfparam name="Url.DeptKey" default="0">
//<cfparam name="Url.ProviderKey" default="0">
//<cfparam name="Url.PatientKey" default="0">
//<cfparam name="Url.PrimaryKey" default="0">
//<cfparam name="Url.EncounterDescriptionKey" default="0">
//<cfparam name="Url.DateKey" default="04/04/1900">
//<cfparam name="Variables.Records" default="0">
//<cfparam name="Variables.CreateLockedNote" default="0">
//<cfparam name="URL.PrintPHAOnly" default="0">
//<cfparam name="Variables.GenerateAVS" default="0">
//
//
//
//

$dt = $this->EncounterHistoryModel->get_by_id($id)->row();
$Encounter_Id = (int) $id;
$Patient_Id = (int) $dt->Patient_ID;
$Provider_Id = (int) $dt->Provider_ID;
$EncounterDescription_Id = (int) $dt->EncounterDescription_ID;
$patient_dt =  $this->PatientProfileModel->get_by_id($Patient_Id)->row();

//
//<!---  Variables.TempEncounterDocumentDirectory used for writing the signedoff version of the chart note--->
//<cfparam name="Variables.TempEncounterDocumentDirectory" default="">
//
//<cfif IsDefined("URL.Print")>
//	<cfset AuditMode="P">
//<cfelse>
//	<cfset AuditMode="V">
//</cfif>
//<cfset AuditRecord=Url.PrimaryKey>
//<cfset AuditTrail="E">
//
//<cfif IsDefined("URL.Print")>
//	<cfif Variables.CreateLockedNote EQ 1>
//		<cfset Variables.IncludeAttachments = 0>
//	<cfelse>
//		<cfset Variables.IncludeAttachments = 1>
//	</cfif>
//<cfelse>
//	<cfset Variables.IncludeAttachments = 0>
//</cfif>
//
//
//
//<cfquery datasource="#Variables.EMRDataSource#" name="TemplateMasterId">
//Select DISTINCT
//       T.TML2_HeaderMaster_Id
//  From #Variables.TemplateDataSource#.dbo.TML2 T,
//       #Variables.DSNPrefix#eCastEMR_Data.dbo.ETL2 E
// Where E.Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Url.PrimaryKey#">
//   And E.TML2_Id=T.TML2_Id
//</cfquery>



$sql = " Select DISTINCT
       T.TML2_HeaderMaster_Id
        From " . $template_db . ".dbo.TML2 T,
             " . $data_db . ".dbo.ETL2 E
       Where E.Encounter_Id= $Encounter_Id
         And E.TML2_Id=T.TML2_Id  ";
$template_master_id = $this->ReportModel->data_db->query($sql);
$template_master_id_num = $template_master_id->num_rows();
$template_master_result = $template_master_id->result();



//<cfset TemplateStruct=StructNew()>
//<cfif TemplateMasterId.RecordCount NEQ 0>
//	<cfloop query="TemplateMasterId">
//		<cfset Temp=StructInsert(TemplateStruct,TemplateMasterId.TML2_HeaderMaster_Id,TemplateMasterId.TML2_HeaderMaster_Id,TRUE)>
//	</cfloop>
//</cfif>
$TemplateStruct = array();
if ($template_master_id_num > 0) {
  foreach ($template_master_result as $template_master_dt) {
    $TemplateStruct[] = $template_master_dt->TML2_HeaderMaster_Id;
  }
}

//
//
//<cfquery datasource="#Variables.EMRDataSource#" name="PatientHeader">
//Select TOP 1
//       AccountNumber,
//       FirstName+' '+MiddleName+' '+LastName AS PatientFullName
//  From PatientProfile
// Where Patient_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Url.PatientKey#">
//</cfquery>
//
//
//


$sql = "Select TOP 1
       AccountNumber,
       FirstName+' '+MiddleName+' '+LastName AS PatientFullName
        From " . $data_db . ".dbo.PatientProfile
       Where Patient_Id = $Patient_Id  ";
$PatientHeader = $this->ReportModel->data_db->query($sql);


//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
//<html>
//<head>
//<title>Print ChartNotes</title>
//<style type="text/css" media="all">
//body {margin-top: 0.25in;
//      margin-left: 0.25in;
//	  margin-right: 0.25in;
//	  margin-bottom: 0.25in;
//	  word-wrap: break-word;
//      }
//</style>
//<script language="JavaScript" defer>
//function ScrollBar()
//{
//document.body.style.scrollbarBaseColor='#808080';
//document.body.style.scrollbarArrowColor='#FFFFFF';
//document.body.style.scrollbarHighlightColor='#FFFFFF';
//}
//</script>
//</head>
//
//
?>

<!DOCTYPE html>
<html>
  <head>
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
     <?php
      $title = "Print Chart Notes";
     if(!empty($patient_dt->FirstName)){
       $title.=" for ".implode(', ', array_filter(array($patient_dt->LastName, $patient_dt->FirstName)));
     }
     ?>
    <title><?php echo $title?></title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/ace/img/faicon.ico') ?>">
    <style type="text/css" media="all">
      body {margin-top: 0.25in;
            margin-left: 0.25in;
            margin-right: 0.25in;
            margin-bottom: 0.25in;
            word-wrap: break-word;
      }
    </style>
    <script language="JavaScript" defer>
      function ScrollBar()
      {
        document.body.style.scrollbarBaseColor = '#808080';
        document.body.style.scrollbarArrowColor = '#FFFFFF';
        document.body.style.scrollbarHighlightColor = '#FFFFFF';
      }
    </script>

    <?php
    if ($print_mode == 'print') {
      ?>

      <script>
         // window.print();
      </script>
      <?php
    }
    ?>
  </head>


  <?php
//
//<cfquery datasource="#Variables.EMRDataSource#" name="SOHeaders">
//Select TOP 1
//       HeaderIds
//  From SOChartHeaders
// Where Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Url.PrimaryKey#">
//</cfquery>
//
//
//

  $sql = "Select TOP 1
          HeaderIds
     From " . $data_db . ".dbo.SOChartHeaders
    Where Encounter_Id= $Encounter_Id";
  $so_headers = $this->ReportModel->data_db->query($sql);
  $so_headers_num = $so_headers->num_rows();
  $so_headers_row = $so_headers->row();


//<cfif (SOHeaders.RecordCount EQ 0) OR (Trim(SOHeaders.HeaderIds) EQ "") OR (Variables.GenerateAVS EQ 1)>
//	<cfquery datasource="#Variables.EMRDataSource#" name="ModuleSettings">
//		Select
//			H.Header_Id,
//			H.HeaderMaster_Id,
//			H.HeaderOrder,
//			M.Component,
//			M.FreeTextYN
//		From EncounterHeaders H
//		Left Outer Join HeaderMaster M
//			On H.HeaderMaster_Id=M.HeaderMaster_Id
//		Where H.Provider_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Url.ProviderKey#">
//			And H.EncounterDescription_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Url.EncounterDescriptionKey#">
//			And (H.Hidden<><cfqueryparam cfsqltype="CF_SQL_BIT" value="1"> Or H.Hidden IS NULL)
//			<cfif (URL.PrintPHAOnly EQ 1)>
//				And (H.HeaderMaster_Id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="149">)
//			</cfif>
//		Order By H.HeaderOrder
//	</cfquery>
//<cfelse>
//	<cfquery datasource="#Variables.EMRDataSource#" name="ModuleSettings">
//		Select
//			H.Header_Id,
//			H.HeaderMaster_Id,
//			H.HeaderOrder,
//			M.Component,
//			M.FreeTextYN
//		From EncounterHeaders H
//		Left Outer Join HeaderMaster M
//			On H.HeaderMaster_Id=M.HeaderMaster_Id
//		Where H.Header_Id IN (<cfqueryparam list="Yes" separator="," value="#Valuelist(SOHeaders.HeaderIds,',')#">)
//			<cfif (URL.PrintPHAOnly EQ 1)>
//				And (H.HeaderMaster_Id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="149">)
//			</cfif>
//		Order By H.HeaderOrder
//	</cfquery>
//</cfif>

  $PrintPHAOnly = $PrintPatientOnly; //bolean
  if ($so_headers_num == 0 || $so_headers_row->HeaderIds == "") {
    $add = "";
    if ($PrintPHAOnly) {
      $add = " And (H.HeaderMaster_Id = 149 or H.HeaderMaster_Id = 148) ";
    }
    $sql = "Select
			H.Header_Id,
			H.HeaderMaster_Id,
			H.HeaderOrder,
			M.Component,
			M.FreeTextYN
		From " . $data_db . ".dbo.EncounterHeaders H
		Left Outer Join " . $data_db . ".dbo.HeaderMaster M
			On H.HeaderMaster_Id=M.HeaderMaster_Id
		Where H.Provider_Id = $Provider_Id
			And H.EncounterDescription_Id = $EncounterDescription_Id
			And (H.Hidden <> 1 Or H.Hidden IS NULL)
			$add " .
            "Order By H.HeaderOrder";
    $ModuleSettings = $this->ReportModel->data_db->query($sql);
    $ModuleSettings_result = $ModuleSettings->result();
  } else {
    if ($PrintPHAOnly) {
      $add = " And (H.HeaderMaster_Id = 149 or H.HeaderMaster_Id = 148) ";
    }
    $sql = "Select
			H.Header_Id,
			H.HeaderMaster_Id,
			H.HeaderOrder,
			M.Component,
			M.FreeTextYN
		From " . $data_db . ".dbo.EncounterHeaders H
		Left Outer Join " . $data_db . ".dbo.HeaderMaster M
			On H.HeaderMaster_Id=M.HeaderMaster_Id
		Where H.Header_Id IN ($HeaderIds)
			$add
		Order By H.HeaderOrder";

    $ModuleSettings = $this->ReportModel->data_db->query($sql);
    $ModuleSettings_result = $ModuleSettings->result();
  }

  //var_dump($ModuleSettings_result);
//<cfquery datasource="#Variables.EMRDataSource#" name="DefaultConfig">
//Select EncounterConfig_Id
//  From EncounterConfig
// Where EncounterDescription_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Url.EncounterDescriptionKey#">
//   And Provider_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Url.ProviderKey#">
//</cfquery>
//
//
  $sql = "Select EncounterConfig_Id
  From " . $data_db . ".dbo.EncounterConfig
  Where EncounterDescription_ID= $EncounterDescription_Id
   And Provider_Id=$Provider_Id";
  $DefaultConfig = $this->ReportModel->data_db->query($sql);
  $DefaultConfig_num = $DefaultConfig->num_rows();
  $DefaultConfig_result = $DefaultConfig->result();
  $DefaultConfig_row = $DefaultConfig->row();


//
//
//
//<cfif DefaultConfig.RecordCount EQ 0>
//	<cfquery datasource="#Variables.EMRDataSource#" name="ProviderDefaultConfig">
//	Select EncounterConfig_Id
//	  From EncounterConfig
//	 Where EncounterDescription_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="0">
//	   And Provider_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Url.ProviderKey#">
//	</cfquery>
//
//	<cfset variables.ProvDefConfig = ProviderDefaultConfig.EncounterConfig_Id>
//<cfelse>
//	<cfset variables.ProvDefConfig = DefaultConfig.EncounterConfig_Id>
//</cfif>
//
//


  if ($DefaultConfig_num == 0) {
    $sql = "Select EncounterConfig_ID
	   From " . $data_db . ".dbo.EncounterConfig
	   Where EncounterDescription_ID = 0
	   And Provider_Id =$Provider_Id";
    $ProviderDefaultConfig = $this->ReportModel->data_db->query($sql);
    $ProviderDefaultConfig_row = $ProviderDefaultConfig->row();
    $ProvDefConfig = ($ProviderDefaultConfig_row) ? $ProviderDefaultConfig_row->EncounterConfig_ID : 0;
  } else {
    $ProvDefConfig = $DefaultConfig_row->EncounterConfig_Id;
  }




//
//
//
//<cfquery datasource="#Variables.EMRDataSource#" name="HeaderMasterExist">
//Select HeaderMaster_Id,
//       EncounterComponents_Id
//  From EncounterComponents
// Where (((EncounterText IS NOT NULL) AND (EncounterText NOT LIKE '')) OR ComponentKeys IS NOT NULL)
//   And Patient_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Url.PatientKey#">
//   And Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Url.PrimaryKey#">
//</cfquery>
//
//

  $sql = "Select HeaderMaster_Id,
       EncounterComponents_Id
   From EncounterComponents
   Where (((EncounterText IS NOT NULL) AND (EncounterText NOT LIKE '')) OR ComponentKeys IS NOT NULL)
   And Patient_Id=$Patient_Id
   And Encounter_Id= $Encounter_Id";

  $HeaderMasterExist = $this->ReportModel->data_db->query($sql);
  $HeaderMasterExist_result = $HeaderMasterExist->result();

//
//<cfset HeaderMasterStruct=StructNew()>
//<cfif HeaderMasterExist.RecordCount NEQ 0>
//	<cfloop query="HeaderMasterExist">
//		<cfset Temp=StructInsert(HeaderMasterStruct,HeaderMasterExist.HeaderMaster_Id,HeaderMasterExist.HeaderMaster_Id,TRUE)>
//	</cfloop>
//</cfif>
//

  $HeaderMasterStruct = array();
  if ($HeaderMasterExist->num_rows() != 0) {
    //	<cfloop query="HeaderMasterExist">
    //		<cfset Temp=StructInsert(HeaderMasterStruct,HeaderMasterExist.HeaderMaster_Id,HeaderMasterExist.HeaderMaster_Id,TRUE)>
    //	</cfloop>
    foreach ($HeaderMasterExist_result as $value) {
      $HeaderMasterStruct[] = $value->HeaderMaster_Id;
    }
  }


//<cfoutput>
//<body bgcolor="##ffffff" leftmargin="0" topmargin="0" <!---onload="ScrollBar(); <cfif IsDefined('Url.P')>PrintScriptX('#Url.P#');</cfif>"--->>
//</cfoutput>
//
  ?>
  <body bgcolor="#ffffff" leftmargin="0" topmargin="0" >
    <div style="max-width: 672px; margin: 0 auto">
    <?php
//
//<cfif DefaultConfig.RecordCount EQ 0>
//	<cfmodule template="defaultheader.cfm"
//	 DeptKey="#Url.DeptKey#"
//	 ProviderKey="#Url.ProviderKey#"
//	 PatientKey="#Url.PatientKey#"
//	 EMRDataSource="#Variables.EMRDataSource#"
//	 ImageDataSource="#Variables.ImageDataSource#"
//	 RelativeFileUploadDirectory="#Variables.RelativeFileUploadDirectory#"
//	 PrimaryKey="#Url.PrimaryKey#"
//	 FaxKey="#Url.FaxKey#"
//	 ProductionServer="#Variables.ProductionServer#"
//	 DocumentDirectory="#Variables.TempEncounterDocumentDirectory#"
// 	 FileUploadDirectory="#Variables.FileUploadDirectory#"
//	 RelativeTempFilesDirectory="#Variables.RelativeTempFilesDirectory#"
//	 DatabaseIPAddress="#variables.DatabaseIPAddress#"
//	 DatabaseUserId="#Variables.DatabaseUserId#"
//	 DatabasePassword="#Variables.DatabasePassword#"
//	 DSNPreFix="#Variables.DSNPreFix#"
//	 TempFilesDirectory="#Variables.TempFilesDirectory#"
//	 FaxAttachmentsDirectory="#Variables.FaxAttachmentsDirectory#"
//	 >
//	<cfset variables.EncounterConfig_Id = 0>
//<cfelse>
//	<cfmodule template="customheader.cfm"
//	 DeptKey="#Url.DeptKey#"
//	 ProviderKey="#Url.ProviderKey#"
//	 PatientKey="#Url.PatientKey#"
//	 EMRDataSource="#Variables.EMRDataSource#"
//	 ImageDataSource="#Variables.ImageDataSource#"
//	 RelativeFileUploadDirectory="#Variables.RelativeFileUploadDirectory#"
//	 PrimaryKey="#Url.PrimaryKey#"
//	 FaxKey="#Url.FaxKey#"
//	 ProductionServer="#Variables.ProductionServer#"
//	 DocumentDirectory="#Variables.TempEncounterDocumentDirectory#"
// 	 FileUploadDirectory="#Variables.FileUploadDirectory#"
//	 RelativeTempFilesDirectory="#Variables.RelativeTempFilesDirectory#"
//	 DatabaseIPAddress="#variables.DatabaseIPAddress#"
//	 DatabaseUserId="#Variables.DatabaseUserId#"
//	 DatabasePassword="#Variables.DatabasePassword#"
//	 DSNPreFix="#Variables.DSNPreFix#"
//	 TempFilesDirectory="#Variables.TempFilesDirectory#"
//	 FaxAttachmentsDirectory="#Variables.FaxAttachmentsDirectory#"
//	 ConfigKey="#DefaultConfig.EncounterConfig_Id#"
//	 PrintPHAOnly="#URL.PrintPHAOnly#"
//	 GenerateAVS="#variables.GenerateAVS#"
//	 >
//	<cfset variables.EncounterConfig_Id = DefaultConfig.EncounterConfig_Id>
//</cfif>
//
//
    if ($DefaultConfig_num == 0) {
      //defaultheader.cfm
      $this->load->view('encounter/print/defaultheader');
      $EncounterConfig_Id = 0;
    } else {
      //customheader.cfm
      $data['ConfigKey'] = $DefaultConfig_row->EncounterConfig_Id;
      $data['PrintPatientOnly'] = $PrintPatientOnly;
      $this->load->view('encounter/print/customheader', $data);
      $EncounterConfig_Id = $DefaultConfig_row->EncounterConfig_Id;
    }



//
//
//<br>
//
    ?>
    <br/>
    <?php
//
//<cfquery datasource="#Variables.EMRDataSource#" name="GetAdmendmentID">
//Select Top 1
//       Amendment_ID
//  From EncounterHistory
// Where Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#URL.PrimaryKey#">
//</cfquery>
//
//


    $sql = "Select Top 1
      Amendment_ID
  From EncounterHistory
 Where Encounter_Id= $Encounter_Id ";
    $GetAdmendmentID = $this->ReportModel->data_db->query($sql);
    $GetAdmendmentID_row = $GetAdmendmentID->row();
//
//<cfif GetAdmendmentID.Amendment_ID NEQ "">
//	<cfquery datasource="#Variables.EMRDataSource#" name="GetOriginalEncounterDate">
//	Select Top 1
//	       Convert(Char,EncounterDate,101) AS TheDate
//	  From EncounterHistory
//	 Where Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#GetAdmendmentID.Amendment_ID#">
//	</cfquery>
//	<cfoutput>
//		<div align="center" style="font-size: 14px; color: Black; font-weight: bold; font-family: Times New Roman;">Amendment to Previous Encounter of #Trim(GetOriginalEncounterDate.TheDate)#</div>
//	</cfoutput>
//</cfif>
//

    if ($GetAdmendmentID_row->Amendment_ID != "") {
      $sql = "	Select Top 1
	       Convert(Char,EncounterDate,101) AS TheDate
          From EncounterHistory
         Where Encounter_Id= $Encounter_Id 	";
      $GetOriginalEncounterDate = $this->ReportModel->data_db->query($sql);
      $GetOriginalEncounterDate_row = $GetOriginalEncounterDate->row();
      ?>
      <div align="center" style="font-size: 14px; color: Black; font-weight: bold; font-family: 'Times New Roman';">Amendment to Previous Encounter of <?php echo $GetOriginalEncounterDate_row->TheDate ?></div>
      <?php
    }

//
//
//
//<cfset variables.HeaderNeeded = False>
//<cfset variables.OutputMasterKey = 0>
//<cfset variables.NeedTemplateHeader = True>
//
//
//<cfloop query="ModuleSettings">
//
//		<cfif ModuleSettings.HeaderMaster_Id EQ 1 OR ModuleSettings.FreeTextYN EQ 0 OR StructKeyExists(HeaderMasterStruct,ModuleSettings.HeaderMaster_Id)>
//			<cfmodule template="HeaderNeeded.cfm"
//			 EMRDataSource="#Variables.EMRDataSource#"
//			 HeaderKey="#ModuleSettings.Header_Id#"
//			 PatientKey="#Url.PatientKey#"
//			 HeaderMasterKey="#ModuleSettings.HeaderMaster_Id#"
//			 FreeTextKey="#ModuleSettings.FreeTextYN#"
//			 SOHeaders="#SOHeaders.RecordCount#">
//		</cfif>
//		<!--- Wes: added ModuleSettings.HeaderMaster_Id EQ 29 so I could load this component even when there is no medications on the chart note --->
//        <cfif (ModuleSettings.HeaderMaster_Id NEQ 1 And Variables.Records NEQ 0) OR (ModuleSettings.HeaderMaster_Id EQ 29)>
//        	 <cfmodule template="encountercomponents.cfm"
//			  EMRDataSource="#Variables.EMRDataSource#"
//			  DateKey="#Url.DateKey#"
//			  PatientKey="#Url.PatientKey#"
//			  PrimaryKey="#Url.PrimaryKey#"
//			  HeaderMasterKey="#ModuleSettings.HeaderMaster_Id#"
//			  FreeTextKey="#ModuleSettings.FreeTextYN#"
//			  HeaderKey="#ModuleSettings.Header_Id#"
//			  SOHeaders="#SOHeaders.RecordCount#"
//			 >
//			  <cfset variables.NeedTemplateHeader = True>
//			  <!--- Wes: added ModuleSettings.HeaderMaster_Id EQ 29 so I could load this component even when there is no medications on the chart note --->
//              <cfif (ModuleSettings.Component NEQ 0) And (Variables.ComponentKey NEQ 0 OR ModuleSettings.HeaderMaster_Id EQ 29)>
//				  <cfmodule template="#Trim(ModuleSettings.Component)#"
//				   EMRDataSource="#Variables.EMRDataSource#"
//				   PrimaryKey="#Url.PrimaryKey#"
//				   PatientKey="#Url.PatientKey#"
//				   ProviderKey="#Url.ProviderKey#"
//				   ImageDataSource="#Variables.ImageDataSource#"
//				   ComponentKey="#Variables.ComponentKey#"
//				   DatabaseIPAddress="#Variables.DatabaseIPAddress#"
//				   DatabaseUserId="#Variables.DatabaseUserId#"
//				   DatabasePassword="#Variables.DatabasePassword#"
//				   DSNPrefix="#Variables.DSNPreFix#"
//				   TempFilesDirectory="#Variables.TempFilesDirectory#"
//				   RelativeTempFilesDirectory="#Variables.RelativeTempFilesDirectory#"
//				   HeaderMasterKey="#ModuleSettings.HeaderMaster_Id#"
//				   FaxKey="#Url.FaxKey#"
//				   ProductionServer="#Variables.ProductionServer#"
//				   DateKey="#Url.DateKey#"
//				   DocumentDirectory="#Variables.TempEncounterDocumentDirectory#"
//				   HeaderKey="#ModuleSettings.Header_Id#"
//				   FreeTextKey="#ModuleSettings.FreeTextYN#"
//				   SOHeaders="#SOHeaders.RecordCount#"
//				   ConfigKey="#variables.ProvDefConfig#"
//				   UseDetailKeys="#variables.UseDetailKeys#"
//				   EncounterComponentPrimaryKey="#variables.EncounterComponentPrimaryKey#"
//				  >
//			  </cfif>
//			  <cfif Variables.EncounterComponentKey NEQ 0>
//				  <cfmodule template="comp_encountertext.cfm"
//				   EMRDataSource="#Variables.EMRDataSource#"
//				   ComponentKey="#Variables.ComponentKey#"
//				   EncounterComponentKey="#Variables.EncounterComponentKey#"
//				   HeaderKey="#ModuleSettings.Header_Id#"
//				   PatientKey="#Url.PatientKey#"
//				   HeaderMasterKey="#ModuleSettings.HeaderMaster_Id#"
//				   FreeTextKey="#ModuleSettings.FreeTextYN#"
//				   SOHeaders="#SOHeaders.RecordCount#"
//				  >
//			  </cfif>
//		</cfif>
//		<cfif StructKeyExists(TemplateStruct,ModuleSettings.HeaderMaster_Id)>
//			<cfmodule template="comp_templates.cfm"
//				EMRDataSource="#Variables.EMRDataSource#"
//				PrimaryKey="#Url.PrimaryKey#"
//				ProviderKey="#Url.ProviderKey#"
//				DSNPrefix="#Variables.DSNPreFix#"
//				HeaderMasterKey="#ModuleSettings.HeaderMaster_Id#"
//				OutPutMasterKey="#Variables.OutPutMasterKey#"
//				EncounterDescriptionKey="#Url.EncounterDescriptionKey#"
//				TemplateDataSource="#Trim(Variables.TemplateDataSource)#"
//				HeaderKey="#ModuleSettings.Header_Id#"
//				PatientKey="#Url.PatientKey#"
//				FreeTextKey="#ModuleSettings.FreeTextYN#"
//				SOHeaders="#SOHeaders.RecordCount#"
//				>
//		</cfif>
//</cfloop>
//
//
//
    if (!empty($dt->ChiefComplaint)) {
      $pass['dt_encounter'] = $dt;
      $pass['header_text'] = "Reason for Visit";
      $this->load->view("encounter/print/comp_yb_custom", $pass);
    }

    foreach ($ModuleSettings_result as $ModuleSetting_dt) {
      $data['ConfigKey'] = $EncounterConfig_Id;
      $data['ModuleSetting_dt'] = $ModuleSetting_dt;
      $data['HeaderKey'] = $ModuleSetting_dt->Header_Id;
      $data['PatientKey'] = $Patient_Id;
      $data['HeaderMasterKey'] = $ModuleSetting_dt->HeaderMaster_Id;
      $data['FreeTextKey'] = $ModuleSetting_dt->FreeTextYN;
      $data['SOHeaders'] = $so_headers_num;
      $data['data_db'] = $data_db;
      $data['PrimaryKey'] = $Encounter_Id;
      $data['ProviderKey'] = $Provider_Id;
      $data['OutPutMasterKey'] = $OutputMasterKey;
      $data['EncounterDescriptionKey'] = $EncounterDescription_Id;


      if ($ModuleSetting_dt->HeaderMaster_Id == 1 || $ModuleSetting_dt->FreeTextYN == 0 || in_array($ModuleSetting_dt->HeaderMaster_Id, $HeaderMasterStruct)) {
        if (!isset($summary_report)) {
          $this->load->view('encounter/print/headerneeded', $data);
        }
      }
      //        <cfif (ModuleSettings.HeaderMaster_Id NEQ 1 And Variables.Records NEQ 0) OR (ModuleSettings.HeaderMaster_Id EQ 29)>
      if (($ModuleSetting_dt->HeaderMaster_Id != 1) || ($ModuleSetting_dt->HeaderMaster_Id == 29)) {
        $keys = encountercomponents($data);
        $data['ComponentKey'] = $keys['ComponentKey'];
        $data['EncounterComponentKey'] = $keys['EncounterComponentKey'];
        $data['UseDetailKeys'] = $keys['UseDetailKeys'];

        if ($ModuleSetting_dt->Component && $data['ComponentKey'] != '0') {
          $com_print = str_replace('.cfm', '', $ModuleSetting_dt->Component);
          if (!isset($summary_report)) {
            $this->load->view("encounter/comp_print/$com_print", $data);
          }
        }

        if ($keys['EncounterComponentKey'] != 0) {
          if (!isset($summary_report)) {
            $this->load->view("encounter/comp_print/comp_encountertext", $data);
          }
        }
      }

      // 139 => Reason for Visit
      // pause it alot of looping
      if (in_array($ModuleSetting_dt->HeaderMaster_Id, $TemplateStruct) && $ModuleSetting_dt->HeaderMaster_Id != 139) {
        $this->load->view('encounter/print/comp_templates', $data);
      }
    }

//
//
//
//<cfif (URL.PrintPHAOnly NEQ 1) AND (Variables.GenerateAVS NEQ 1)>
//	<cfmodule template="signature.cfm"
//	 EMRDataSource="#Variables.EMRDataSource#"
//	 DeptKey="#Url.DeptKey#"
//	 ProviderKey="#Url.ProviderKey#"
//	 PrimaryKey="#Url.PrimaryKey#"
//	 PatientKey="#Url.PatientKey#"
//	 ImageDataSource="#Variables.ImageDataSource#"
//	 RelativeFileUploadDirectory="#Variables.RelativeFileUploadDirectory#"
//	 FaxKey="#Url.FaxKey#"
//	 ProductionServer="#Variables.ProductionServer#"
//	 DocumentDirectory="#Variables.TempEncounterDocumentDirectory#"
//	 FileUploadDirectory="#Variables.FileUploadDirectory#"
//	 RelativeTempFilesDirectory="#Variables.RelativeTempFilesDirectory#"
//	 DatabaseIPAddress="#variables.DatabaseIPAddress#"
//	 DatabaseUserId="#Variables.DatabaseUserId#"
//	 DatabasePassword="#Variables.DatabasePassword#"
//	 DSNPreFix="#Variables.DSNPreFix#"
//	 TempFilesDirectory="#Variables.TempFilesDirectory#"
//	 UTC_TimeOffset="#variables.sUTC_TimeOffset#"
//	 UTC_DST="#variables.sUTC_DST#"
//	 FaxAttachmentsDirectory="#Variables.FaxAttachmentsDirectory#"
//	 ConfigKey="#variables.EncounterConfig_Id#"
//	 >
//</cfif>
//
//


    $GenerateAVS = 0; //dummy

    if ($PrintPHAOnly != 1 && $GenerateAVS != 1) {
      //load signature.cfm
      $data['PatientKey'] = $Patient_Id;
      $data['PrimaryKey'] = $Encounter_Id;
      $data['ProviderKey'] = $Provider_Id;
      if (!isset($summary_report)) {
        $this->load->view("encounter/print/signature", $data);
      }
    }


//
//<!--- Do we need to include the Full Size attachments? --->
//<cfif (Variables.IncludeAttachments EQ 1) AND (URL.PrintPHAOnly NEQ 1)>
//
//	<cfinclude template="ChartNoteAttachmentPrinting1.cfm">
//
//</cfif>
//
    //dummi
    $IncludeAttachments = 1;
    if ($IncludeAttachments == 1 && $PrintPHAOnly == 1) {
      //load ChartNoteAttachmentPrinting1.cfm
    }

//
//</body>
//</html>
//
//
//
    ?>
      </div>
  </body>
</html>
<?php
//
//
//<!--- 02/13/09 JWY - Appears redundant to me, as the same call is made in EncDetailDB.cfm.
//						Contrbuting to issues outlined in Case 5354
//
//<cfthread action="run" name="saveChartNotesThread-#CreateUUID()#">
//<!--- SF case 4335 --->
//<cfinclude template="saveChartNotes.cfm"/>
//</cfthread>
//--->
?>
<?php

?>
