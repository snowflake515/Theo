<?php
//<!---
//      WAD 3/14/07
//	              - modified query SignatureData to include new column "SignedOffSupervising"
//	              - modified logic for dual sign off
//--->
//<!---  IMPORTANT NOTE:  Don't use any Session variables in this routine.  Causes problems
//						when this routine is called from ProcessOldEncountersPDF.cfm
//<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//	<cfset Variables.sId=Session.Id>
//	<cfset Variables.sUserId=Session.User_Id>
//	<cfset Variables.sOrgId=Session.Org_Id>
//</cflock>
//--->
//<cfif isdefined('url.faxkey')>
//	<!--- OK to reference application variables here, as will never be here from ProcessOldEncountersPDF.cfm --->
//	<cfset Variables.DestinationDirectory = Attributes.FaxAttachmentsDirectory>
//	<cfset Variables.TempDir = '/faxattachments/'>
//<cfelse>
//	<cfset Variables.DestinationDirectory = Attributes.TempFilesDirectory>
//	<cfset Variables.TempDir = ReplaceNoCase(Attributes.RelativeTempFilesDirectory,"..","","ALL")>
//</cfif>
//<cfset Variables.FileName="">
//
//<!--- 04/13/2009 JWY - I'll leave this, but all other code for the "old" encounter system are being removed. --->
//<cfset Variables.NewEncounterSystem=1>
//
//<cfquery datasource="#Attributes.EMRDataSource#" name="SignatureData">
//Select
//       P.ProviderFirstName,
//       P.ProviderMiddleName,
//	   P.ProviderLastName,
//	   P.ProviderTitle,
//	   P.ProviderSuffix,
//	   P.ProviderAddress1,
//	   P.ProviderAddress2,
//	   P.ProviderCity,
//	   P.ProviderState,
//	   P.ProviderZip,
//	   P.ProviderPhone,
//	   P.ProviderFax,
//	   P.Provider_Id,
//	   E.EncounterNotes,
//	   E.EncounterDate,
//	   E.Dept_Id,
//	   E.Provider_Id,
//	   isnull(E.SupProvider_Id, 0) as SupProvider_Id,
//	   E.ReferredByProvider,
//	   E.EncounterSignedOff,
//	   E.SignedOffSupervising,
//	   dbo.UTCtoLocal(E.RenderingSignedOffDate_UTC, <cfqueryparam cfsqltype="CF_SQL_NUMERIC" scale="2" value="#Attributes.UTC_TimeOffset#">, <cfqueryparam cfsqltype="CF_SQL_BIT" value="#Attributes.UTC_DST#">) as RenderingSignedOffDate,
//	   dbo.UTCtoLocal(E.SupervisingSignedOffDate_UTC, <cfqueryparam cfsqltype="CF_SQL_NUMERIC" scale="2" value="#Attributes.UTC_TimeOffset#">, <cfqueryparam cfsqltype="CF_SQL_BIT" value="#Attributes.UTC_DST#">) as SupervisingSignedOffDate,
//	   E.ChiefComplaint,
//	   E.ReferredAux1,
//	   E.ReferredAux2,
//	   E.ReferredAux3,
//	   E.Org_Id,
//	   D.EncounterDescription,
//	   D.EncounterDescription_Id,
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
//  Join  PatientProfile PP
//    On E.Patient_Id=PP.Patient_Id
//  Join ProviderProfile P
//    On P.Provider_Id=E.Provider_Id
//  Left Outer Join ProblemList PL
//    On E.Problem_Id=PL.Problem_Id
// Where E.Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.PrimaryKey#">
//</cfquery>
//SKIPP UTCtoLocal

$sql = "Select
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
        P.Provider_Id,
        E.EncounterNotes,
        E.EncounterDate,
        E.Dept_Id,
        E.Provider_Id,
        isnull(E.SupProvider_Id, 0) as SupProvider_Id,
        E.ReferredByProvider,
        E.EncounterSignedOff,
        E.SignedOffSupervising,
        E.RenderingSignedOffDate_UTC as RenderingSignedOffDate,
        E.SupervisingSignedOffDate_UTC  as SupervisingSignedOffDate,
        E.ChiefComplaint,
        E.ReferredAux1,
        E.ReferredAux2,
        E.ReferredAux3,
        E.Org_Id,
        D.EncounterDescription,
        D.EncounterDescription_Id,
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
     From EncounterHistory E
     Left Outer Join EncounterDescriptionList D
       On E.EncounterDescription_Id=D.EncounterDescription_Id
     Join  PatientProfile PP
       On E.Patient_Id=PP.Patient_Id
     Join ProviderProfile P
       On P.Provider_Id=E.Provider_Id
     Left Outer Join ProblemList PL
       On E.Problem_Id=PL.Problem_Id
    Where E.Encounter_Id=$PrimaryKey";

$SignatureData = $this->ReportModel->data_db->query($sql);
$SignatureData_num = $SignatureData->num_rows();
$SignatureData_row = $SignatureData->row();

//<cfquery datasource="#Attributes.EMRDataSource#" name="SuperProvider">
//	Select TOP 1
//		ProviderFirstName,
//		ProviderMiddleName,
//		ProviderLastName,
//		ProviderTitle,
//		ProviderSuffix
//	From ProviderProfile
//	Where Provider_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#SignatureData.SupProvider_Id#">
//</cfquery>

$sql = "	Select TOP 1
		ProviderFirstName,
		ProviderMiddleName,
		ProviderLastName,
		ProviderTitle,
		ProviderSuffix
	From ProviderProfile
	Where Provider_Id=$SignatureData_row->SupProvider_Id";

$SuperProvider = $this->ReportModel->data_db->query($sql);
$SuperProvider_num = $SuperProvider->num_rows();
$SuperProvider_row = $SuperProvider->row();


//<cfset Variables.SigFileRendering="">
//<cfquery datasource="#Attributes.ImageDataSource#" name="GetProviderType">
//Select TOP 1
//       ImageType
//  From AdminImages
// Where Provider_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Attributes.ProviderKey#">
//</cfquery>

$sql = "Select TOP 1
       ImageFile, ImageType
  From " . $image_db . ".dbo.AdminImages
 Where Provider_Id=$ProviderKey";

$GetProviderType = $this->ReportModel->data_db->query($sql);
$GetProviderType_num = $GetProviderType->num_rows();
$GetProviderType_result = $GetProviderType->result();
$GetProviderType_row = $GetProviderType->row();

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
//<cfset variables.BodyFontInfo = CreateObject("component","cfc.chartnote.chartnote").getChartFooterFontInfo(Left(CGI.Server_Name,Find(".",CGI.Server_Name,1)-1),Attributes.ConfigKey)>
//<cfset variables.DefaultStyle = "color: " & Variables.BodyFontInfo.FontColor & "; font-size: " & variables.BodyFontInfo.FontSize & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">
//<cfset variables.AltStyle = "color: maroon; font-size: " & variables.BodyFontInfo.FontSize + 4 & "px; font-weight: " & Variables.BodyFontInfo.FontWeight & "; font-family: " & variables.BodyFontInfo.FontFace & "; font-style: " & Variables.BodyFontInfo.FontStyle & "; text-decoration: " & Variables.BodyFontInfo.FontDecoration & ";">


$data['data_db'] = $data_db;
$HeaderKey = isset($HeaderKey) ? $HeaderKey: 0;
$BodyFontInfo = getChartFooterFontInfo($data, $HeaderKey);
$DefaultStyle = "color: " . $BodyFontInfo['FontColor'] . "; font-size: " . $BodyFontInfo['FontSize'] . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";
$AltStyle = "color: maroon; font-size: " . ((int) $BodyFontInfo['FontSize'] + 4 ) . "px; font-weight: " . $BodyFontInfo['FontWeight'] . "; font-family: " . $BodyFontInfo['FontFace'] . "; font-style: " . $BodyFontInfo['FontStyle'] . "; text-decoration: " . $BodyFontInfo['FontDecoration'] . ";";


//<cfif GetProviderType.RecordCount NEQ 0>
//
//	<cfset Variables.SigFileRendering="#CreateUUID()##RandRange(1,100000)#.#GetProviderType.ImageType#">
//<!---	<cfset Variables.SigFileRendering=Trim(Variables.sUserId)&Variables.sOrgId&Variables.sId&Month(Now())&Day(Now())&Year(Now())&TimeFormat(Now(),"hh")&TimeFormat(Now(),"mm")&TimeFormat(Now(),"ss")&RandRange(1,1000)&"."&GetProviderType.ImageType> --->
// 	<CF_BLOBSELECT sqlServerName="#Attributes.DatabaseIPAddress#"
//		dsn="#Attributes.ImageDataSource#"
//  		PortNumber="1433"
//  		LOGIN="#Attributes.DatabaseUserId#"
//  		PASSWORD="#Attributes.DatabasePassword#"
//  		QUERYSTRING="Select ImageFile From #Attributes.DSNPreFix#eCastEMR_Images.dbo.AdminImages Where Provider_Id=#Attributes.ProviderKey#"
//  		FILENAME="#Variables.DestinationDirectory##Variables.SigFileRendering#">
//	<cfif (Len(Variables.SigFileRendering)-4) GT 0>
//	 	<cf_imagecr3 load="#Variables.DestinationDirectory##Variables.SigFileRendering#" save="#Variables.DestinationDirectory#n#Left(Variables.SigFileRendering,Len(Variables.SigFileRendering)-4)#.jpg" resize=">x50">
//		<cfset Variables.SigFileRendering='n'&Left(Variables.SigFileRendering,Len(Variables.SigFileRendering)-4)&'.jpg'>
//	</cfif>
//</cfif>


if ($GetProviderType_num != 0) {
//   SKIPP
}

//<cfset Variables.SigFileSupervising="">
$SigFileSupervising = "";

//<cfif SignatureData.SupProvider_Id NEQ 0>
//	<cfquery datasource="#Attributes.ImageDataSource#" name="GetSupProviderType">
//	Select TOP 1
//	       ImageType
//	  From AdminImages
//	 Where Provider_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#SignatureData.SupProvider_Id#">
//	</cfquery>
//
//	<cfif GetSupProviderType.RecordCount NEQ 0>
//		<cfset Variables.SigFileSupervising="#CreateUUID()##RandRange(1,100000)#.#GetProviderType.ImageType#">
//<!---		<cfset Variables.SigFileSupervising=Trim(Variables.sUserId)&Variables.sOrgId&Variables.sId&Month(Now())&Day(Now())&Year(Now())&TimeFormat(Now(),"hh")&TimeFormat(Now(),"mm")&TimeFormat(Now(),"ss")&RandRange(1,1000)&"."&GetProviderType.ImageType> --->
//	 	<CF_BLOBSELECT sqlServerName="#Attributes.DatabaseIPAddress#"
//			dsn="#Attributes.ImageDataSource#"
//	  		PortNumber="1433"
//	  		LOGIN="#Attributes.DatabaseUserId#"
//	  		PASSWORD="#Attributes.DatabasePassword#"
//	  		QUERYSTRING="Select ImageFile From #Attributes.DSNPreFix#eCastEMR_Images.dbo.AdminImages Where Provider_Id=#SignatureData.SupProvider_Id#"
//	  		FILENAME="#Variables.DestinationDirectory##Variables.SigFileSupervising#">
//		<cfif (Len(Variables.SigFileSupervising)-4) GT 0>
//		 	<cf_imagecr3 load="#Variables.DestinationDirectory##Variables.SigFileSupervising#" save="#Variables.DestinationDirectory#n#Left(Variables.SigFileSupervising,Len(Variables.SigFileSupervising)-4)#.jpg" resize=">x50">
//			<cfset Variables.SigFileSupervising='n'&Left(Variables.SigFileSupervising,Len(Variables.SigFileSupervising)-4)&'.jpg'>
//		</cfif>
//	</cfif>
//</cfif>

if ($SignatureData_row->SupProvider_Id != 0) {
  $sql = "	Select TOP 1
	       ImageType
	  From " . $image_db . ".dbo.AdminImages
	 Where Provider_Id=$SignatureData_row->SupProvider_Id";

  $GetSupProviderType = $this->ReportModel->data_db->query($sql);
  $GetSupProviderType_num = $GetSupProviderType->num_rows();
  $GetSupProviderType_row = $GetSupProviderType->row();

  if ($GetSupProviderType_num != 0) {
    $SigFileSupervising = ""; //SKIPP
  }
}

//<cfif SignatureData.ReferredByProvider GT 0 OR SignatureData.ReferredAux1 GT 0 OR SignatureData.ReferredAux2 GT 0 OR SignatureData.ReferredAux3 GT 0>
//		<cfset ProvidersIdList="0">
//		<cfif SignatureData.ReferredByProvider GT 0>
//			<cfset Variables.ProvidersIdList=Variables.ProvidersIdList&","&SignatureData.ReferredbyProvider>
//		</cfif>
//		<cfif SignatureData.ReferredAux1 GT 0>
//			<cfset Variables.ProvidersIdList=Variables.ProvidersIdList&","&SignatureData.ReferredAux1>
//		</cfif>
//		<cfif SignatureData.ReferredAux2 GT 0>
//			<cfset Variables.ProvidersIdList=Variables.ProvidersIdList&","&SignatureData.ReferredAux2>
//		</cfif>
//		<cfif SignatureData.ReferredAux3 GT 0>
//			<cfset Variables.ProvidersIdList=Variables.ProvidersIdList&","&SignatureData.ReferredAux3>
//		</cfif>
//	<cfquery datasource="#Attributes.EMRDataSource#" name="CC">
//	Select RefProvFirst,
//           RefProvMiddle,
//	       RefProvLast,
//	       RefProvTitle,
//		   RefProv_Id
//	  From ReferringProviderList
//	 Where RefProv_Id IN (<cfqueryparam list="Yes" separator="," value="#Variables.ProvidersIdList#">)
//	</cfquery>
//</cfif>
//
if ($SignatureData_row->ReferredByProvider > 0 || $SignatureData_row->ReferredAux1 > 0 || $SignatureData_row->ReferredAux2 > 0 || $SignatureData_row->ReferredAux3 > 0) {
  $ProvidersIdList = 0;
  if ($SignatureData_row->ReferredByProvider > 0) {
    $ProvidersIdList = $ProvidersIdList . "," . $SignatureData_row->ReferredByProvider;
  }
  if ($SignatureData_row->ReferredAux1 > 0) {
    $ProvidersIdList = $ProvidersIdList . "," . $SignatureData_row->ReferredAux1;
  }
  if ($SignatureData_row->ReferredAux2 > 0) {
    $ProvidersIdList = $ProvidersIdList . "," . $SignatureData_row->ReferredAux2;
  }
  if ($SignatureData_row->ReferredAux3 > 0) {
    $ProvidersIdList = $ProvidersIdList . "," . $SignatureData_row->ReferredAux3;
  }
  $sql = "Select RefProvFirst,
              RefProvMiddle,
              RefProvLast,
              RefProvTitle,
              RefProv_Id
            From ReferringProviderList
            Where RefProv_Id IN ($ProvidersIdList)";

  $CC = $this->ReportModel->data_db->query($sql);
  $CC_num = $CC->num_rows();
  $CC_result = $CC->result();
}
//<cfset ReferredByProviderStruct=StructNew()>
//<cfset ReferredAux1Struct=StructNew()>
//<cfset ReferredAux2Struct=StructNew()>
//<cfset ReferredAux3Struct=StructNew()>
$ReferredByProviderStruct = array();
$ReferredAux1Struct = array();
$ReferredAux2Struct = array();
$ReferredAux3Struct = array();
//<cfif SignatureData.ReferredByProvider GT 0 OR SignatureData.ReferredAux1 GT 0 OR SignatureData.ReferredAux2 GT 0 OR SignatureData.ReferredAux3 GT 0>
//	<cfif CC.RecordCount NEQ 0>
//		<cfloop query="CC">
//			<cfif SignatureData.ReferredByProvider EQ CC.RefProv_Id>
//				<cfset Temp=StructInsert(ReferredByProviderStruct,1,1,TRUE)>
//				<cfset ReferredByProviderStruct.RefProvFirst=Trim(CC.RefProvfirst)>
//				<cfset ReferredByProviderStruct.RefProvMiddle=Trim(CC.RefProvMiddle)>
//				<cfset ReferredByProviderStruct.RefProvLast=Trim(CC.RefProvLast)>
//				<cfset ReferredByProviderStruct.RefProvTitle=Trim(CC.RefProvTitle)>
//			</cfif>
//			<cfif SignatureData.ReferredAux1 EQ CC.RefProv_Id>
//				<cfset Temp=StructInsert(ReferredAux1Struct,1,1,TRUE)>
//				<cfset ReferredAux1Struct.RefProvFirst=Trim(CC.RefProvfirst)>
//				<cfset ReferredAux1Struct.RefProvMiddle=Trim(CC.RefProvMiddle)>
//				<cfset ReferredAux1Struct.RefProvLast=Trim(CC.RefProvLast)>
//				<cfset ReferredAux1Struct.RefProvTitle=Trim(CC.RefProvTitle)>
//			</cfif>
//			<cfif SignatureData.ReferredAux2 EQ CC.RefProv_Id>
//				<cfset Temp=StructInsert(ReferredAux2Struct,1,1,TRUE)>
//				<cfset ReferredAux2Struct.RefProvFirst=Trim(CC.RefProvfirst)>
//				<cfset ReferredAux2Struct.RefProvMiddle=Trim(CC.RefProvMiddle)>
//				<cfset ReferredAux2Struct.RefProvLast=Trim(CC.RefProvLast)>
//				<cfset ReferredAux2Struct.RefProvTitle=Trim(CC.RefProvTitle)>
//			</cfif>
//			<cfif SignatureData.ReferredAux3 EQ CC.RefProv_Id>
//				<cfset Temp=StructInsert(ReferredAux3Struct,1,1,TRUE)>
//				<cfset ReferredAux3Struct.RefProvFirst=Trim(CC.RefProvfirst)>
//				<cfset ReferredAux3Struct.RefProvMiddle=Trim(CC.RefProvMiddle)>
//				<cfset ReferredAux3Struct.RefProvLast=Trim(CC.RefProvLast)>
//				<cfset ReferredAux3Struct.RefProvTitle=Trim(CC.RefProvTitle)>
//			</cfif>
//		</cfloop>
//	</cfif>
//</cfif>

if ($SignatureData_row->ReferredByProvider > 0 || $SignatureData_row->ReferredAux1 > 0 || $SignatureData_row->ReferredAux2 || $SignatureData_row->ReferredAux3 > 0) {
  if ($CC_num != 0) {
    foreach ($CC_result as $cr) {
      if ($SignatureData_row->ReferredByProvider == $cr->RefProv_Id) {
        $ReferredByProviderStruct = array(
            'RefProvFirst' => trim($cr->RefProvfirst),
            'RefProvMiddle' => trim($cr->RefProvMiddle),
            'RefProvLast' => trim($cr->RefProvLast),
            'RefProvTitle' => trim($cr->RefProvTitle),
        );
      }

      if ($SignatureData_row->ReferredAux1 == $cr->RefProv_Id) {
        $ReferredAux1Struct = array(
            'RefProvFirst' => trim($cr->RefProvfirst),
            'RefProvMiddle' => trim($cr->RefProvMiddle),
            'RefProvLast' => trim($cr->RefProvLast),
            'RefProvTitle' => trim($cr->RefProvTitle),
        );
      }

      if ($SignatureData_row->ReferredAux2 == $cr->RefProv_Id) {
        $ReferredAux2Struct = array(
            'RefProvFirst' => trim($cr->RefProvfirst),
            'RefProvMiddle' => trim($cr->RefProvMiddle),
            'RefProvLast' => trim($cr->RefProvLast),
            'RefProvTitle' => trim($cr->RefProvTitle),
        );
      }

      if ($SignatureData_row->ReferredAux3 == $cr->RefProv_Id) {
        $ReferredAux3Struct = array(
            'RefProvFirst' => trim($cr->RefProvfirst),
            'RefProvMiddle' => trim($cr->RefProvMiddle),
            'RefProvLast' => trim($cr->RefProvLast),
            'RefProvTitle' => trim($cr->RefProvTitle),
        );
      }
    }
  }
}

//
//<cfset variables.FreezeDocument = 0>
//<cfif (Trim(Attributes.DocumentDirectory) NEQ "") and (SignatureData.EncounterSignedOff EQ 1) and (SignatureData.SignedOffSupervising EQ 1)>
//	<cfset variables.FreezeDocument = 1>
//</cfif>

$FreezeDocument = 0;
//remove DocumentDirectory variable
if ($SignatureData_row->EncounterSignedOff == 1 && $SignatureData_row->SignedOffSupervising == 1) {
  $FreezeDocument = 1;
}
?>

<table cellpadding="0" cellspacing="0">
  <tr>
    <td align="left" width="300" style="<?php echo $DefaultStyle; ?>" valign="bottom" nowrap>
      <br><br>
<?php
if ($SignatureData_row->EncounterSignedOff != 1) {
  ?>
        <span style="<?php echo $AltStyle; ?>">
          <strong>Not Signed Off</strong>
        </span>
        <?php
      } else if ($FreezeDocument == 0) {
        echo ($GetProviderType_row) ? '<img src="data:image/' . $GetProviderType_row->ImageType . ';base64,' . base64_encode($GetProviderType_row->ImageFile) . '"  border="0" width="100"/>' : "";
//        echo '<img src="#Attributes.ProductionServer#/emr#Variables.TempDir##variables.SigFileRendering#" border="0">';
      } else {
//        	<cftry>
//					<cfset Variables.TempSigImageFilesName="#CreateUUID()##RandRange(1,100000)#.jpg">
//					<cffile action = "copy"
//							source = "#Variables.DestinationDirectory##variables.SigFileRendering#"
//							destination = "#Attributes.DocumentDirectory#\">
//					<cffile action="rename"
//							source="#Attributes.DocumentDirectory#\#variables.SigFileRendering#"
//							destination="#Attributes.DocumentDirectory#\#Variables.TempSigImageFilesName#">
//					<img src="$$SOMEECASTABSOLUTEPATH$$#Variables.TempSigImageFilesName#" border="0">
//					<cfcatch type="Any">
//						<img src="#Attributes.ProductionServer#/emr#Variables.TempDir##variables.SigFileRendering#" border="0">
//					</cfcatch>
//				</cftry>
        echo ($GetProviderType_row) ? '<img src="data:image/' . $GetProviderType_row->ImageType . ';base64,' . base64_encode($GetProviderType_row->ImageFile) . '"  border="0" width="100"/>' : "";
      }
      ?>

    </td>

    <td align="left" style="<?php echo $DefaultStyle; ?>" valign="bottom" nowrap>
      <br><br>
<?php
if ($SignatureData_row->Provider_Id == $SignatureData_row->SupProvider_Id || $SignatureData_row->SupProvider_Id == 0) {
  echo "&nbsp;";
} else if($SignatureData_row->SignedOffSupervising != 1){
?>
    <span style="<?php echo $AltStyle; ?>">
          <strong>Not Signed Off</strong>
    </span>
<?php
}else if ($FreezeDocument == 0) {
          echo ($GetProviderType_row) ? '<img src="data:image/' . $GetProviderType_row->ImageType . ';base64,' . base64_encode($GetProviderType_row->ImageFile) . '"  border="0" width="100"/>' : "";
//        echo '<img src="#Attributes.ProductionServer#/emr#Variables.TempDir##variables.SigFileSupervising#" border="0">';
} else {
//        <cftry>
//					<cfset Variables.TempSigImageFilesName2="#CreateUUID()##RandRange(1,100000)#.jpg">
//					<cffile action = "copy"
//							source = "#Variables.DestinationDirectory##variables.SigFileSupervising#"
//							destination = "#Attributes.DocumentDirectory#\">
//					<cffile action="rename"
//							source="#Attributes.DocumentDirectory#\#variables.SigFileSupervising#"
//							destination="#Attributes.DocumentDirectory#\#Variables.TempSigImageFilesName2#">
//					<img src="$$SOMEECASTABSOLUTEPATH$$#Variables.TempSigImageFilesName2#" border="0">
//					<cfcatch type="Any">
//						<img src="#Attributes.ProductionServer#/emr#Variables.TempDir##variables.SigFileSupervising#" border="0">
//					</cfcatch>
//				</cftry>
      echo ($GetProviderType_row) ? '<img src="data:image/' . $GetProviderType_row->ImageType . ';base64,' . base64_encode($GetProviderType_row->ImageFile) . '"  border="0" width="100"/>' : "";
}
?>
    </td>
  </tr>


  <tr>
    <td align="left" style="<?php echo $DefaultStyle; ?>" valign="baseline" nowrap>

<?php
//      <cfif SignatureData.EncounterSignedOff NEQ 1>
//				#Trim(SignatureData.ProviderTitle)# #Trim(SignatureData.ProviderFirstName)# #Trim(SignatureData.ProviderMiddleName)# #Trim(SignatureData.ProviderLastName)#
//        <cfif SignatureData.Providersuffix NEQ "">, #Trim(SignatureData.ProviderSuffix)#</cfif><br>
//			<cfelseif variables.FreezeDocument EQ 0>
//				<CF_ElectronicSignatureStatement EMRDataSource="#Attributes.EMRDataSource#" OrgID="#SignatureData.Org_Id#">
//				#Trim(SignatureData.ProviderTitle)# #Trim(SignatureData.ProviderFirstName)# #Trim(SignatureData.ProviderMiddleName)# #Trim(SignatureData.ProviderLastName)#<cfif SignatureData.Providersuffix NEQ "">, #Trim(SignatureData.ProviderSuffix)#</cfif><br>
//				#DateFormat(SignatureData.RenderingSignedOffDate,"mm/dd/yyyy")# #TimeFormat(SignatureData.RenderingSignedOffDate,"hh:mm tt")#
//			<cfelse>
//				<CF_ElectronicSignatureStatement EMRDataSource="#Attributes.EMRDataSource#" OrgID="#SignatureData.Org_Id#">
//				#Trim(SignatureData.ProviderTitle)# #Trim(SignatureData.ProviderFirstName)# #Trim(SignatureData.ProviderMiddleName)# #Trim(SignatureData.ProviderLastName)#<cfif SignatureData.Providersuffix NEQ "">, #Trim(SignatureData.ProviderSuffix)#</cfif><br>
//				#DateFormat(SignatureData.RenderingSignedOffDate,"mm/dd/yyyy")# #TimeFormat(SignatureData.RenderingSignedOffDate,"hh:mm tt")#
//			</cfif>
if ($SignatureData_row->EncounterSignedOff != 1) {
  echo trim($SignatureData_row->ProviderTitle) . " " . trim($SignatureData_row->ProviderFirstName) . " " . trim($SignatureData_row->ProviderMiddleName) . " ". trim($SignatureData_row->ProviderLastName);
  if ($SignatureData_row->ProviderSuffix != "") {
    echo ", ".trim($SignatureData_row->ProviderSuffix) . " <br/>";
  }
} else if ($FreezeDocument == 0) {
//        <CF_ElectronicSignatureStatement EMRDataSource="#Attributes.EMRDataSource#" OrgID="#SignatureData.Org_Id#">
  echo trim($SignatureData_row->ProviderTitle) . " " . trim($SignatureData_row->ProviderFirstName) . " " . trim($SignatureData_row->ProviderMiddleName) . " ". trim($SignatureData_row->ProviderLastName);
  if ($SignatureData_row->ProviderSuffix != "") {
    echo ", ".trim($SignatureData_row->ProviderSuffix) . " <br/>";
  }
  echo ($SignatureData_row->RenderingSignedOffDate) ? date('m/d/Y', strtotime($SignatureData_row->RenderingSignedOffDate)) : NULL;
} else {
//        <CF_ElectronicSignatureStatement EMRDataSource="#Attributes.EMRDataSource#" OrgID="#SignatureData.Org_Id#">
  echo trim($SignatureData_row->ProviderTitle) . " " . trim($SignatureData_row->ProviderFirstName) . " " . trim($SignatureData_row->ProviderMiddleName) . " ". trim($SignatureData_row->ProviderLastName);
  if ($SignatureData_row->ProviderSuffix != "") {
    echo ", ".trim($SignatureData_row->ProviderSuffix) . " <br/>";
  }
  echo ($SignatureData_row->RenderingSignedOffDate) ? date('m/d/Y', strtotime($SignatureData_row->RenderingSignedOffDate)) : NULL;
}
?>
    </td>

    <td align="left" style="<?php echo $DefaultStyle; ?>" valign="baseline" nowrap>
      <?php
//       <cfif (SignatureData.Provider_Id EQ SignatureData.SupProvider_Id) OR (SignatureData.Provider_Id EQ 0)>
//				&nbsp;
//			<cfelseif SignatureData.SignedOffSupervising NEQ 1>
//				#Trim(SuperProvider.ProviderTitle)# #Trim(SuperProvider.ProviderFirstName)# #Trim(SuperProvider.ProviderMiddleName)# #Trim(SuperProvider.ProviderLastName)#
//        <cfif SuperProvider.ProviderSuffix NEQ "">, #Trim(SuperProvider.ProviderSuffix)#</cfif><br>
//			<cfelseif variables.FreezeDocument EQ 0>
//				<CF_ElectronicSignatureStatement EMRDataSource="#Attributes.EMRDataSource#" OrgID="#SignatureData.Org_Id#">
//				#Trim(SuperProvider.ProviderTitle)# #Trim(SuperProvider.ProviderFirstName)# #Trim(SuperProvider.ProviderMiddleName)# #Trim(SuperProvider.ProviderLastName)#
//        <cfif SuperProvider.ProviderSuffix NEQ "">, #Trim(SuperProvider.ProviderSuffix)#</cfif><br>
//				#DateFormat(SignatureData.SupervisingSignedOffDate,"mm/dd/yyyy")# #TimeFormat(SignatureData.SupervisingSignedOffDate,"hh:mm tt")#
//			<cfelse>
//				<CF_ElectronicSignatureStatement EMRDataSource="#Attributes.EMRDataSource#" OrgID="#SignatureData.Org_Id#">
//				#Trim(SuperProvider.ProviderTitle)# #Trim(SuperProvider.ProviderFirstName)# #Trim(SuperProvider.ProviderMiddleName)# #Trim(SuperProvider.ProviderLastName)#
//        <cfif SuperProvider.ProviderSuffix NEQ "">, #Trim(SuperProvider.ProviderSuffix)#</cfif><br>
//				#DateFormat(SignatureData.SupervisingSignedOffDate,"mm/dd/yyyy")# #TimeFormat(SignatureData.SupervisingSignedOffDate,"hh:mm tt")#
//			</cfif>



      if ($SignatureData_row->Provider_Id == $SignatureData_row->SupProvider_Id || $SignatureData_row->Provider_Id == 0) {
        echo "&nbsp;";
      } else if ($SignatureData_row->SignedOffSupervising != 1 && $SuperProvider_num) {
        echo $SuperProvider_row->ProviderTitle . " " . $SuperProvider_row->ProviderFirstName . " " . $SuperProvider_row->ProviderMiddleName . " " . $SuperProvider_row->ProviderLastName;
        if ($SuperProvider_row->ProviderSuffix != "") {
          echo ", " . trim($SuperProvider_row->ProviderSuffix) . "<br/>";
        }
      } else if ($FreezeDocument == 0 && $SuperProvider_num) {
//          <CF_ElectronicSignatureStatement EMRDataSource="#Attributes.EMRDataSource#" OrgID="#SignatureData.Org_Id#">
        echo $SuperProvider_row->ProviderTitle . " " . $SuperProvider_row->ProviderFirstName . " " . $SuperProvider_row->ProviderMiddleName . " " . $SuperProvider_row->ProviderLastName;
        if ($SuperProvider_row->ProviderSuffix != "") {
          echo ", " . trim($SuperProvider_row->ProviderSuffix) . "<br/>";
        }
        echo ($SignatureData_row->SupervisingSignedOffDate)? date('m/d/Y', strtotime($SignatureData_row->SupervisingSignedOffDate)) : NULL;
      } else {
        if ($SuperProvider_num) {
          //          <CF_ElectronicSignatureStatement EMRDataSource="#Attributes.EMRDataSource#" OrgID="#SignatureData.Org_Id#">
          echo $SuperProvider_row->ProviderTitle . " " . $SuperProvider_row->ProviderFirstName . " " . $SuperProvider_row->ProviderMiddleName . " " . $SuperProvider_row->ProviderLastName;
          if ($SuperProvider_row->ProviderSuffix != "") {
            echo ", " . trim($SuperProvider_row->ProviderSuffix) . "<br/>";
          }
          echo ($SignatureData_row->SupervisingSignedOffDate) ? date('m/d/Y', strtotime($SignatureData_row->SupervisingSignedOffDate)) : NULL;
        }
      }
      ?>
    </td>
  </tr>

  <tr>
    <td colspan="2" align="left" style="<?php echo $DefaultStyle; ?>" valign="top">
      <?php
//      <cfif SignatureData.ReferredByProvider GT 0 OR SignatureData.ReferredAux1 GT 0 OR SignatureData.ReferredAux2 GT 0 OR SignatureData.ReferredAux3 GT 0>
//				<cfif CC.RecordCount NEQ 0>
//					<br>
//					<cfif StructKeyExists(ReferredByProviderStruct,1)>
//						<br>CC: #ReferredByProviderStruct.RefProvTitle# #ReferredByProviderStruct.RefProvFirst# #ReferredByProviderStruct.RefProvMiddle# #ReferredByProviderStruct.RefProvLast#
//					</cfif>
//					<cfif StructKeyExists(ReferredAux1Struct,1)>
//						<br>CC: #ReferredAux1Struct.RefProvTitle# #ReferredAux1Struct.RefProvFirst# #ReferredAux1Struct.RefProvMiddle# #ReferredAux1Struct.RefProvLast#
//					</cfif>
//					<cfif StructKeyExists(ReferredAux2Struct,1)>
//						<br>CC: #ReferredAux2Struct.RefProvTitle# #ReferredAux2Struct.RefProvFirst# #ReferredAux2Struct.RefProvMiddle# #ReferredAux2Struct.RefProvLast#
//					</cfif>
//					<cfif StructKeyExists(ReferredAux3Struct,1)>
//						<br>CC: #ReferredAux3Struct.RefProvTitle# #ReferredAux3Struct.RefProvFirst# #ReferredAux3Struct.RefProvMiddle# #ReferredAux3Struct.RefProvLast#
//					</cfif>
//				</cfif>
//			</cfif>
      if ($SignatureData_row->ReferredByProvider > 0 || $SignatureData_row->ReferredAux1 > 0 || $SignatureData_row->ReferredAux2 > 0 || $SignatureData_row->ReferredAux3 > 0) {
        if ($CC_num != 0) {
          if (isset($ReferredByProviderStruct) && $ReferredByProviderStruct != NULL) {
            echo "<br>CC: " . $ReferredByProviderStruct['RefProvTitle'] . " " . $ReferredByProviderStruct['RefProvFirst'] . " " . $ReferredByProviderStruct['RefProvMiddle'] . " " . $ReferredByProviderStruct['RefProvLast'];
          }
          if (isset($ReferredAux1Struct) && $ReferredAux1Struct != NULL) {
            echo "<br>CC: " . $ReferredAux1Struct['RefProvTitle'] . " " . $ReferredAux1Struct['RefProvFirst'] . " " . $ReferredAux1Struct['RefProvMiddle'] . " " . $ReferredAux1Struct['RefProvLast'];
          }
          if (isset($ReferredAux2Struct) && $ReferredAux2Struct != NULL) {
            echo "<br>CC: " . $ReferredAux2Struct['RefProvTitle'] . " " . $ReferredAux2Struct['RefProvFirst'] . " " . $ReferredAux2Struct['RefProvMiddle'] . " " . $ReferredAux2Struct['RefProvLast'];
          }
          if (isset($ReferredAux3Struct) && $ReferredAux3Struct != NULL) {
            echo "<br>CC: " . $ReferredAux3Struct['RefProvTitle'] . " " . $ReferredAux3Struct['RefProvFirst'] . " " . $ReferredAux3Struct['RefProvMiddle'] . " " . $ReferredAux3Struct['RefProvLast'];
          }
        }
      }
      ?>
    </td>
  </tr>
</table>
