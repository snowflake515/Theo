<?php
$dt = $this->EncounterHistoryModel->get_by_id($id)->row();
$Encounter_Id = $id;
$ProviderKey = $dt->Provider_ID;
$PatientKey = $dt->Patient_ID;
$Dept_Id =  ($dt->Dept_ID) ? (int)$dt->Dept_ID : @(int)$current_user->Dept_Id;;

$DeptKey = $Dept_Id; //EMBED
$ConfigKey  = $ConfigKey; //EMBED
//<!--- Program Name: CustomHeader.cfm
//
//		Change Log
//		JWY 3/24/2008 - Fixed problem where Patient Middle Initial was showing in the header no Patient info was supposed to show.
//		JWY 9/17/2008 - (Case 4082) Corrected table structure so that Department Info and Logo's position correctly
//--->
//<cfparam name="Attributes.PrintPHAOnly" default="0">
//<cfparam name="Attributes.GenerateAVS" default="0">
//
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.sId=Session.Id>
//	<cfset Variables.sUserId=Session.User_Id>
//	<cfset Variables.sOrgId=Session.Org_Id>
//</cflock>
//
//<cfif isdefined('url.faxkey')>
//	<!--- OK to reference application variables here, as will never be here from ProcessOldEncountersPDF.cfm --->
//	<cfset Variables.DestinationDirectory = Attributes.FaxAttachmentsDirectory>
//	<cfset Variables.TempDir = '/faxattachments/'>
//<cfelse>
//	<cfset Variables.DestinationDirectory = Attributes.TempFilesDirectory>
//	<cfset Variables.TempDir = ReplaceNoCase(Attributes.RelativeTempFilesDirectory,"..","","ALL")>
//</cfif>
//
//<cfset SupStruct=StructNew()>
//<cfset RendStruct=StructNew()>
//
//<cfset RendStruct.FirstName="">
//<cfset RendStruct.MiddleName="">
//<cfset RendStruct.LastName="">
//<cfset RendStruct.Title="">
//<cfset RendStruct.Suffix="">
//<cfset RendStruct.Addr1="">
//<cfset RendStruct.Addr2="">
//<cfset RendStruct.City="">
//<cfset RendStruct.State="">
//<cfset RendStruct.Zip="">
//<cfset RendStruct.Phone="">
//<cfset RendStruct.Fax="">

$SupStruct = array();
$RendStruct = array(
    'FirstName' => "",
    'MiddleName' => "",
    'LastName' => "",
    'Title' => "",
    'Suffix' => "",
    'Addr1' => "",
    'Addr2' => "",
    'City' => "",
    'State' => "",
    'Zip' => "",
    'Phone' => "",
    'Fax' => "",
);

//<cfif Attributes.ProviderKey EQ 0 OR Attributes.ProviderKey EQ 1>
//	<cfquery datasource="#Attributes.EMRDataSource#" name="NoProvider">
//	Select TOP 1
//	       P.ProviderFirstName,
//           P.ProviderMiddleName,
//	       P.ProviderLastName,
//	       P.ProviderTitle,
//	       P.ProviderSuffix,
//	       P.ProviderAddress1,
//	       P.ProviderAddress2,
//	       P.ProviderCity,
//	       P.ProviderState,
//	       P.ProviderZip,
//	       P.ProviderPhone,
//	       P.ProviderFax,
//		   P.Provider_Id
//	  From ProviderProfile P,
//	       PatientProfile PP
//     Where PP.Patient_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PatientKey#">
//	   And P.Provider_Id=PP.Provider_Id
//	</cfquery>
//	<cfset RendStruct.FirstName=NoProvider.ProviderFirstName>
//	<cfset RendStruct.MiddleName=NoProvider.ProviderMiddleName>
//	<cfset RendStruct.LastName=NoProvider.ProviderLastName>
//	<cfset RendStruct.Title=NoProvider.ProviderTitle>
//	<cfset RendStruct.Suffix=NoProvider.ProviderSuffix>
//	<cfset RendStruct.Addr1=NoProvider.ProviderAddress1>
//	<cfset RendStruct.Addr2=NoProvider.ProviderAddress2>
//	<cfset RendStruct.City=NoProvider.ProviderCity>
//	<cfset RendStruct.State=NoProvider.ProviderState>
//	<cfset RendStruct.Zip=NoProvider.ProviderZip>
//	<cfset RendStruct.Phone=NoProvider.ProviderPhone>
//	<cfset RendStruct.Fax=NoProvider.ProviderFax>
//</cfif>


if ($ProviderKey == 0 || $ProviderKey == 1) {
  $sql = "	Select TOP 1
	       P.ProviderFirstName,
           P.ProviderMiddleName,
	       P.ProviderLastName,
	       P.ProviderTitle,
	       P.ProviderSuffix,
	       P.ProviderAddress1,
	       P.ProviderAddress2,
	       P.ProviderCity,
	       P.ProviderState,
	       P.ProviderZip,
	       P.ProviderPhone,
	       P.ProviderFax,
		   P.Provider_Id
	  From " . $data_db . ".dbo.ProviderProfile P,
	       " . $data_db . ".dbo.PatientProfile PP
     Where PP.Patient_Id=$PatientKey
	   And P.Provider_Id=PP.Provider_Id";

  $NoProvider = $this->ReportModel->data_db->query($sql);
  $NoProvider_num = $NoProvider->num_rows();
  $NoProvider_row = $NoProvider->row();

  $RendStruct = array(
      'FirstName' => $NoProvider_row->ProviderFirstName,
      'MiddleName' => $NoProvider_row->ProviderMiddleName,
      'LastName' => $NoProvider_row->ProviderLastName,
      'Title' => $NoProvider_row->ProviderTitle,
      'Suffix' => $NoProvider_row->ProviderSuffix,
      'Addr1' => $NoProvider_row->ProviderAddress1,
      'Addr2' => $NoProvider_row->ProviderAddress2,
      'City' => $NoProvider_row->ProviderCity,
      'State' => $NoProvider_row->ProviderState,
      'Zip' => $NoProvider_row->ProviderZip,
      'Phone' => $NoProvider_row->ProviderPhone,
      'Fax' => $NoProvider_row->ProviderFax,
  );
}

//<cfquery datasource="#Attributes.EMRDataSource#" name="CustomConfig">
//Select TOP 1
//       LogoPosition,
//       BlockInfoPosition,
//       PatientName,
//	   PatientAddress,
//	   PatientCity,
//	   PatientState,
//	   PatientZip,
//	   AccountNumber,
//	   MRN,
//	   DatePrinted,
//	   PrintedPatientName,
//	   PatientSSN,
//	   PatientDOB,
//	   PatientAge,
//	   Provider,
//	   isnull(SupervisingProvider, Provider) as SupervisingProvider,
//	   EncounterDate,
//	   Margin1,
//	   Margin2,
//	   Dictated,
//	   isnull(OptionAlignment, 0) as OptionAlignment
//  From EncounterConfig
// Where EncounterConfig_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.ConfigKey#">
//</cfquery>
$sql = "Select TOP 1
       LogoPosition,
       BlockInfoPosition,
       PatientName,
	   PatientAddress,
	   PatientCity,
	   PatientState,
	   PatientZip,
	   AccountNumber,
	   MRN,
	   DatePrinted,
	   PrintedPatientName,
	   PatientSSN,
	   PatientDOB,
	   PatientAge,
	   Provider,
	   isnull(SupervisingProvider, Provider) as SupervisingProvider,
	   EncounterDate,
	   Margin1,
	   Margin2,
	   Dictated,
	   isnull(OptionAlignment, 0) as OptionAlignment
  From " . $data_db . ".dbo.EncounterConfig
 Where EncounterConfig_Id=$ConfigKey";

$CustomConfig = $this->ReportModel->data_db->query($sql);
$CustomConfig_num = $CustomConfig->num_rows();
$CustomConfig_row = $CustomConfig->row();


//<!---
//The CFC call below returns the Font and Color information for the display of Chart Note body items.
//Six items are returned:
//
//Variables.BodyFontInfo.FontColor = Body Font Color  (ex. 000000 for black)
//Variables.BodyFontInfo.FontSize = Body Font Size (ex. 12)
//Variables.BodyFontInfo.FontFace = Body Font Family (ex. Times New Roman)
//Variables.BodyFontInfo.FontWeight = Bold or Normal
//Variables.BodyFontInfo.FontStyle = Italics or Normal
//Variables.BodyFontInfo.FontDecoration = Underline or None
//--->
//<cfset variables.BodyFontInfo = CreateObject("component","cfc.chartnote.chartnote").getChartHeaderFontInfo(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Attributes.ConfigKey)>
//<cfset variables.DefaultStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: " & variables.BodyFontInfo.FontSize & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//<cfset variables.LargerStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: " & variables.BodyFontInfo.FontSize + 4 & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">

$data['data_db'] = $data_db;
$BodyFontInfo = getChartHeaderFontInfo($data, $ConfigKey);
$DefaultStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
$LargerStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . ($BodyFontInfo['FontSize'] + 4) . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";

//<cfquery datasource="#Attributes.ImageDataSource#" name="GetDeptType">
//Select TOP 1
//       ImageType
//  From AdminImages
// Where Dept_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.DeptKey#">
//</cfquery>

$sql = "Select TOP 1
       ImageType
  From " . $image_db . ".dbo.AdminImages
 Where Dept_Id=$DeptKey";

$GetDeptType = $this->ReportModel->data_db->query($sql);
$GetDeptType_num = $GetDeptType->num_rows();
$GetDeptType_row = $GetDeptType->row();

//<cfset Variables.DeptFile="">
//<cfif GetDeptType.RecordCount NEQ 0>
//	<cfset Variables.DeptFile=Trim(Variables.sUserId)&Variables.sOrgId&Variables.sId&Month(Now())&Day(Now())&Year(Now())&TimeFormat(Now(),"hh")&TimeFormat(Now(),"mm")&TimeFormat(Now(),"ss")&RandRange(1,1000)&"."&GetDeptType.ImageType>
//	<CF_BLOBSELECT sqlServerName="#Attributes.DatabaseIPAddress#"
//		dsn="#Attributes.ImageDataSource#"
//		PortNumber="1433"
// 		LOGIN="#Attributes.DatabaseUserId#"
// 		PASSWORD="#Attributes.DatabasePassword#"
//  		QUERYSTRING="Select ImageFile From #Attributes.DSNPreFix#eCastEMR_Images.dbo.AdminImages Where Dept_Id=#Attributes.DeptKey#"
//  		FILENAME="#Variables.DestinationDirectory##Variables.DeptFile#">
//	<cfoutput>
//	<cfif (Len(Variables.DeptFile)-4) GT 0>
//		<cf_imagecr3 load="#Variables.DestinationDirectory##Variables.DeptFile#" save="#Variables.DestinationDirectory#n#Left(Variables.DeptFile,Len(Variables.DeptFile)-4)#.jpg" resize=">x75">
//		<cfset Variables.DeptFile='n'&Left(Variables.DeptFile,Len(Variables.DeptFile)-4)&'.jpg'>
//	</cfif>
//	</cfoutput>
//</cfif>
//
$sql= "Select * From $image_db.dbo.AdminImages Where Dept_Id=$DeptKey";
$logo = $this->ReportModel->data_db->query($sql);
$logo_num = $logo->num_rows();
$logo_row = $logo->row();
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="HeaderData">
//Select TOP 1
//       E.EncounterNotes,
//	   E.EncounterDate,
//	   E.Dept_Id,
//	   E.Provider_Id,
//	   E.SupProvider_Id,
//	   E.EncounterSignedOff,
//	   E.ChiefComplaint,
//	   D.EncounterDescription,
//	   D.EncounterDescription_Id,
//	   F.FacilityName,
//	   PP.FirstName,
//	   PP.MiddleName,
//	   PP.LastName,
//	   PP.SSN,
//   	   PP.Sex,
//	   PP.DOB,
//	   PP.MedicalRecordNumber,
//	   PP.AccountNumber,
//	   PP.Addr1,
//	   PP.Addr2,
//	   PP.City,
//	   PP.State,
//	   PP.Zip,
//	   PL.ProblemDescription
//  From EncounterHistory E
//  Left Outer Join EncounterDescriptionList D
//    On E.EncounterDescription_Id=D.EncounterDescription_Id
//  Join PatientProfile PP
//    On E.Patient_Id=PP.Patient_Id
//  Left Outer Join FacilityList F
//    On E.Facility_Id=F.Facility_Id
//  Left Outer Join ProblemList PL
//    On E.Problem_Id=PL.Problem_Id
// Where E.Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PrimaryKey#">
//</cfquery>


$sql = "Select TOP 1
       E.EncounterNotes,
	   E.EncounterDate,
	   E.Dept_Id,
	   E.Provider_Id,
	   E.SupProvider_Id,
	   E.EncounterSignedOff,
	   E.ChiefComplaint,
	   D.EncounterDescription,
	   D.EncounterDescription_Id,
	   F.FacilityName,
	   PP.FirstName,
	   PP.MiddleName,
	   PP.LastName,
	   PP.SSN,
   	   PP.Sex,
	   PP.DOB,
	   PP.MedicalRecordNumber,
	   PP.AccountNumber,
	   PP.Addr1,
	   PP.Addr2,
	   PP.City,
	   PP.State,
	   PP.Zip,
	   PL.ProblemDescription
  From " . $data_db . ".dbo.EncounterHistory E
  Left Outer Join " . $data_db . ".dbo.EncounterDescriptionList D
    On E.EncounterDescription_Id=D.EncounterDescription_Id
  Join " . $data_db . ".dbo.PatientProfile PP
    On E.Patient_Id=PP.Patient_Id
  Left Outer Join " . $data_db . ".dbo.FacilityList F
    On E.Facility_Id=F.Facility_Id
  Left Outer Join " . $data_db . ".dbo.ProblemList PL
    On E.Problem_Id=PL.Problem_Id
 Where E.Encounter_Id=$Encounter_Id";

$HeaderData = $this->ReportModel->data_db->query($sql);
$HeaderData_num = $HeaderData->num_rows();
$HeaderData_row = $HeaderData->row();

//<cfquery datasource="#Attributes.EMRDataSource#" name="SupProvider">
//Select TOP 2
//       ProviderFirstName,
//       ProviderMiddleName,
//       ProviderLastName,
//       ProviderTitle,
//       ProviderSuffix,
//       ProviderAddress1,
//       ProviderAddress2,
//       ProviderCity,
//       ProviderState,
//       ProviderZip,
//       ProviderPhone,
//       ProviderFax,
//       Provider_Id
//  From ProviderProfile
// Where (Provider_Id=<cfif HeaderData.RecordCount NEQ 0><cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#HeaderData.Provider_Id#"><cfelse>0</cfif> OR Provider_Id=<cfif HeaderData.SupProvider_Id GTE 1><cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#HeaderData.SupProvider_Id#"><cfelse><cfqueryparam cfsqltype="CF_SQL_INTEGER" value="0"></cfif>)
//</cfquery>


if ($HeaderData_num != 0) {
  $Provider_Id = $HeaderData_row->Provider_Id;
} else {
  $Provider_Id = 0;
}

if ($HeaderData_row->SupProvider_Id >= 1) {
  $SupProvider_Id = $HeaderData_row->SupProvider_Id;
} else {
  $SupProvider_Id = 0;
}

$sql = "Select TOP 2
       ProviderFirstName,
       ProviderMiddleName,
       ProviderLastName,
       ProviderTitle,
       ProviderSuffix,
       ProviderAddress1,
       ProviderAddress2,
       ProviderCity,
       ProviderState,
       ProviderZip,
       ProviderPhone,
       ProviderFax,
       Provider_Id
  From " . $data_db . ".dbo.ProviderProfile
 Where (Provider_Id=$Provider_Id OR Provider_Id=$SupProvider_Id)";

$SupProvider = $this->ReportModel->data_db->query($sql);
$SupProvider_num = $SupProvider->num_rows();
$SupProvider_result = $SupProvider->result();

//<cfloop query="SupProvider">
//	<cfif HeaderData.SupProvider_Id EQ SupProvider.Provider_Id>
//		<cfset SupStruct.FirstName=SupProvider.ProviderFirstName>
//		<cfset SupStruct.MiddleName=SupProvider.ProviderMiddleName>
//		<cfset SupStruct.LastName=SupProvider.ProviderLastName>
//		<cfset SupStruct.Title=SupProvider.ProviderTitle>
//		<cfset SupStruct.Suffix=SupProvider.ProviderSuffix>
//		<cfset SupStruct.Addr1=SupProvider.ProviderAddress1>
//		<cfset SupStruct.Addr2=SupProvider.ProviderAddress2>
//		<cfset SupStruct.City=SupProvider.ProviderCity>
//		<cfset SupStruct.State=SupProvider.ProviderState>
//		<cfset SupStruct.Zip=SupProvider.ProviderZip>
//		<cfset SupStruct.Phone=SupProvider.ProviderPhone>
//		<cfset SupStruct.Fax=SupProvider.ProviderFax>
//	</cfif>
//	<cfif HeaderData.Provider_Id EQ SupProvider.Provider_Id>
//		<cfif Attributes.ProviderKey NEQ 0 AND Attributes.ProviderKey NEQ 1>
//			<cfset RendStruct.FirstName=SupProvider.ProviderFirstName>
//			<cfset RendStruct.MiddleName=SupProvider.ProviderMiddleName>
//			<cfset RendStruct.LastName=SupProvider.ProviderLastName>
//			<cfset RendStruct.Title=SupProvider.ProviderTitle>
//			<cfset RendStruct.Suffix=SupProvider.ProviderSuffix>
//			<cfset RendStruct.Addr1=SupProvider.ProviderAddress1>
//			<cfset RendStruct.Addr2=SupProvider.ProviderAddress2>
//			<cfset RendStruct.City=SupProvider.ProviderCity>
//			<cfset RendStruct.State=SupProvider.ProviderState>
//			<cfset RendStruct.Zip=SupProvider.ProviderZip>
//			<cfset RendStruct.Phone=SupProvider.ProviderPhone>
//			<cfset RendStruct.Fax=SupProvider.ProviderFax>
//		</cfif>
//	</cfif>
//</cfloop>


foreach ($SupProvider_result as $spr) {
  if ($HeaderData_row->SupProvider_Id == $spr->Provider_Id) {
    $SupStruct['FirstName'] = $spr->ProviderFirstName;
    $SupStruct['MiddleName'] = $spr->ProviderMiddleName;
    $SupStruct['LastName'] = $spr->ProviderLastName;
    $SupStruct['Title'] = $spr->ProviderTitle;
    $SupStruct['Suffix'] = $spr->ProviderSuffix;
    $SupStruct['Addr1'] = $spr->ProviderAddress1;
    $SupStruct['Addr2'] = $spr->ProviderAddress2;
    $SupStruct['City'] = $spr->ProviderCity;
    $SupStruct['State'] = $spr->ProviderState;
    $SupStruct['Zip'] = $spr->ProviderZip;
    $SupStruct['Phone'] = $spr->ProviderPhone;
    $SupStruct['Fax'] = $spr->ProviderFax;
  }

  if ($HeaderData_row->Provider_Id == $spr->Provider_Id) {
    if ($ProviderKey != 0 && $ProviderKey != 1) {
      $RendStruct['FirstName'] = $spr->ProviderFirstName;
      $RendStruct['MiddleName'] = $spr->ProviderMiddleName;
      $RendStruct['LastName'] = $spr->ProviderLastName;
      $RendStruct['Title'] = $spr->ProviderTitle;
      $RendStruct['Suffix'] = $spr->ProviderSuffix;
      $RendStruct['Addr1'] = $spr->ProviderAddress1;
      $RendStruct['Addr2'] = $spr->ProviderAddress2;
      $RendStruct['City'] = $spr->ProviderCity;
      $RendStruct['State'] = $spr->ProviderState;
      $RendStruct['Zip'] = $spr->ProviderZip;
      $RendStruct['Phone'] = $spr->ProviderPhone;
      $RendStruct['Fax'] = $spr->ProviderFax;
    }
  }
}

//<cfif CustomConfig.LogoPosition EQ 0>
//	<cfset CustomConfig.LogoPosition = 4>
//</cfif>
//<cfif CustomConfig.BlockInfoPosition EQ 0>
//	<cfset CustomConfig.BlockInfoPosition = 4>
//</cfif>

if ($CustomConfig_row->LogoPosition == 0) {
  $CustomConfig_LogoPosition = 4;
}
if ($CustomConfig_row->BlockInfoPosition == 0) {
  $CustomConfig_BlockInfoPosition = 4;
}


?>
<span style="<?php echo $DefaultStyle; ?>">
<table border="0" cellpadding="3" cellspacing="0" style="width: 7.0in;">
	<tr>
    <?php
//    <cfif (CustomConfig.LogoPosition NEQ 1) AND (CustomConfig.BlockInfoPosition NEQ 1)>
//			<cfset variables.colwidth = "33%">
//		<cfelse>
//			<cfset variables.colwidth = "1%">
//		</cfif>
    if($CustomConfig_row->LogoPosition != 1 && $CustomConfig_row->BlockInfoPosition != 1){
      $colwidth = "33%";
    }else{
      $colwidth = "1%";
    }
    ?>

		<td width="<?php echo $colwidth; ?>" nowrap align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
       <?php
       //we set it manual;
       $DocumentDirectory = "";
       $DeptFile = ($logo_row && $logo_row->ImageType)?'<img src="data:image/'.$logo_row->ImageType.';base64,'.  base64_encode($logo_row->ImageFile).'"  border="0" width="100"/>' : "";
          if($CustomConfig_row->LogoPosition == 1){
            if( $DocumentDirectory == ""){
              if( $DeptFile != ""){
//                <img src="#Attributes.ProductionServer#/emr#Variables.TempDir##Variables.DeptFile#" border="0">
                echo $DeptFile;
              }else{
                  //echo '<img src="data:image/'.$logo_row->ImageType.';base64,'.base64_encode( $logo_row->ImageFile ).'"  border="0" width="100"/>';
                  //
  //          <cftry>
  //						<cfset Variables.TempLogoImageFilesName="#CreateUUID()##RandRange(1,100000)#.jpg">
  //						<cffile action = "copy"
  //								source = "#Variables.DestinationDirectory##variables.DeptFile#"
  //								destination = "#Attributes.DocumentDirectory#\">
  //						<cffile action="rename"
  //								source="#Attributes.DocumentDirectory#\#variables.DeptFile#"
  //								destination="#Attributes.DocumentDirectory#\#Variables.TempLogoImageFilesName#">
  //						<img src="$$SOMEECASTABSOLUTEPATH$$#Variables.TempLogoImageFilesName#" border="0">
  //
  //						<cfcatch type="Any">
  //
  //						</cfcatch>
  //					</cftry>
              }
            }
          }else if($CustomConfig_row->BlockInfoPosition == 1){
      ?>
				<table border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td nowrap align="left" valign="top" style="<?php echo $DefaultStyle; ?>">
							<?php
              echo $HeaderData_row->FacilityName."<br>";
              if($RendStruct['Addr1'] != ""){
                echo $RendStruct['Addr1']."<br>";
              }
              if($RendStruct['Addr2'] != ""){
                echo $RendStruct['Addr2']."<br>";
              }
              echo $RendStruct['City'];
              if($RendStruct['City'] != ""){
                echo ",";
              }
              echo $RendStruct['State']." ".$RendStruct['Zip']."<br>";
              if($RendStruct['Phone'] != ""){
                echo "P:".$RendStruct['Phone']."<br>";
              }
              if($RendStruct['Fax'] != ""){
                echo "F:".$RendStruct['Fax'];
              }
              ?>
						</td>
					</tr>
				</table>
      <?php
          }else{
            echo "&nbsp;";
          }
      ?>
		</td>

		<td nowrap align="left" style="<?php echo $DefaultStyle; ?>" valign="top">

      <?php
      if($CustomConfig_row->LogoPosition == 2){
        if(trim($DocumentDirectory) == ""){
            if($DeptFile != ""){

  //            	<img src="#Attributes.ProductionServer#/emr#Variables.TempDir##Variables.DeptFile#" border="0">
              //echo '<img src="data:image/'.$logo_row->ImageType.';base64,'.base64_encode( $logo_row->ImageFile ).'"  border="0" width="100"/>';
               echo $DeptFile;
            }else{
              //echo '<img src="data:image/'.$logo_row->ImageType.';base64,'.base64_encode( $logo_row->TnImage ).'"  border="0" width="100"/>';
  //          <cftry>
  //						<cfset Variables.TempLogoImageFilesName="#CreateUUID()##RandRange(1,100000)#.jpg">
  //						<cffile action = "copy"
  //								source = "#Variables.DestinationDirectory##variables.DeptFile#"
  //								destination = "#Attributes.DocumentDirectory#\">
  //						<cffile action="rename"
  //								source="#Attributes.DocumentDirectory#\#variables.DeptFile#"
  //								destination="#Attributes.DocumentDirectory#\#Variables.TempLogoImageFilesName#">
  //						<img src="$$SOMEECASTABSOLUTEPATH$$#Variables.TempLogoImageFilesName#" border="0">
  //
  //						<cfcatch type="Any">
  //
  //						</cfcatch>
  //					</cftry>


          }
        }
      }else if($CustomConfig_row->BlockInfoPosition == 2){
      ?>
				<table border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td nowrap align="left" valign="top" style="<?php echo $DefaultStyle; ?>">
              <?php
              echo $HeaderData_row->FacilityName."<br>";
              if($RendStruct['Addr1'] != ""){
                echo $RendStruct['Addr1']."<br>";
              }
              if($RendStruct['Addr2'] != ""){
                echo $RendStruct['Addr2']."<br>";
              }
              echo $RendStruct['City'];
              if($RendStruct['City'] != ""){
                echo ",";
              }
              echo $RendStruct['State']." ".$RendStruct['State']." ".$RendStruct['Zip'];
              if($RendStruct['Phone'] != ""){
                echo "P: ".$RendStruct['Phone']."<br>";
              }
              if($RendStruct['Fax'] != ""){
                echo "F: ".$RendStruct['Fax'];
              }
              ?>
						</td>
					</tr>
				</table>
      <?php
      }else{
        echo "&nbsp;";
      }
      ?>
		</td>
		<td width="<?php echo $colwidth;  ?>" nowrap align="right" style="<?php echo $DefaultStyle; ?>" valign="top">
      <?php
      if($CustomConfig_row->LogoPosition == 3){
        if($DocumentDirectory == ""){
          if($DeptFile != ""){
             echo $DeptFile;
//            <img src="#Attributes.ProductionServer#/emr#Variables.TempDir##Variables.DeptFile#" border="0">
          }else{
//            <cftry>
//						<cfset Variables.TempLogoImageFilesName="#CreateUUID()##RandRange(1,100000)#.jpg">
//						<cffile action = "copy"
//								source = "#Variables.DestinationDirectory##variables.DeptFile#"
//								destination = "#Attributes.DocumentDirectory#\">
//						<cffile action="rename"
//								source="#Attributes.DocumentDirectory#\#variables.DeptFile#"
//								destination="#Attributes.DocumentDirectory#\#Variables.TempLogoImageFilesName#">
//						<img src="$$SOMEECASTABSOLUTEPATH$$#Variables.TempLogoImageFilesName#" border="0">
//
//						<cfcatch type="Any">
//
//						</cfcatch>
//					</cftry>
          }
        }
      }else if($CustomConfig_row->BlockInfoPosition == 3){
      ?>
				<table border="0" cellpadding="3" cellspacing="0" style="<?php echo $DefaultStyle; ?>">
					<tr>
						<td nowrap align="left" valign="top" style="<?php echo $DefaultStyle; ?>">
              <?php
              echo $HeaderData_row->FacilityName."<br>";
              if(!empty($RendStruct['Addr1'])){
                echo $RendStruct['Addr1']."<br>";
              }
              if(!empty($RendStruct['Addr2'])){
                echo $RendStruct['Addr2']."<br>";
              }

              if(!empty($RendStruct['City'])){
                echo $RendStruct['City'];
                echo ",";
              }
              echo $RendStruct['State']." ".$RendStruct['Zip']."<br>";
              if(!empty($RendStruct['Phone'])){
                echo "P: ".$RendStruct['Phone']."<br>";
              }
              if(!empty($RendStruct['Fax']) ){
                echo "F: ".$RendStruct['Fax'];
              }
              ?>
						</td>
					</tr>
				</table>
       <?php
      }else{
        echo "&nbsp;";
      }
       ?>
		</td>
	</tr>
</table>
</span>



<table border="0" cellpadding="3" cellspacing="0" style="width: 7.0in;">
	<tr>
		<td align="center" colspan="2">
			<hr noshade size="1" color="Black">
		</td>
	</tr>

	<tr>
    <?php
    if($CustomConfig_row->OptionAlignment == 0){
      echo '<td nowrap align="left" valign="top">';
    ?>

  <table cellpadding="1" cellspacing="0">
      <?php
      $NeedLineBreaks = 0;
      $DisplayInfo = "";
      if($HeaderData_row->FirstName != "" && $CustomConfig_row->PatientName != 0){
        $DisplayInfo = $DisplayInfo."".$HeaderData_row->FirstName;
        $NeedLineBreaks = 1;
      }

      if($HeaderData_row->MiddleName != "" && $CustomConfig_row->PatientName != 0){
        $DisplayInfo = $DisplayInfo."&nbsp;".$HeaderData_row->MiddleName;
        $NeedLineBreaks = 1;
      }

      if($CustomConfig_row->PatientName != 0){
        $DisplayInfo = $DisplayInfo."&nbsp;".$HeaderData_row->LastName."<br/>";
        $NeedLineBreaks = 1;
      }
      if($HeaderData_row->Addr1 != "" && $CustomConfig_row->PatientAddress != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->Addr1."<br/>";
        $NeedLineBreaks = 1;
      }
       if($HeaderData_row->Addr2 != "" && $CustomConfig_row->PatientAddress != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->Addr2."<br/>";
        $NeedLineBreaks = 1;
      }
       if($HeaderData_row->City != "" && $CustomConfig_row->PatientCity != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->City;
        $NeedLineBreaks = 1;
      }
      if($HeaderData_row->State != "" && $CustomConfig_row->PatientState != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->State;
        $NeedLineBreaks = 1;
      }
      if($HeaderData_row->Zip != "" && $CustomConfig_row->PatientZip != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->Zip;
        $NeedLineBreaks = 1;
      }

      if(trim($DisplayInfo) != ""){
      ?>
        <tr>
          <td nowrap align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
            <?php echo $DisplayInfo; ?>
          </td>
        </tr>
      <?php
      }
      if($CustomConfig_row->AccountNumber != 0){
      ?>
        <tr>
          <td nowrap align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
            Account Number: <b><?php echo sprintf('%s', $HeaderData_row->AccountNumber); ?></b>
          </td>
        </tr>
      <?php
      }
      if($CustomConfig_row->MRN != 0){
      ?>
        <tr>
          <td nowrap align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
            Policy Number: <b><?php echo $HeaderData_row->MedicalRecordNumber; ?></b>
          </td>
        </tr>
      <?php
      }
      ?>
    </table>

    <?php
      echo '</td>';
      echo '<td nowrap align="right" valign="top">';
    ?>
      <table border="0" cellpadding="1" cellspacing="0">
        <?php
        if($CustomConfig_row->DatePrinted != 0){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              Printed:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <strong><?php echo date("m/d/Y"); ?></strong>
            </td>
          </tr>
        <?php
        }
        if($CustomConfig_row->PrintedPatientName != 0){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              Patient Name:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <strong><?php echo $HeaderData_row->FirstName." ".$HeaderData_row->MiddleName." ".$HeaderData_row->LastName; ?></strong>
            </td>
          </tr>
        <?php
        }
        if($CustomConfig_row->PatientSSN != 0){
        ?>
          <tr  style="display: none">
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              SSN:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <strong><?php echo $HeaderData_row->SSN; ?></strong>
            </td>
          </tr>
        <?php
        }
        if($CustomConfig_row->PatientDOB != 0){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              DOB:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <strong><?php echo date('m/d/Y', strtotime($HeaderData_row->DOB)); ?></strong>
            </td>
          </tr>
        <?php
        }
        if($CustomConfig_row->PatientAge != 0){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              Age:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <?php
              if($HeaderData_row->DOB != ""){
                $Age = ($HeaderData_row->DOB) ? dob_to_age($HeaderData_row->DOB) : "";
                echo "<strong>$Age</strong>";

              }
              ?>
            </td>
          </tr>
        <?php
        }
        if($CustomConfig_row->Provider != 0){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              Provider:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <strong>
                <?php
                echo trim($RendStruct['Title'])." ".$RendStruct['FirstName']." ".$RendStruct['MiddleName']." ".$RendStruct['LastName'];
                if($RendStruct['Suffix'] != ""){
                  echo ",";
                  echo trim($RendStruct['Suffix']);
                }
              ?>
              </strong>
            </td>
          </tr>
        <?php
        }
        if($CustomConfig_row->SupervisingProvider != "" && $HeaderData_row->SupProvider_Id > 0 && $HeaderData_row->Provider_Id > 1 && $HeaderData_row->SupProvider_Id != $HeaderData_row->Provider_Id){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              Supervising Provider:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <strong>
                <?php
                echo trim($SupStruct['Title'])." ".trim($SupStruct['FirstName'])." ".trim($SupStruct['MiddleName'])." ".trim($SupStruct['LastName']);
                if($SupStruct['Suffix'] != ""){
                  echo ",";
                  echo trim($SupStruct['Suffix']);
                }
                ?>
              </strong>
            </td>
          </tr>
       <?php
        }
       ?>

      </table>
    <?php
      echo '</td>';
    }else{
      echo '<td nowrap align="left" valign="top">';
    ?>
    <table border="0" cellpadding="1" cellspacing="0">
        <?php
        if($CustomConfig_row->DatePrinted != 0){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              Printed:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <strong><?php echo date("m/d/Y"); ?></strong>
            </td>
          </tr>
        <?php
        }
        if($CustomConfig_row->PrintedPatientName != 0){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              Patient Name:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <strong><?php echo $HeaderData_row->FirstName." ".$HeaderData_row->MiddleName." ".$HeaderData_row->LastName ?></strong>
            </td>
          </tr>
        <?php
        }
        if($CustomConfig_row->PatientSSN != 0){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              SSN:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <strong><?php echo $HeaderData_row->SSN; ?></strong>
            </td>
          </tr>
        <?php
        }
        if($CustomConfig_row->PatientDOB != 0){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              DOB:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <strong><?php echo date('m/d/Y', strtotime($HeaderData_row->DOB)); ?></strong>
            </td>
          </tr>
        <?php
        }
        if($CustomConfig_row->PatientAge != 0){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              Age:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <?php
              if($HeaderData_row->DOB != ""){
                $Age = (int) date('Y', strtotime($HeaderData_row->DOB)) - date("Y");
                echo "<strong>$Age</strong>";
              }
              ?>
            </td>
          </tr>
        <?php
        }
        if($CustomConfig_row->Provider != 0){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              Rendering Provider:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <strong>
                <?php
                echo trim($RendStruct['Title'])." ".$RendStruct['FirstName']." ".$RendStruct['MiddleName']." ".$RendStruct['LastName'];
                if($RendStruct['Suffix'] != ""){
                  echo ",";
                  echo trim($RendStruct['Suffix']);
                }
              ?>
              </strong>
            </td>
          </tr>
        <?php
        }
        if($CustomConfig_row->SupervisingProvider != "" && $HeaderData_row->SupProvider_Id > 0 && $HeaderData_row->Provider_Id > 1 && $HeaderData_row->SupProvider_Id != $HeaderData_row->Provider_Id){
        ?>
          <tr>
            <td nowrap align="right" style="<?php echo $DefaultStyle ?>" valign="top">
              Supervising Provider:
            </td>
            <td nowrap align="left" style="<?php echo $DefaultStyle ?>" valign="top">
              <strong>
                <?php
                echo trim($SupStruct['Title'])." ".trim($SupStruct['FirstName'])." ".trim($SupStruct['MiddleName'])." ".trim($SupStruct['LastName']);
                if($SupStruct['Suffix'] != ""){
                  echo ",";
                  echo trim($SupStruct['Suffix']);
                }
                ?>
              </strong>
            </td>
          </tr>
       <?php
        }
       ?>

      </table>
    <?php
      echo '</td>';
      echo '<td nowrap align="right" valign="top">';
    ?>
    <table cellpadding="1" cellspacing="0">
      <?php
      $NeedLineBreaks = 0;
      $DisplayInfo = "";
      if($HeaderData_row->FirstName != "" && $CustomConfig_row->PatientName != 0){
        $DisplayInfo = $DisplayInfo."".$HeaderData_row->FirstName;
        $NeedLineBreaks = 1;
      }

      if($HeaderData_row->MiddleName != "" && $CustomConfig_row->PatientName != 0){
        $DisplayInfo = $DisplayInfo."&nbsp;".$HeaderData_row->MiddleName;
        $NeedLineBreaks = 1;
      }

      if($CustomConfig_row->PatientName != 0){
        $DisplayInfo = $DisplayInfo."&nbsp;".$HeaderData_row->LastName."<br>";
        $NeedLineBreaks = 1;
      }
      if($HeaderData_row->Addr1 != "" && $CustomConfig_row->PatientAddress != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->Addr1."<br/>";
        $NeedLineBreaks = 1;
      }
       if($HeaderData_row->Addr2 != "" && $CustomConfig_row->PatientAddress != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->Addr2."<br/>";
        $NeedLineBreaks = 1;
      }
       if($HeaderData_row->City != "" && $CustomConfig_row->PatientCity != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->City;
        $NeedLineBreaks = 1;
      }
      if($HeaderData_row->State != "" && $CustomConfig_row->PatientState != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->State;
        $NeedLineBreaks = 1;
      }
      if($HeaderData_row->Zip != "" && $CustomConfig_row->PatientZip != 0){
        $DisplayInfo = $DisplayInfo." ".$HeaderData_row->Zip;
        $NeedLineBreaks = 1;
      }

      if(trim($DisplayInfo) != ""){
      ?>
        <tr>
          <td nowrap align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
            <?php echo $DisplayInfo; ?>
          </td>
        </tr>
      <?php
      }
      if($CustomConfig_row->AccountNumber != 0){
      ?>
        <tr>
          <td nowrap align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
            Account Number: <b><?php echo sprintf('%s', $HeaderData_row->AccountNumber); ?></b>
          </td>
        </tr>
      <?php
      }
      if($CustomConfig_row->MRN != 0){
      ?>
        <tr>
          <td nowrap align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
            MRN: <b><?php echo $HeaderData_row->MedicalRecordNumber; ?></b>
          </td>
        </tr>
      <?php
      }
      ?>
    </table>
    <?php
      echo '</td>';
    }
    ?>
	</tr>

	<tr>
		<td colspan="2" nowrap align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
			<br>
			DOS:
      <strong>
        <?php echo date('F d, Y', strtotime($HeaderData_row->EncounterDate)); ?>
      </strong>
		</td>
	</tr>

  <?php
//  if($HeaderData_row->EncounterSignedOff != 1 && $PrintPHAOnly != 1 && $GenerateAVS != 0){
  if($HeaderData_row->EncounterSignedOff != 1 && $PrintPatientOnly != 1){
    if (!isset($summary_report)) {
  ?>
		<tr>
			<td align="center" colspan="2" style="<?php echo $LargerStyle; ?>" valign="top">
				<strong>Preliminary Note - has not been signed off by provider</strong>
			</td>
		</tr>
  <?php
    }
  }
  ?>
</table>
