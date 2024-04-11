<?php
$dt = $this->EncounterHistoryModel->get_by_id($id)->row();

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
//<cfset SupStruct.FirstName="">
//<cfset SupStruct.MiddleName="">
//<cfset SupStruct.LastName="">
//<cfset SupStruct.Title="">
//<cfset SupStruct.Suffix="">
//<cfset SupStruct.Addr1="">
//<cfset SupStruct.Addr2="">
//<cfset SupStruct.City="">
//<cfset SupStruct.State="">
//<cfset SupStruct.Zip="">
//<cfset SupStruct.Phone="">
//<cfset SupStruct.Fax="">
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
//
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
//
//

$Encounter_Id = $id;
$ProviderKey = $dt->Provider_ID;
$Patient_Id = $dt->Patient_ID;


if ($ProviderKey == 0 || $ProviderKey == 1) {

  $sql = "Select TOP 1
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
     Where PP.Patient_Id=" . $Patient_Id . "
	   And P.Provider_Id=PP.Provider_Id";

  $NoProvider = $this->ReportModel->data_db->query($sql);
  $NoProvider_row = $NoProvider->row();
}

//
//<cfquery datasource="#Attributes.ImageDataSource#" name="GetDeptType">
//Select TOP 1
//       ImageType
//  From AdminImages
// Where Dept_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.DeptKey#">
//</cfquery>
//

$Dept_Id =  ($dt->Dept_ID) ? (int)$dt->Dept_ID : (int)$current_user->Dept_Id;;

$sql = "Select TOP 1
     ImageType
     From " . $image_db . ".dbo.AdminImages
     Where Dept_Id=$Dept_Id";

$GetDeptType = $this->ReportModel->data_db->query($sql);
$GetDeptType_row = $GetDeptType->row();


//
//
//
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
//
$DeptKey = $dt->Dept_ID;
$sql= "Select * From $image_db.dbo.AdminImages Where Dept_Id=$DeptKey";
$logo = $this->ReportModel->data_db->query($sql);
$logo_num = $logo->num_rows();
$logo_row = $logo->row();
//
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="HeaderData">
//Select
//       E.SupProvider_Id,
//	   E.EncounterNotes,
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
//

$sql = "
     Select
     E.SupProvider_Id,
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
//echo $sql;
$HeaderData = $this->ReportModel->data_db->query($sql);
$HeaderData_num = $HeaderData->num_rows();
$HeaderData_result = $HeaderData->result();
$HeaderData_row = $HeaderData->row();



//
//
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="SupProvider">
//Select
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
//
//var_dump($HeaderData->num_rows());

if ($HeaderData_num != 0) {
  $Provider_Id_q = $ProviderKey;
} else {
  $Provider_Id_q = 0;
}
//var_dump($HeaderData_row->SupProvider_Id);
if ($HeaderData_row->SupProvider_Id >= 1) {
  $SupProvider_Id_q = $HeaderData_row->SupProvider_Id;
} else {
  $SupProvider_Id_q = 0;
}

$sql = "Select
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
 Where (Provider_Id=$Provider_Id_q  OR Provider_Id=$SupProvider_Id_q)";

$SupProvider = $this->ReportModel->data_db->query($sql);
$SupProvider_result = $SupProvider->result();
$SupProvider_row = $HeaderData->row();

//
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
//	        <cfset RendStruct.FirstName=SupProvider.ProviderFirstName>
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
//
//

foreach ($SupProvider_result as $SupProvider_dt) {

  if ($HeaderData_row->SupProvider_Id == $SupProvider_dt->Provider_Id) {
    $su_FirstName = $SupProvider_dt->ProviderFirstName;
    $su_MiddleName = $SupProvider_dt->ProviderMiddleName;
    $su_LastName = $SupProvider_dt->ProviderLastName;
    $su_Title = $SupProvider_dt->ProviderTitle;
    $su_Suffix = $SupProvider_dt->ProviderSuffix;
    $su_Addr1 = $SupProvider_dt->ProviderAddress1;
    $su_Addr2 = $SupProvider_dt->ProviderAddress2;
    $su_City = $SupProvider_dt->ProviderCity;
    $su_State = $SupProvider_dt->ProviderState;
    $su_Zip = $SupProvider_dt->ProviderZip;
    $su_Phone = $SupProvider_dt->ProviderPhone;
    $su_Fax = $SupProvider_dt->ProviderFax;
  }

  if ($HeaderData_row->Provider_Id == $SupProvider_dt->Provider_Id) {
    if ($ProviderKey != 0 && $ProviderKey != 1) {
      $FirstName = $SupProvider_dt->ProviderFirstName;
      $MiddleName = $SupProvider_dt->ProviderMiddleName;
      $LastName = $SupProvider_dt->ProviderLastName;
      $Title = $SupProvider_dt->ProviderTitle;
      $Suffix = $SupProvider_dt->ProviderSuffix;
      $Addr1 = $SupProvider_dt->ProviderAddress1;
      $Addr2 = $SupProvider_dt->ProviderAddress2;
      $City = $SupProvider_dt->ProviderCity;
      $State = $SupProvider_dt->ProviderState;
      $Zip = $SupProvider_dt->ProviderZip;
      $Phone = $SupProvider_dt->ProviderPhone;
      $Fax = $SupProvider_dt->ProviderFax;
    }
  }
}


//
//
//<table border="0" cellpadding="3" cellspacing="0" style="width: 7.0in;">
//<tr>
//<td nowrap align="left" valign="top">
//	<cfoutput>
//	<cfif Trim(Attributes.DocumentDirectory) EQ "">
//		<cfif Variables.DeptFile NEQ ''>
//			<img src="#Attributes.ProductionServer#/emr#Variables.TempDir##Variables.DeptFile#" border="0">
//		</cfif>
//	<cfelse> <!--- writing document to database--->
//		<cftry>
//			<cfset Variables.TempLogoImageFilesName="#CreateUUID()##RandRange(1,100000)#.jpg">
//			<cffile action = "copy"
//					source = "#Variables.DestinationDirectory##variables.DeptFile#"
//					destination = "#Attributes.DocumentDirectory#\">
//			<cffile action="rename"
//					source="#Attributes.DocumentDirectory#\#variables.DeptFile#"
//					destination="#Attributes.DocumentDirectory#\#Variables.TempLogoImageFilesName#">
//			<img src="$$SOMEECASTABSOLUTEPATH$$#Variables.TempLogoImageFilesName#" border="0">
//
//			<cfcatch type="Any">
//
//			</cfcatch>
//		</cftry>
//
//	</cfif>
//	</cfoutput>
//</td>
//<td align="right" valign="top">
//	<table border="0" cellpadding="3" cellspacing="0">
//	<tr>
//	<td nowrap align="left" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
//		<cfoutput>
//			#HeaderData.FacilityName#<br>
//			<cfif RendStruct.Addr1 NEQ "">
//				#RendStruct.Addr1#<br>
//			</cfif>
//			<cfif RendStruct.Addr2 NEQ "">
//				#RendStruct.Addr2#<br>
//			</cfif>
//			#RendStruct.City#<cfif RendStruct.City NEQ "">,</cfif> #RendStruct.State# #RendStruct.Zip#<br>
//			<cfif RendStruct.Phone NEQ "">
//				P:#RendStruct.Phone#<br>
//			</cfif>
//			<cfif RendStruct.Fax NEQ "">
//				F:#RendStruct.Fax#
//			</cfif>
//		</cfoutput>
//	</td>
//	</tr>
//	</table>
//</td>
//</tr>
//<tr>
//<td align="center" colspan="2">
//<hr noshade size="1" color="Black">
//</td>
//</tr>
//<tr>
//<td nowrap align="left" valign="top">
//	<cfoutput>
//	<table cellpadding="1" cellspacing="0">
//	<tr>
//	<td nowrap align="left" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
//	<cfif HeaderData.FirstName NEQ "">#HeaderData.FirstName#</cfif>
//	<cfif HeaderData.MiddleName NEQ ""> #HeaderData.MiddleName#</cfif>
//	<cfif HeaderData.LastName NEQ ""> #HeaderData.LastName#<br></cfif>
//	<cfif HeaderData.Addr1 NEQ "">#HeaderData.Addr1#<br></cfif>
//	<cfif HeaderData.Addr2 NEQ "">#HeaderData.Addr2#<br></cfif>
//	<cfif HeaderData.City NEQ "" And HeaderData.State NEQ "" And HeaderData.Zip NEQ "">#HeaderData.City#,#HeaderData.State# #HeaderData.Zip#</cfif><br><br>
//	</td>
//	</tr>
//	<tr>
//	<td nowrap align="left" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
//	Account Number: <b>#HeaderData.AccountNumber#</b>
//	</td>
//	<tr>
//	<td nowrap align="left" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
//	MRN: <b>#HeaderData.MedicalRecordNumber#</b>
//	</td>
//	</tr>
//	<tr>
//	<td nowrap align="left" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
//	<br><br>
//	DOS: <b>#DateFormat(HeaderData.EncounterDate,"mmmm d, yyyy")#</b>
//	</td>
//	</tr>
//	</table>
//	</cfoutput>
//</td>
//<td nowrap align="right" valign="top">
//	<table border="0" cellpadding="1" cellspacing="0">
//	<tr>
//	<td nowrap align="right" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
//	Printed:
//	</td>
//	<td nowrap align="left" style="font-size: 12px; color: Black; font-weight: bold; font-family: 'Times New Roman';" valign="top">
//	<cfoutput>
//	#DateFormat(Now(),"mm/d/yyyy")#
//	</cfoutput>
//	</td>
//	</tr>
//	<tr>
//	<td nowrap align="right" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
//	Patient Name:
//	</td>
//	<td nowrap align="left" style="font-size: 12px; color: Black; font-weight: bold; font-family: 'Times New Roman';" valign="top">
//	<cfoutput>
//	#HeaderData.FirstName# #HeaderData.MiddleName# #HeaderData.Lastname#
//	</cfoutput>
//	</td>
//	</tr>
//	<tr>
//	<td nowrap align="right" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
//	SSN:
//	</td>
//	<td nowrap align="left" style="font-size: 12px; color: Black; font-weight: bold; font-family: 'Times New Roman';" valign="top">
//	<cfoutput>
//	#HeaderData.SSN#
//	</cfoutput>
//	</td>
//	</tr>
//	<tr>
//	<td nowrap align="right" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
//	DOB:
//	</td>
//	<td nowrap align="left" style="font-size: 12px; color: Black; font-weight: bold; font-family: 'Times New Roman';" valign="top">
//	<cfoutput>
//	#DateFormat(HeaderData.DOB,"mm/dd/yyyy")#
//	</cfoutput>
//	</td>
//	</tr>
//	<tr>
//	<td nowrap align="right" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
//	Age:
//	</td>
//	<td nowrap align="left" style="font-size: 12px; color: Black; font-weight: bold; font-family: 'Times New Roman';" valign="top">
//	<cfoutput>
//	<cfif Headerdata.DOB NEQ "">
//		<cfset Variables.Age=DateDiff("YYYY",HeaderData.DOB,Now())>
//		#Variables.Age#
//	</cfif>
//	</cfoutput>
//	</td>
//	</tr>
//	<tr>
//	<td nowrap align="right" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
//	Provider:
//	</td>
//	<td nowrap align="left" style="font-size: 12px; color: Black; font-weight: bold; font-family: 'Times New Roman';" valign="top">
//	<cfoutput>
//	      #Trim(RendStruct.Title)# #Trim(RendStruct.FirstName)# #Trim(RendStruct.MiddleName)# #Trim(RendStruct.LastName)#<cfif RendStruct.Suffix NEQ "">, #Trim(RendStruct.Suffix)#</cfif>
//	<cfif (HeaderData.SupProvider_Id GT 0 And HeaderData.Provider_Id GT 1) And (HeaderData.SupProvider_Id NEQ HeaderData.Provider_Id)>
//		<br>#Trim(SupStruct.Title)# #Trim(SupStruct.FirstName)# #Trim(SupStruct.MiddleName)# #Trim(SupStruct.LastName)#<cfif SupStruct.Suffix NEQ "">, #Trim(SupStruct.Suffix)#</cfif>
//	</cfif>
//	</cfoutput>
//	</td>
//	</tr>
//	</table>
//</td>
//</tr>
//</table>
?>



<table border="0" cellpadding="3" cellspacing="0" style="width: 7.0in;">
  <tr>
    <td nowrap align="left" valign="top">
    <!--	<cfoutput>
      <cfif Trim(Attributes.DocumentDirectory) EQ "">
        <cfif Variables.DeptFile NEQ ''>
          <img src="#Attributes.ProductionServer#/emr#Variables.TempDir##Variables.DeptFile#" border="0">
        </cfif>
      <cfelse> - writing document to database-
        <cftry>
          <cfset Variables.TempLogoImageFilesName="#CreateUUID()##RandRange(1,100000)#.jpg">
          <cffile action = "copy"
              source = "#Variables.DestinationDirectory##variables.DeptFile#"
              destination = "#Attributes.DocumentDirectory#\">
          <cffile action="rename"
              source="#Attributes.DocumentDirectory#\#variables.DeptFile#"
              destination="#Attributes.DocumentDirectory#\#Variables.TempLogoImageFilesName#">
          <img src="$$SOMEECASTABSOLUTEPATH$$#Variables.TempLogoImageFilesName#" border="0">

          <cfcatch type="Any">

          </cfcatch>
        </cftry>

      </cfif>
      </cfoutput>-->

      <?php echo (!empty($logo_row->ImageType)) ? '<img src="data:image/'.$logo_row->ImageType.';base64,'.base64_encode( $logo_row->ImageFile ).'"  border="0" width="100"/>' : NULL;?>

    </td>
    <td align="right" valign="top">
      <table border="0" cellpadding="3" cellspacing="0">
        <tr>
          <td nowrap align="left" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
        <!--		<cfoutput>
              #HeaderData.FacilityName#<br>
              <cfif RendStruct.Addr1 NEQ "">
                #RendStruct.Addr1#<br>
              </cfif>
              <cfif RendStruct.Addr2 NEQ "">
                #RendStruct.Addr2#<br>
              </cfif>
              #RendStruct.City#<cfif RendStruct.City NEQ "">,</cfif> #RendStruct.State# #RendStruct.Zip#<br>
              <cfif RendStruct.Phone NEQ "">
                P:#RendStruct.Phone#<br>
              </cfif>
              <cfif RendStruct.Fax NEQ "">
                F:#RendStruct.Fax#
              </cfif>
            </cfoutput>-->
            <?php
            echo $HeaderData_row->FacilityName . '<br/>';

            if (isset($Addr1) && $Addr1 != "") {
              echo $Addr1 . ' <br/>';
            }
            if (isset($Addr2) && $Addr2 != "") {
              echo $Addr2 . ' <br/>';
            }

            if (isset($City) && $City != "") {
              echo $City . ', ';
            }

            if (isset($State) && $State != "") {
              echo $State . ' ';
            }
            if (isset($Zip) && $Zip != "") {
              echo $Zip . ' <br/>';
            }
            ?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td align="center" colspan="2">
      <hr noshade size="1" color="Black">
    </td>
  </tr>
  <tr>
    <td nowrap align="left" valign="top">
  <cfoutput>
    <table cellpadding="1" cellspacing="0">
      <tr>
        <td nowrap align="left" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">

<!--      <cfif HeaderData.FirstName NEQ "">#HeaderData.FirstName#</cfif>
<cfif HeaderData.MiddleName NEQ ""> #HeaderData.MiddleName#</cfif>
<cfif HeaderData.LastName NEQ ""> #HeaderData.LastName#<br></cfif>
<cfif HeaderData.Addr1 NEQ "">#HeaderData.Addr1#<br></cfif>
<cfif HeaderData.Addr2 NEQ "">#HeaderData.Addr2#<br></cfif>
<cfif HeaderData.City NEQ "" And HeaderData.State NEQ "" And HeaderData.Zip NEQ "">#HeaderData.City#,#HeaderData.State# #HeaderData.Zip#</cfif><br><br>
          -->

          <?php
          if ($HeaderData_row->FirstName != "") {
            echo $HeaderData_row->FirstName;
          }
          if ($HeaderData_row->MiddleName != "") {
            echo $HeaderData_row->MiddleName;
          }
          if ($HeaderData_row->LastName != "") {
            echo $HeaderData_row->LastName;
          }
          ?>

        </td>
      </tr>
      <tr>
        <td nowrap align="left" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
          Account Number: <b><?php echo sprintf('%s', $HeaderData_row->AccountNumber); ?></b>
        </td>
      <tr>
        <td nowrap align="left" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
         Policy Number: <b><?php echo $HeaderData_row->MedicalRecordNumber; ?></b>
        </td>
      </tr>
      <tr>
        <td nowrap align="left" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
          <br><br>
          DOS: <b><?php echo ($HeaderData_row->EncounterDate) ? date("M-d,Y", strtotime($HeaderData_row->EncounterDate)) : ""; ?></b>
        </td>
      </tr>
    </table>
  </cfoutput>
</td>
<td nowrap align="right" valign="top">
  <table border="0" cellpadding="1" cellspacing="0">
    <tr>
      <td nowrap align="right" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
        Printed:
      </td>
      <td nowrap align="left" style="font-size: 12px; color: Black; font-weight: bold; font-family: 'Times New Roman';" valign="top">
    <cfoutput>
      <?php echo date('m/d/Y') ?>
    </cfoutput>
</td>
</tr>
<tr>
  <td nowrap align="right" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
    Patient Name:
  </td>
  <td nowrap align="left" style="font-size: 12px; color: Black; font-weight: bold; font-family: 'Times New Roman';" valign="top">
<cfoutput>
  <?php echo $HeaderData_row->FirstName . ' ' . $HeaderData_row->MiddleName . ' ' . $HeaderData_row->LastName; ?>

</cfoutput>
</td>
</tr>
<tr style="display: none">
  <td nowrap align="right" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
    SSN:
  </td>
  <td nowrap align="left" style="font-size: 12px; color: Black; font-weight: bold; font-family: 'Times New Roman';" valign="top">
<cfoutput>
  <?php echo $HeaderData_row->SSN ?>
</cfoutput>
</td>
</tr>
<tr>
  <td nowrap align="right" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
    DOB:
  </td>
  <td nowrap align="left" style="font-size: 12px; color: Black; font-weight: bold; font-family: 'Times New Roman';" valign="top">
<cfoutput>
  <?php echo ($HeaderData_row->DOB) ? date("m/d/Y", strtotime($HeaderData_row->DOB)) : ""; ?>
</cfoutput>
</td>
</tr>
<tr>
  <td nowrap align="right" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
    Age:
  </td>
  <td nowrap align="left" style="font-size: 12px; color: Black; font-weight: bold; font-family: 'Times New Roman';" valign="top">
<cfoutput>
  <?php echo($HeaderData_row->DOB) ? dob_to_age($HeaderData_row->DOB) : ""; ?>
</cfoutput>
</td>
</tr>
<tr>
  <td nowrap align="right" style="font-size: 12px; color: Black; font-weight: normal; font-family: 'Times New Roman';" valign="top">
    Provider:
  </td>
  <td nowrap align="left" style="font-size: 12px; color: Black; font-weight: bold; font-family: 'Times New Roman';" valign="top">
<cfoutput>
  <?php
  if (isset($Title) && $Title != "") {
    echo $Title . ' ';
  }
  if (isset($FirstName) && $FirstName != "") {
    echo $FirstName . ' ';
  }
  if (isset($MiddleName) && $MiddleName != "") {
    echo $MiddleName . ' ';
  }
  if (isset($LastName) && $LastName != "") {
    echo $LastName . ' ';
  }
  if (isset($LastName) && $Suffix != "") {
    echo $Suffix . ' ';
  }

  if (($HeaderData_row->SupProvider_Id > 0 && $HeaderData_row->Provider_Id > 1) && ($HeaderData_row->SupProvider_Id != $HeaderData_row->Provider_Id)) {
    if (isset($su_Title) && $su_Title != "") {
      echo $su_Title . ' ';
    }
    if (isset($su_FirstName) && $su_FirstName != "") {
      echo $su_FirstName . ' ';
    }
    if (isset($su_MiddleName) && $su_MiddleName != "") {
      echo $MiddleName . ' ';
    }
    if (isset($su_LastName) && $su_LastName != "") {
      echo $su_LastName . ' ';
    }
    if (isset($su_LastName) && $su_Suffix != "") {
      echo $su_Suffix . ' ';
    }
  }
  ?>

</cfoutput>
</td>
</tr>
</table>
</td>
</tr>
</table>
