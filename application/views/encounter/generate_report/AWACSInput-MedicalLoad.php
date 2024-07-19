<?php

//
//<!--- 
//	<responsibilities>This page calculates the AWACS Risk Factor for medical related information already in the system and inserts the data to the database</responsibilities>
//
//	<note author="Chris Hoffman" date="7 July 2011"> File: AWACSInput-MedicalLoad.cfm
//													Case: 10,008 - Created file
//	</note>
//	<io>
//		<in>
//			<number name="URL.Encounter_id" scope="url" precision="integer" comments="This is the Encounter Id" />
//		</in>
//
//		<out>
//		</out>
//	</io>
//
//  --->
//
//<!--- The following is from AWACS_RptMenu.cfm --->
//<cfset variables.Encounter_Id = GetEncounterList.Encounter_ID>  
//  
$Encounter_Id = $Encounter_dt->Encounter_ID;
//  
//<cfset Variables.BMI = 0>
//<cfset Variables.Systolic = 0>
//<cfset Variables.Diastolic = 0>
//<cfset Variables.Age = 0>
//<cfset Variables.WaistInch = 0>
//<cfset Variables.CheckICD9Codes = 1>
//<cfset Variables.HasNewHistoryId = 0>
//<cfset Variables.HasFamilyHistory = 0>
//
$BMI = 0;
$Systolic = 0;
$Diastolic = 0;
$Age = 0;
$WaistInch = 0;
$CheckICD9Codes = 1;
$HasNewHistoryId = 0;
$HasFamilyHistory = 0;
//
//<cfset Variables.TValue1 = -1>
//<cfset Variables.TValue2 = -1>
//<cfset Variables.TValue3 = -1>
//<cfset Variables.TValue4 = -1>
//<cfset Variables.TValue5 = -1>
//<cfset Variables.TValue6 = -1>
//<cfset Variables.TValue7 = -1>
//<cfset Variables.TValue8 = -1>
//<cfset Variables.TValue9 = -1>
//<cfset Variables.TValue10 = -1>
//<cfset Variables.TValue11 = -1>
//<cfset Variables.TValue12 = -1>
//<cfset Variables.TValue13 = -1>
//<cfset Variables.TValue14 = -1>
//<cfset Variables.TValue15 = -1>
//<cfset Variables.TValue16 = -1>
//<cfset Variables.TValue17 = -1>
//<cfset Variables.TValue18 = -1>
//<cfset Variables.TValue19 = -1>
//<cfset Variables.TValue20 = -1>
//<cfset Variables.TValue21 = -1>
//<cfset Variables.TValue22 = -1>
//<cfset Variables.TValue23 = -1>
//<cfset Variables.TValue24 = -1>
//<cfset Variables.TValue25 = -1>
//<cfset Variables.TValue26 = -1>
//<cfset Variables.TValue27 = -1>
//<cfset Variables.TValue28 = -1>
//<cfset Variables.TValue29 = -1>
//<cfset Variables.TValue30 = -1>
//<cfset Variables.TValue31 = -1>
//<cfset Variables.TValue32 = -1>
//<cfset Variables.TValue33 = -1>
//<cfset Variables.TValue34 = -1>
//<cfset Variables.TValue35 = -1>
//<cfset Variables.TValue36 = -1>
//<cfset Variables.TValue37 = -1>
//<cfset Variables.TValue38 = -1>
//<cfset Variables.TValue39 = -1>
//

$TValue1 = -1;
$TValue2 = -1;
$TValue3 = -1;
$TValue4 = -1;
$TValue5 = -1;
$TValue6 = -1;
$TValue7 = -1;
$TValue8 = -1;
$TValue9 = -1;
$TValue10 = -1;
$TValue11 = -1;
$TValue12 = -1;
$TValue13 = -1;
$TValue14 = -1;
$TValue15 = -1;
$TValue16 = -1;
$TValue17 = -1;
$TValue18 = -1;
$TValue19 = -1;
$TValue20 = -1;
$TValue21 = -1;
$TValue22 = -1;
$TValue23 = -1;
$TValue24 = -1;
$TValue25 = -1;
$TValue26 = -1;
$TValue27 = -1;
$TValue28 = -1;
$TValue29 = -1;
$TValue30 = -1;
$TValue31 = -1;
$TValue32 = -1;
$TValue33 = -1;
$TValue34 = -1;
$TValue35 = -1;
$TValue36 = -1;
$TValue37 = -1;
$TValue38 = -1;
$TValue39 = -1;


//
//
//<cfset Variables.TDescription1 = ''>
//<cfset Variables.TDescription2 = ''>
//<cfset Variables.TDescription3 = ''>
//<cfset Variables.TDescription4 = ''>
//<cfset Variables.TDescription5 = ''>
//<cfset Variables.TDescription6 = ''>
//<cfset Variables.TDescription7 = ''>
//<cfset Variables.TDescription8 = ''>
//<cfset Variables.TDescription9 = ''>
//<cfset Variables.TDescription10 = ''>
//<cfset Variables.TDescription11 = ''>
//<cfset Variables.TDescription12 = ''>
//<cfset Variables.TDescription13 = ''>
//<cfset Variables.TDescription14 = ''>
//<cfset Variables.TDescription15 = ''>
//<cfset Variables.TDescription16 = ''>
//<cfset Variables.TDescription17 = ''>
//<cfset Variables.TDescription18 = ''>
//<cfset Variables.TDescription19 = ''>
//<cfset Variables.TDescription20 = ''>
//<cfset Variables.TDescription21 = ''>
//<cfset Variables.TDescription22 = ''>
//<cfset Variables.TDescription23 = ''>
//<cfset Variables.TDescription24 = ''>
//<cfset Variables.TDescription25 = ''>
//<cfset Variables.TDescription26 = ''>
//<cfset Variables.TDescription27 = ''>
//<cfset Variables.TDescription28 = ''>
//<cfset Variables.TDescription29 = ''>
//<cfset Variables.TDescription30 = ''>
//<cfset Variables.TDescription31 = ''>
//<cfset Variables.TDescription32 = ''>
//<cfset Variables.TDescription33 = ''>
//<cfset Variables.TDescription34 = ''>
//<cfset Variables.TDescription35 = ''>
//<cfset Variables.TDescription36 = ''>
//<cfset Variables.TDescription37 = ''>
//<cfset Variables.TDescription38 = ''>
//<cfset Variables.TDescription39 = ''>
//
//
//

$TDescription1 = "";
$TDescription2 = "";
$TDescription3 = "";
$TDescription4 = "";
$TDescription5 = "";
$TDescription6 = "";
$TDescription7 = "";
$TDescription8 = "";
$TDescription9 = "";
$TDescription10 = "";
$TDescription11 = "";
$TDescription12 = "";
$TDescription13 = "";
$TDescription14 = "";
$TDescription15 = "";
$TDescription16 = "";
$TDescription17 = "";
$TDescription18 = "";
$TDescription19 = "";
$TDescription20 = "";
$TDescription21 = "";
$TDescription22 = "";
$TDescription23 = "";
$TDescription24 = "";
$TDescription25 = "";
$TDescription26 = "";
$TDescription27 = "";
$TDescription28 = "";
$TDescription29 = "";
$TDescription30 = "";
$TDescription31 = "";
$TDescription32 = "";
$TDescription33 = "";
$TDescription34 = "";
$TDescription35 = "";
$TDescription36 = "";
$TDescription37 = "";
$TDescription38 = "";
$TDescription39 = "";


//
//<cfquery datasource="#Variables.EMRDataSource#" name="EncounterInfo">
//	SELECT	TOP 1 Patient_id,
//			Org_id
//	FROM	EncounterHistory
//	WHERE	Encounter_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#variables.Encounter_id#">
//</cfquery>	
//

$sql = "SELECT	TOP 1 Patient_id,
			Org_id
	FROM	EncounterHistory
	WHERE	Encounter_id = $Encounter_Id";
$EncounterInfo = $this->ReportModel->data_db->query($sql);
$EncounterInfo_num = $EncounterInfo->num_rows();
$EncounterInfo_row = $EncounterInfo->row();

//
//<!--- Query to get all patient profile information --->
//<cfquery datasource="#Variables.EMRDataSource#" name="PatientProfileInfo">
//	SELECT 	Top 1 *
//	FROM 	PatientProfile
//	WHERE	Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#EncounterInfo.Patient_id#">
//			AND Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#EncounterInfo.Org_id#">
//			AND (Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR Hidden is Null)	
//</cfquery>
//
$org_select_id = ($EncounterInfo_row->Org_id)? $EncounterInfo_row->Org_id : $current_user->Org_Id;
$sql = "	SELECT 	Top 1 *
	FROM 	PatientProfile
	WHERE	Patient_id = $EncounterInfo_row->Patient_id
				";

$PatientProfileInfo = $this->ReportModel->data_db->query($sql);
$PatientProfileInfo_num = $PatientProfileInfo->num_rows();
$PatientProfileInfo_row = $PatientProfileInfo->row();
//
//<cfif PatientProfileInfo.NoKnownProblems eq 1>
//	<cfset Variables.CheckICD9Codes = 0>
//</cfif>
//

if ($PatientProfileInfo_num && $PatientProfileInfo_row->NoKnownProblems == 1) {
  $CheckICD9Codes = 0;
}

//
//<cfquery datasource="#Variables.EMRDataSource#" name="AnyICD9Problems">
//	SELECT	PL.ICD9_ID
//	FROM	ProblemList PL
//	WHERE	PL.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Patient_id#">
//			AND PL.Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Org_id#">
//			AND (PL.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR PL.Hidden is Null)
//</cfquery>
//

$sql = "SELECT	PL.ICD9_ID
	FROM	ProblemList PL
	WHERE	PL.Patient_id = $PatientProfileInfo_row->Patient_ID 
			AND PL.Org_id = $PatientProfileInfo_row->Org_ID
			AND (PL.Hidden = 0 OR PL.Hidden is Null)";


$AnyICD9Problems = $this->ReportModel->data_db->query($sql);
$AnyICD9Problems_num = $AnyICD9Problems->num_rows();
$AnyICD9Problems_result = $AnyICD9Problems->result();


//
//
//<cfif AnyICD9Problems.RecordCount gt 0>
//	<cfset Variables.CheckICD9Codes = 1>
//</cfif>
//
if ($AnyICD9Problems_num > 0) {
  $CheckICD9Codes = 1;
}
//
//
//<cfquery datasource="#Variables.EMRDataSource#" name="SelectHistory">
//	SELECT	Top 1 
//			History_Hist_ID, 
//			HistoryMedical_Dtl_ID, 
//			HistorySurgical_Dtl_ID, 
//			HistorySocial_Dtl_ID, 
//			HistoryFamilyNotes_Dtl_ID, 
//			HistoryObGyn_Dtl_ID
//	FROM 	History
//	WHERE 	Patient_ID = <cfqueryparam cfsqltype="cf_sql_bigint"  value="#PatientProfileInfo.PATIENT_ID#">
//			AND ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">
//</cfquery>
//

$sql = "SELECT	Top 1 
			History_Hist_ID, 
			HistoryMedical_Dtl_ID, 
			HistorySurgical_Dtl_ID, 
			HistorySocial_Dtl_ID, 
			HistoryFamilyNotes_Dtl_ID, 
			HistoryObGyn_Dtl_ID
	FROM 	History
	WHERE 	Patient_ID = $PatientProfileInfo_row->Patient_ID 
			AND ORG_ID = $PatientProfileInfo_row->Org_ID ";

$SelectHistory = $this->ReportModel->data_db->query($sql);
$SelectHistory_num = $SelectHistory->num_rows();
$SelectHistory_row = $SelectHistory->row();

//
//<cfif SelectHistory.RecordCount gt 0>
//	<cfset Variables.HasNewHistoryId = 1>
//</cfif>
//
if ($SelectHistory_num > 0) {
  $HasNewHistoryId = 1;
}
//
//<cfquery datasource="#Variables.EMRDataSource#" name="FamilyRecords">
//	SELECT 	Top 1 *
//	FROM 	HistoryFamily
//	WHERE	Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.PATIENT_ID#">
//			AND ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">
//</cfquery>
//
$sql = "SELECT 	Top 1 *
   	FROM 	HistoryFamily
	  WHERE	Patient_id = $PatientProfileInfo_row->Patient_ID 
    AND ORG_ID = $PatientProfileInfo_row->Org_ID";
$FamilyRecords = $this->ReportModel->data_db->query($sql);
$FamilyRecords_num = $FamilyRecords->num_rows();
$FamilyRecords_row = $FamilyRecords->row();


//
//<cfif FamilyRecords.RecordCount gt 0>
//	<cfset Variables.HasFamilyHistory = 1>
//</cfif>

if ($FamilyRecords_num > 0) {
  $HasFamilyHistory = 1;
}

//<cfquery datasource="#Variables.EMRDataSource#" name="PatientVitalsInfo">
//	SELECT 	Top 1 *
//	FROM 	VitalsHistory
//	WHERE	Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.PATIENT_ID#">
//			AND ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">
//			AND (Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR Hidden is Null)
//</cfquery>
//
//

$sql = "SELECT 	Top 1 *
	FROM 	VitalsHistory
	WHERE	Patient_id = $PatientProfileInfo_row->Patient_ID 
			AND ORG_ID = $PatientProfileInfo_row->Org_ID 
			AND (Hidden = 0 OR Hidden is Null)";

$PatientVitalsInfo = $this->ReportModel->data_db->query($sql);
$PatientVitalsInfo_num = $PatientVitalsInfo->num_rows();
$PatientVitalsInfo_row = $PatientVitalsInfo->row();
//
//<cfif not isdefined('variables.VitalsConversionLoaded')>
//	<cfinclude template="VitalsConversion.cfm">
//</cfif>
//
if (!isset($VitalsConversionLoaded)) {
  $this->load->view('encounter/generate_report/VitalsConversion');
}

//
//<!--- # 2 - Family Hx CVD 1 degree (Heart Disease) --->
//<cfif Variables.HasFamilyHistory eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N2FamilyHistHeartDisease">
//		SELECT	COUNT(HFMA.SmartControlAnswer) AS TOTNUMBER
//		FROM	HistoryFamilyMember_Dtl HFMD,
//				HistoryFamilyMember_Answer HFMA,
//				HistoryFamily HF
//		WHERE	HF.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint"  value="#PatientProfileInfo.PATIENT_ID#">
//				AND HF.Org_id = <cfqueryparam cfsqltype="cf_sql_integer" value="#PatientProfileInfo.ORG_ID#">
//				AND HFMD.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
//				AND HFMA.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
//				AND HFMD.Relationship_HistoryDropdownMaster_ID in (<cfqueryparam cfsqltype="cf_sql_integer" list="yes" value="26,27,28,29,30,31">)
//				AND HFMA.SmartControlAnswer = <cfqueryparam cfsqltype="cf_sql_bit" value="1">
//				AND HFMA.SmartControlMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="129">
//				AND (HFMD.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR HFMD.Hidden is Null)
//		GROUP BY HFMA.SmartControlAnswer			
//	</cfquery>
//</cfif>
//
if ($HasFamilyHistory == 1) {
  $sql = "	SELECT	COUNT(HFMA.SmartControlAnswer) AS TOTNUMBER
		FROM	HistoryFamilyMember_Dtl HFMD,
				HistoryFamilyMember_Answer HFMA,
				HistoryFamily HF
		WHERE	HF.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND HF.Org_id = $PatientProfileInfo_row->Org_ID
				AND HFMD.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
				AND HFMA.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
				AND HFMD.Relationship_HistoryDropdownMaster_ID in (26,27,28,29,30,31)
				AND HFMA.SmartControlAnswer = 1
				AND HFMA.SmartControlMaster_ID = 129
				AND (HFMD.Hidden = 0 OR HFMD.Hidden is Null)
     		GROUP BY HFMA.SmartControlAnswer			";

  $N2FamilyHistHeartDisease = $this->ReportModel->data_db->query($sql);
  $N2FamilyHistHeartDisease_num = $N2FamilyHistHeartDisease->num_rows();
  $N2FamilyHistHeartDisease_result = $N2FamilyHistHeartDisease->result();
}
//
//<!--- # 3 - History of LVH ICD9 --->
//<cfif Variables.CheckICD9Codes eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N3HistoryOfLVHICD9">
//		SELECT	I9.ICD9Code
//		FROM	ICD9Master I9,
//				ProblemList PL
//		WHERE	PL.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Patient_id#">
//				AND PL.Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Org_id#">
//				AND (PL.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR PL.Hidden is Null)
//				AND PL.ICD9_ID = I9.ICD9_ID
//				AND (
//						(I9.ICD9Code LIKE '426.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="426">) OR
//						(I9.ICD9Code LIKE '427.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="427">)
//					)	
//	</cfquery>
//</cfif>
//

if ($CheckICD9Codes == 1) {
  $sql = "SELECT	I9.ICD9Code
		FROM	ICD9Master I9,
				ProblemList PL
		WHERE	PL.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND PL.Org_id = $PatientProfileInfo_row->Org_ID 
				AND (PL.Hidden = 0 OR PL.Hidden is Null)
				AND PL.ICD9_ID = I9.ICD9_ID
				AND (
						(I9.ICD9Code LIKE '426.%') OR (I9.ICD9Code = '426') OR
						(I9.ICD9Code LIKE '427.%') OR (I9.ICD9Code = '427')
					)	";
  $N3HistoryOfLVHICD9 = $this->ReportModel->data_db->query($sql);
  $N3HistoryOfLVHICD9_num = $N3HistoryOfLVHICD9->num_rows();
  $N3HistoryOfLVHICD9_result = $N3HistoryOfLVHICD9->result();
}


//
//<!--- #4 - HbA1C --->
//<cfquery datasource="#Variables.EMRDataSource#" name="N4HbA1C">
//	SELECT	RD.ResultsTestName,
//			RD.ResultsTestResults_num
//	FROM	ResultsHistory RH,
//			ResultsDetails RD
//	WHERE	RH.Patient_ID = <cfqueryparam cfsqltype="cf_sql_bigint"  value="#PatientProfileInfo.PATIENT_ID#">
//			AND RH.ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">	
//			AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
//			AND RD.ResultsTestName LIKE 'HbA1C'
//			AND RD.ResultsTestResults_num <> <cfqueryparam cfsqltype="cf_sql_integer" value="0"> 
//			AND (datediff(d,RH.DateReceived,<cfqueryparam cfsqltype="cf_sql_timestamp" value="#CreateODBCDate(Now())#">) < <cfqueryparam cfsqltype="cf_sql_integer" value="365">)
//			AND RD.ResultsTestResults_num IS NOT NULL
//</cfquery>	
//
//

$sql = "	SELECT	RD.ResultsTestName,
			RD.ResultsTestResults_num
	FROM	ResultsHistory RH,
			ResultsDetails RD
	WHERE	RH.Patient_ID = $PatientProfileInfo_row->Patient_ID 
			AND RH.ORG_ID = $PatientProfileInfo_row->Org_ID 	
			AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
			AND RD.ResultsTestName LIKE 'HbA1C'
			AND RD.ResultsTestResults_num <> 0
			AND (datediff(d,RH.DateReceived, " . date('Y-m-d') . ") < 365)
			AND RD.ResultsTestResults_num IS NOT NULL";

$N4HbA1C = $this->ReportModel->data_db->query($sql);
$N4HbA1C_num = $N4HbA1C->num_rows();
$N4HbA1C_row = $N4HbA1C->row();
$N4HbA1C_result = $N4HbA1C->result();
//
//<!--- #5 - BMI --->
//<cfset Variables.BMI = DisplayBMI(PatientVitalsInfo.Height_cm, PatientVitalsInfo.Weight_Kg, '-1')>
//
$BMI = 0; //  DisplayBMI(PatientVitalsInfo.Height_cm, PatientVitalsInfo.Weight_Kg, '-1')>
//
//<!--- #6 - Family History of Diabetes  --->
//<cfif Variables.HasFamilyHistory eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N6FamilyHistDiabetes">
//		SELECT	COUNT(HFMA.SmartControlAnswer) AS TOTNUMBER
//		FROM	HistoryFamilyMember_Dtl HFMD,
//				HistoryFamilyMember_Answer HFMA,
//				HistoryFamily HF
//		WHERE	HF.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint"  value="#PatientProfileInfo.PATIENT_ID#">
//				AND HF.Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">
//				AND HFMD.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
//				AND HFMA.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
//				AND HFMD.Relationship_HistoryDropdownMaster_ID in (<cfqueryparam cfsqltype="cf_sql_integer" list="yes" value="21,22,23,24,25,26,27,28,29">)
//				AND HFMA.SmartControlAnswer = <cfqueryparam cfsqltype="cf_sql_bit" value="1">
//				AND HFMA.SmartControlMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="124">
//				AND (HFMD.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR HFMD.Hidden is Null)
//		GROUP BY HFMA.SmartControlAnswer			
//	</cfquery>
//</cfif>
//

if ($HasFamilyHistory == 1) {
  $sql = "SELECT	COUNT(HFMA.SmartControlAnswer) AS TOTNUMBER
		FROM	HistoryFamilyMember_Dtl HFMD,
				HistoryFamilyMember_Answer HFMA,
				HistoryFamily HF
		WHERE	HF.Patient_id = $PatientProfileInfo_row->Patient_ID 
				AND HF.Org_id = $PatientProfileInfo_row->Org_ID 
				AND HFMD.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
				AND HFMA.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
				AND HFMD.Relationship_HistoryDropdownMaster_ID in (21,22,23,24,25,26,27,28,29)
				AND HFMA.SmartControlAnswer = 1
				AND HFMA.SmartControlMaster_ID = 124
				AND (HFMD.Hidden = 0 OR HFMD.Hidden is Null)
		GROUP BY HFMA.SmartControlAnswer		";

  $N6FamilyHistDiabetes = $this->ReportModel->data_db->query($sql);
  $N6FamilyHistDiabetes_num = $N6FamilyHistDiabetes->num_rows();
  $N6FamilyHistDiabetes_row = $N6FamilyHistDiabetes->row();
}

//
//<!--- #7 - Smoking Status  --->
//<cfif Variables.HasNewHistoryId eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N7SmokingStatus">
//		SELECT	HSD.TobaccoUse_HistoryDropdownMaster_ID,
//				HDM.DisplayName,
//				HSD.PacksPerDay
//		FROM	HistorySocial_Dtl HSD,
//				HistoryDropdownMaster HDM
//		WHERE	HSD.HistorySocial_Dtl_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#SelectHistory.HistorySocial_Dtl_ID#">
//				AND HSD.TobaccoUse_HistoryDropdownMaster_ID = HDM.HistoryDropdownMaster_ID 
//				AND HSD.TobaccoUse_HistoryDropdownMaster_ID IN (<cfqueryparam cfsqltype="cf_sql_integer" list="yes" value="56,57,58,59,60,61,63">)
//	</cfquery>
//</cfif>
//

if ($HasNewHistoryId = 1 && $SelectHistory_num) {
  $sql = "	SELECT	HSD.TobaccoUse_HistoryDropdownMaster_ID,
				HDM.DisplayName,
				HSD.PacksPerDay
	FROM	HistorySocial_Dtl HSD,
				HistoryDropdownMaster HDM
		WHERE	HSD.HistorySocial_Dtl_ID = $SelectHistory_row->HistorySocial_Dtl_ID
				AND HSD.TobaccoUse_HistoryDropdownMaster_ID = HDM.HistoryDropdownMaster_ID 
			AND HSD.TobaccoUse_HistoryDropdownMaster_ID IN (56,57,58,59,60,61,63)";

  $N7SmokingStatus = $this->ReportModel->data_db->query($sql);
  $N7SmokingStatus_num = $N7SmokingStatus->num_rows();
  $N7SmokingStatus_row = $N7SmokingStatus->row();
}

//
//<!--- #8 - Alcohol Intake  --->
//<cfif Variables.HasNewHistoryId eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N8AlcoholIntake">
//		SELECT	HSD.AlcoholHistory_HistoryDropdownMaster_ID,
//				HDM.DisplayName,
//				ISNULL(HSD.NumberDrinks,0) AS NumberOfDrinks
//		FROM	HistorySocial_Dtl HSD,
//				HistoryDropdownMaster HDM
//		WHERE	HSD.HistorySocial_Dtl_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#SelectHistory.HistorySocial_Dtl_ID#">
//				AND HSD.AlcoholHistory_HistoryDropdownMaster_ID = HDM.HistoryDropdownMaster_ID 
//				AND HSD.AlcoholHistory_HistoryDropdownMaster_ID IN (<cfqueryparam cfsqltype="cf_sql_integer" list="yes" value="15,16,17">)
//	</cfquery>
//
//	<cfquery datasource="#Variables.EMRDataSource#" name="N8DrinksPerTime">
//		SELECT	HSD.DrinkUnits_HistoryDropdownMaster_ID,
//				HDM.DisplayName
//		FROM	HistorySocial_Dtl HSD,
//				HistoryDropdownMaster HDM
//		WHERE	HSD.HistorySocial_Dtl_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#SelectHistory.HistorySocial_Dtl_ID#">
//				AND HSD.DrinkUnits_HistoryDropdownMaster_ID = HDM.HistoryDropdownMaster_ID 
//	</cfquery>
//</cfif>
//
if ($HasNewHistoryId == 1 && $SelectHistory_num) {
  $sql = "		SELECT	HSD.AlcoholHistory_HistoryDropdownMaster_ID,
				HDM.DisplayName,
				ISNULL(HSD.NumberDrinks,0) AS NumberOfDrinks
		FROM	HistorySocial_Dtl HSD,
				HistoryDropdownMaster HDM
		WHERE	HSD.HistorySocial_Dtl_ID = $SelectHistory_row->HistorySocial_Dtl_ID
				AND HSD.AlcoholHistory_HistoryDropdownMaster_ID = HDM.HistoryDropdownMaster_ID 
				AND HSD.AlcoholHistory_HistoryDropdownMaster_ID IN ( 15,16,17)
	  ";

  $N8AlcoholIntake = $this->ReportModel->data_db->query($sql);
  $N8AlcoholIntake_num = $N8AlcoholIntake->num_rows();
  $N8AlcoholIntake_result = $N8AlcoholIntake->result();
  $N8AlcoholIntake_row = $N8AlcoholIntake->row();


  $sql = " 
		SELECT	HSD.DrinkUnits_HistoryDropdownMaster_ID,
				HDM.DisplayName
		FROM	HistorySocial_Dtl HSD,
				HistoryDropdownMaster HDM
		WHERE	HSD.HistorySocial_Dtl_ID = $SelectHistory_row->HistorySocial_Dtl_ID
				AND HSD.DrinkUnits_HistoryDropdownMaster_ID = HDM.HistoryDropdownMaster_ID ";

  $N8DrinksPerTime = $this->ReportModel->data_db->query($sql);
  $N8DrinksPerTime_num = $N8DrinksPerTime->num_rows();
  $N8DrinksPerTime_result = $N8DrinksPerTime->result();
  $N8DrinksPerTime_row = $N8DrinksPerTime->row();
}
//
//
//<!--- #11 - Blood Pressure Systolic --->
//<cfset Variables.Systolic = PatientVitalsInfo.Systolic_mmHg>
//
//
$Systolic = ($PatientVitalsInfo_row) ? $PatientVitalsInfo_row->Systolic_mmHg : 0;
//
//<!--- #12 - Blood Pressure Diastolic --->
//<cfset Variables.Diastolic = PatientVitalsInfo.Diastolic_mmHg>
//
$Diastolic = ($PatientVitalsInfo_row) ? $PatientVitalsInfo_row->Diastolic_mmHg : 0;
//
//<!--- # 13 - Total Cholesterol --->
//<cfquery datasource="#Variables.EMRDataSource#" name="N13TotChol">
//	SELECT		Top 1 
//				RD.ResultsTestName,
//				RD.ResultsTestResults_num,
//				RD.ResultsUnits,
//				RH.DateReceived
//	FROM		ResultsHistory RH,
//				ResultsDetails RD
//	WHERE		RH.Patient_ID = <cfqueryparam cfsqltype="cf_sql_bigint"  value="#PatientProfileInfo.PATIENT_ID#">
//				AND RH.ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">	
//				AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
//				AND (ResultsTestName LIKE '%cholesterol%') AND (ResultsTestName LIKE '%total%')
//				AND RD.ResultsTestResults_num <> <cfqueryparam cfsqltype="cf_sql_integer" value="0">
//				AND (datediff(d,RH.DateReceived,<cfqueryparam cfsqltype="cf_sql_timestamp" value="#CreateODBCDate(Now())#">) < <cfqueryparam cfsqltype="cf_sql_integer" value="365">)
//				AND RD.ResultsTestResults_num IS NOT NULL
//	ORDER BY	RH.DateReceived desc			
//</cfquery>
//

$sql = "	SELECT		Top 1 
				RD.ResultsTestName,
				RD.ResultsTestResults_num,
				RD.ResultsUnits,
				RH.DateReceived
	FROM		ResultsHistory RH,
				ResultsDetails RD
	WHERE		RH.Patient_ID = $PatientProfileInfo_row->Patient_ID
				AND RH.ORG_ID = $PatientProfileInfo_row->Org_ID	
				AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
				AND (ResultsTestName LIKE '%cholesterol%') AND (ResultsTestName LIKE '%total%')
				AND RD.ResultsTestResults_num <> 0 
				AND (datediff(d,RH.DateReceived, " . date('Y-m-d') . ") < 365)
				AND RD.ResultsTestResults_num IS NOT NULL
	ORDER BY	RH.DateReceived desc		";

$N13TotChol = $this->ReportModel->data_db->query($sql);
$N13TotChol_num = $N13TotChol->num_rows();
$N13TotChol_row = $N13TotChol->row();

//
//<!--- #14 - LDL --->
//<cfquery datasource="#Variables.EMRDataSource#" name="N14LDL">
//	SELECT		Top 1 
//				RD.ResultsTestName,
//				RD.ResultsTestResults_num,
//				RD.ResultsUnits,
//				RH.DateReceived
//	FROM		ResultsHistory RH,
//				ResultsDetails RD
//	WHERE		RH.Patient_ID = <cfqueryparam cfsqltype="cf_sql_bigint"  value="#PatientProfileInfo.PATIENT_ID#">
//				AND RH.ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">	
//				AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
//				AND ((RD.ResultsTestName LIKE 'ldl_C%')OR (RD.ResultsTestName LIKE 'ldl_(C%'))
//				AND RD.ResultsTestResults_num <> <cfqueryparam cfsqltype="cf_sql_integer" value="0">
//				AND (datediff(d,RH.DateReceived,<cfqueryparam cfsqltype="cf_sql_timestamp" value="#CreateODBCDate(Now())#">) < <cfqueryparam cfsqltype="cf_sql_integer" value="365">)
//				AND RD.ResultsTestResults_num IS NOT NULL
//	ORDER BY	RH.DateReceived desc			
//</cfquery>
//
$sql = "	SELECT		Top 1 
				RD.ResultsTestName,
				RD.ResultsTestResults_num,
				RD.ResultsUnits,
				RH.DateReceived
	FROM		ResultsHistory RH,
				ResultsDetails RD
	WHERE		RH.Patient_ID = $PatientProfileInfo_row->Patient_ID
				AND RH.ORG_ID = $PatientProfileInfo_row->Org_ID	
			AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
				AND ((RD.ResultsTestName LIKE 'ldl_C%')OR (RD.ResultsTestName LIKE 'ldl_(C%'))
				AND RD.ResultsTestResults_num <> 0
				AND (datediff(d,RH.DateReceived," . date('Y-m-d') . ") < 365)
				AND RD.ResultsTestResults_num IS NOT NULL
	ORDER BY	RH.DateReceived desc	";


$N14LDL = $this->ReportModel->data_db->query($sql);
$N14LDL_num = $N14LDL->num_rows();
$N14LDL_row = $N14LDL->row();

//
//<!--- #15 - HDL --->
//<cfquery datasource="#Variables.EMRDataSource#" name="N15HDL">
//	SELECT		Top 1 
//				RD.ResultsTestName,
//				RD.ResultsTestResults_num,
//				RD.ResultsUnits,
//				RH.DateReceived
//	FROM		ResultsHistory RH,
//				ResultsDetails RD
//	WHERE		RH.Patient_ID = <cfqueryparam cfsqltype="cf_sql_bigint"  value="#PatientProfileInfo.PATIENT_ID#">
//				AND RH.ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">	
//				AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
//				AND ((RD.ResultsTestName LIKE 'hdl_Ch%') OR (RD.ResultsTestName LIKE 'hdl'))
//				AND RD.ResultsTestResults_num <> <cfqueryparam cfsqltype="cf_sql_integer" value="0">
//				AND (datediff(d,RH.DateReceived,<cfqueryparam cfsqltype="cf_sql_timestamp" value="#CreateODBCDate(Now())#">) < <cfqueryparam cfsqltype="cf_sql_integer" value="365">)
//				AND RD.ResultsTestResults_num IS NOT NULL
//	ORDER BY	RH.DateReceived desc			
//</cfquery>
//


$sql = "	SELECT		Top 1 
				RD.ResultsTestName,
				RD.ResultsTestResults_num,
				RD.ResultsUnits,
				RH.DateReceived
	FROM		ResultsHistory RH,
				ResultsDetails RD
	WHERE		RH.Patient_ID = $PatientProfileInfo_row->Patient_ID
				AND RH.ORG_ID = $PatientProfileInfo_row->Org_ID		
				AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
				AND ((RD.ResultsTestName LIKE 'hdl_Ch%') OR (RD.ResultsTestName LIKE 'hdl'))
				AND RD.ResultsTestResults_num <> 0
				AND (datediff(d,RH.DateReceived," . date('Y-m-d') . ") < 365)
				AND RD.ResultsTestResults_num IS NOT NULL
	ORDER BY	RH.DateReceived desc	";

$N15HDL = $this->ReportModel->data_db->query($sql);
$N15HDL_num = $N15HDL->num_rows();
$N15HDL_row = $N15HDL->row();
//
//<!--- #16 - Triglycerides--->
//<cfquery datasource="#Variables.EMRDataSource#" name="N16Triglycerides">
//	SELECT		Top 1 
//				RD.ResultsTestName,
//				RD.ResultsTestResults_num,
//				RH.DateReceived
//	FROM		ResultsHistory RH,
//				ResultsDetails RD
//	WHERE		RH.Patient_ID = <cfqueryparam cfsqltype="cf_sql_bigint"  value="#PatientProfileInfo.PATIENT_ID#">
//				AND RH.ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">	
//				AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
//				AND RD.ResultsTestName LIKE '%Triglycerides'
//				AND (RD.ResultsTestResults_num <> <cfqueryparam cfsqltype="cf_sql_integer" value="0">)
//				AND RD.ResultsTestResults_num IS NOT NULL				
//				AND (datediff(d,RH.DateReceived,<cfqueryparam cfsqltype="cf_sql_timestamp" value="#CreateODBCDate(Now())#">) <= <cfqueryparam cfsqltype="cf_sql_integer" value="365">)
//	ORDER BY	RH.DateReceived DESC
//</cfquery>			
//

$sql = "	SELECT		Top 1 
				RD.ResultsTestName,
				RD.ResultsTestResults_num,
				RH.DateReceived
	FROM		ResultsHistory RH,
				ResultsDetails RD
	WHERE		RH.Patient_ID = $PatientProfileInfo_row->Patient_ID
				AND RH.ORG_ID = $PatientProfileInfo_row->Org_ID	
				AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
				AND RD.ResultsTestName LIKE '%Triglycerides'
				AND (RD.ResultsTestResults_num <> 0)
				AND RD.ResultsTestResults_num IS NOT NULL				
				AND (datediff(d,RH.DateReceived," . date('Y-m-d') . ") <= 365)
	ORDER BY	RH.DateReceived DESC";

$N16Triglycerides = $this->ReportModel->data_db->query($sql);
$N16Triglycerides_num = $N16Triglycerides->num_rows();
$N16Triglycerides_row = $N16Triglycerides->row();

//
//
//<!--- #17 - Diabetes ICD9 --->
//<cfif Variables.CheckICD9Codes eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N17DiabeticICD9">
//		SELECT	I9.ICD9Code
//		FROM	ICD9Master I9,
//				ProblemList PL
//		WHERE	PL.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Patient_id#">
//				AND PL.Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Org_id#">
//				AND (PL.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR PL.Hidden is Null)
//				AND PL.ICD9_ID = I9.ICD9_ID
//				AND (
//						(I9.ICD9Code LIKE '250.%')	OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar" value="250">)
//					)	
//	</cfquery>
//</cfif>
//

if ($CheckICD9Codes == 1) {
  $sql = "		SELECT	I9.ICD9Code
		FROM	ICD9Master I9,
				ProblemList PL
		WHERE	PL.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND PL.Org_id = $PatientProfileInfo_row->Org_ID	
				AND (PL.Hidden = 0 OR PL.Hidden is Null)
				AND PL.ICD9_ID = I9.ICD9_ID
				AND (
						(I9.ICD9Code LIKE '250.%')	OR (I9.ICD9Code = '250')
					)	";
  $N17DiabeticICD9 = $this->ReportModel->data_db->query($sql);
  $N17DiabeticICD9_num = $N17DiabeticICD9->num_rows();
  $N17DiabeticICD9_result = $N17DiabeticICD9->result();
}

//
//
//<!--- # 18 - Socioeconomic Status --->
//
//
//<!--- # 19 - Oral Contraceptives --->
//<!--- Hidden per email from Pete on 17 July 2011 
//<cfif (Variables.HasNewHistoryId eq 1) AND (PatientProfileInfo.Sex eq 'F')>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N19OralContraceptives">
//		SELECT	HSD.Pill
//		FROM	HistorySocial_Dtl HSD
//		WHERE	HSD.HistorySocial_Dtl_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#SelectHistory.HistorySocial_Dtl_ID#">
//	</cfquery>
//</cfif>
//--->
//
//<!--- #20 - HRT ICD9 --->
//<!--- Hidden per email from Pete on 17 July 2011 
//<cfif Variables.CheckICD9Codes eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N20HRTICD9">
//		SELECT	I9.ICD9Code
//		FROM	ICD9Master I9,
//				ProblemList PL
//		WHERE	PL.Patient_id = <cfqueryparam cfsqltype="cf_sql_integer" value="#PatientProfileInfo.Patient_id#">
//				AND PL.Org_id = <cfqueryparam cfsqltype="cf_sql_integer" value="#PatientProfileInfo.Org_id#">
//				AND (PL.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR PL.Hidden is Null)
//				AND PL.ICD9_ID = I9.ICD9_ID
//				AND (
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="S5022">) OR
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="86277">) OR
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="84443">) OR
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="83003">) OR						
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="83002">) OR
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="82024">) OR
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="80430">) OR
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="80428">) 						
//						
//						
//					)	
//	</cfquery>
//</cfif>
//--->
//
//<!--- # 21 - Lipoprotein (a) --->
//<cfquery datasource="#Variables.EMRDataSource#" name="N21Lipoprotein">
//	SELECT		Top 1 
//				RD.ResultsTestName,
//				RD.ResultsTestResults_num,
//				RD.ResultsUnits,
//				RH.DateReceived
//	FROM		ResultsHistory RH,
//				ResultsDetails RD
//	WHERE		RH.Patient_ID = <cfqueryparam cfsqltype="cf_sql_bigint"  value="#PatientProfileInfo.PATIENT_ID#">
//				AND RH.ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">	
//				AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
//				AND ((RD.ResultsTestName LIKE 'lipoprotein_a%') OR (RD.ResultsTestName LIKE 'lipoprotein_(a%')) 
//				AND RD.ResultsTestResults_num <> <cfqueryparam cfsqltype="cf_sql_integer" value="0">
//				AND LTRIM(RTRIM(RD.ResultsUnits)) = <cfqueryparam cfsqltype="cf_sql_varchar" value="mg/dl">
//				AND (datediff(d,RH.DateReceived,<cfqueryparam cfsqltype="cf_sql_timestamp" value="#CreateODBCDate(Now())#">) < <cfqueryparam cfsqltype="cf_sql_integer" value="365">)
//				AND RD.ResultsTestResults_num IS NOT NULL
//	ORDER BY	RH.DateReceived desc			
//</cfquery>
//

$sql = "	SELECT		Top 1 
				RD.ResultsTestName,
				RD.ResultsTestResults_num,
				RD.ResultsUnits,
				RH.DateReceived
	FROM		ResultsHistory RH,
				ResultsDetails RD
	WHERE		RH.Patient_ID = $PatientProfileInfo_row->Patient_ID
				AND RH.ORG_ID = $PatientProfileInfo_row->Org_ID	
				AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
				AND ((RD.ResultsTestName LIKE 'lipoprotein_a%') OR (RD.ResultsTestName LIKE 'lipoprotein_(a%')) 
				AND RD.ResultsTestResults_num <> 0
				AND LTRIM(RTRIM(RD.ResultsUnits)) = 'mg/dl'
				AND (datediff(d,RH.DateReceived," . date('Y-m-d') . ") < 365)
				AND RD.ResultsTestResults_num IS NOT NULL
	ORDER BY	RH.DateReceived desc			";

$N21Lipoprotein = $this->ReportModel->data_db->query($sql);
$N21Lipoprotein_num = $N21Lipoprotein->num_rows();
$N21Lipoprotein_row = $N21Lipoprotein->row();

//
//
//<!--- # 23 - Previous Heart Attack --->
//<cfif Variables.CheckICD9Codes eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N23PreviousHeartAttackICD9">
//		SELECT	I9.ICD9Code
//		FROM	ICD9Master I9,
//				ProblemList PL
//		WHERE	PL.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Patient_id#">
//				AND PL.Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Org_id#">
//				AND (PL.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR PL.Hidden is Null)
//				AND PL.ICD9_ID = I9.ICD9_ID
//				AND (
//						(I9.ICD9Code LIKE '410.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="410">) OR
//						(I9.ICD9Code LIKE '411.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="411">) OR
//						(I9.ICD9Code LIKE '412.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="412">) OR
//						(I9.ICD9Code LIKE '414.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="414">)
//					)	
//	</cfquery>
//</cfif>
//

if ($CheckICD9Codes == 1) {
  $sql = "		SELECT	I9.ICD9Code
		FROM	ICD9Master I9,
				ProblemList PL
		WHERE	PL.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND PL.Org_id = $PatientProfileInfo_row->Org_ID
				AND (PL.Hidden = 0 OR PL.Hidden is Null)
				AND PL.ICD9_ID = I9.ICD9_ID
				AND (
						(I9.ICD9Code LIKE '410.%') OR (I9.ICD9Code = '410') OR
						(I9.ICD9Code LIKE '411.%') OR (I9.ICD9Code = '411') OR
						(I9.ICD9Code LIKE '412.%') OR (I9.ICD9Code = '412') OR
						(I9.ICD9Code LIKE '414.%') OR (I9.ICD9Code = '414')
					)";
  $N23PreviousHeartAttackICD9 = $this->ReportModel->data_db->query($sql);
  $N23PreviousHeartAttackICD9_num = $N23PreviousHeartAttackICD9->num_rows();
  $N23PreviousHeartAttackICD9_result = $N23PreviousHeartAttackICD9->result();
}

//
//
//<!--- # 24 - Previous Stroke --->
//<cfif Variables.HasNewHistoryId eq 1>	
//	<cfquery datasource="#Variables.EMRDataSource#" name="N24HistOfStroke">
//		SELECT	HMA.SmartControlAnswer
//		FROM	HistoryMedical_Answer HMA
//		WHERE	HMA.HistoryMedical_Dtl_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#SelectHistory.HistoryMedical_Dtl_ID#">
//				AND HMA.SmartControlMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="68">
//				AND HMA.ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">
//	</cfquery>
//</cfif>
//

if ($HasNewHistoryId == 1 && $SelectHistory_num) {
  $sql = "SELECT	HMA.SmartControlAnswer
		FROM	HistoryMedical_Answer HMA
		WHERE	HMA.HistoryMedical_Dtl_ID = $SelectHistory_row->HistoryMedical_Dtl_ID
				AND HMA.SmartControlMaster_ID = 68
				AND HMA.ORG_ID = $PatientProfileInfo_row->Org_ID";
  $N24HistOfStroke = $this->ReportModel->data_db->query($sql);
  $N24HistOfStroke_num = $N24HistOfStroke->num_rows();
  $N24HistOfStroke_result = $N24HistOfStroke->result();
  $N24HistOfStroke_row = $N24HistOfStroke->row();
}

//
//<!--- # 25 - History of Afib --->
//<cfif Variables.CheckICD9Codes eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N25HistoryOfAfibICD9">
//		SELECT	I9.ICD9Code
//		FROM	ICD9Master I9,
//				ProblemList PL
//		WHERE	PL.Patient_id = <cfqueryparam cfsqltype="cf_sql_integer" value="#PatientProfileInfo.Patient_id#">
//				AND PL.Org_id = <cfqueryparam cfsqltype="cf_sql_integer" value="#PatientProfileInfo.Org_id#">
//				AND (PL.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR PL.Hidden is Null)
//				AND PL.ICD9_ID = I9.ICD9_ID
//				AND (
//						<!---Check for CF --->
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="398.91">) OR
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="428.0">) OR
//						<!---Check for 'Atrial Fibrillation' --->
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="427.3">) OR
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="427.31">) OR
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="427.32">) OR						
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="427.81">)
//					)	
//	</cfquery>
//</cfif>
//

if ($CheckICD9Codes == 1) {
  $sql = "SELECT	I9.ICD9Code
		FROM	ICD9Master I9,
				ProblemList PL
		WHERE	PL.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND PL.Org_id = $PatientProfileInfo_row->Org_ID
				AND (PL.Hidden = 0 OR PL.Hidden is Null)
				AND PL.ICD9_ID = I9.ICD9_ID
				AND (
						
						(I9.ICD9Code = '398.91') OR
						(I9.ICD9Code = '428.0') OR
						
						(I9.ICD9Code = '427.3') OR
						(I9.ICD9Code = '427.31') OR
						(I9.ICD9Code = '427.32') OR						
						(I9.ICD9Code = '427.81')
					)	";
  $N25HistoryOfAfibICD9 = $this->ReportModel->data_db->query($sql);
  $N25HistoryOfAfibICD9_num = $N25HistoryOfAfibICD9->num_rows();
  $N25HistoryOfAfibICD9_result = $N25HistoryOfAfibICD9->result();
  $N25HistoryOfAfibICD9_row = $N25HistoryOfAfibICD9->result();
}

//
//
//<cfif Variables.HasNewHistoryId eq 1>	
//	<cfquery datasource="#Variables.EMRDataSource#" name="N25HistOfCHD">
//		SELECT	HMA.SmartControlAnswer
//		FROM	HistoryMedical_Answer HMA
//		WHERE	HMA.HistoryMedical_Dtl_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="#SelectHistory.HistoryMedical_Dtl_ID#">
//				AND HMA.SmartControlMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="32">
//				AND HMA.ORG_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="#PatientProfileInfo.ORG_ID#">
//	</cfquery>
//</cfif>
//


if ($HasNewHistoryId == 1 && $SelectHistory_num) {
  $sql = "		SELECT	HMA.SmartControlAnswer
		FROM	HistoryMedical_Answer HMA
		WHERE	HMA.HistoryMedical_Dtl_ID = $SelectHistory_row->HistoryMedical_Dtl_ID
				AND HMA.SmartControlMaster_ID =  32
				AND HMA.ORG_ID = $PatientProfileInfo_row->Org_ID";
  $N25HistOfCHD = $this->ReportModel->data_db->query($sql);
  $N25HistOfCHD_num = $N25HistOfCHD->num_rows();
  $N25HistOfCHD_result = $N25HistOfCHD->result();
}

//
//<!--- #26 - Waist Size  --->
//<cfif PatientVitalsInfo.Waist_cm gt 0>
//	<cfset Variables.WaistInch = cmORinch(PatientVitalsInfo.Waist_cm, 0)>
//</cfif>
//

if ($PatientVitalsInfo_row && $PatientVitalsInfo_row->Waist_cm > 0) {
  $WaistInch = $PatientVitalsInfo_row->Waist_cm;
}


//
//<!--- # 27 - PSA --->
//<!--- Hidden per email from Pete on 17 July 2011 
//<cfquery datasource="#Variables.EMRDataSource#" name="N27PSA">
//	SELECT		Top 1 
//				RD.ResultsTestName,
//				RD.ResultsTestResults_num,
//				RD.ResultsUnits,
//				RH.DateReceived
//	FROM		ResultsHistory RH,
//				ResultsDetails RD
//	WHERE		RH.Patient_ID = <cfqueryparam cfsqltype="cf_sql_bigint"  value="#PatientProfileInfo.PATIENT_ID#">
//				AND RH.ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">	
//				AND RD.ResultsHistory_ID = RH.ResultsHistory_ID
//				AND (RD.ResultsTestName LIKE 'psa%') 
//				AND (RD.ResultsTestName NOT LIKE 'psa_(LC)') 
//				AND (RD.ResultsTestName NOT LIKE 'psa,_Ultrasensitive') 
//				AND (RD.ResultsTestName NOT LIKE 'PSA,TOTAL,_SERUM')
//				AND RD.ResultsTestResults_num <> <cfqueryparam cfsqltype="cf_sql_integer" value="0">
//				AND LTRIM(RTRIM(RD.ResultsUnits)) = <cfqueryparam cfsqltype="cf_sql_varchar" value="ng/ml">
//				AND (datediff(d,RH.DateReceived,<cfqueryparam cfsqltype="cf_sql_timestamp" value="#CreateODBCDate(Now())#">) < <cfqueryparam cfsqltype="cf_sql_integer" value="365">)
//				AND RD.ResultsTestResults_num IS NOT NULL
//	ORDER BY	RH.DateReceived desc			
//</cfquery>
//
//--->
//
//<!--- # 28 - Vasectomy --->
//<cfif Variables.HasNewHistoryId eq 1>	
//	<cfquery datasource="#Variables.EMRDataSource#" name="N28Vasectomy">
//		SELECT	HSA.SmartControlAnswer
//		FROM	HistorySurgical_Answer HSA
//		WHERE	HSA.HistorySurgical_Dtl_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#SelectHistory.HistorySurgical_Dtl_ID#">
//				AND HSA.SmartControlMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="109">
//				AND HSA.ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">
//	</cfquery>
//</cfif>
//

if ($HasNewHistoryId == 1 && $SelectHistory_num) {
  $sql = "		SELECT	HSA.SmartControlAnswer
		FROM	HistorySurgical_Answer HSA
		WHERE	HSA.HistorySurgical_Dtl_ID = $SelectHistory_row->HistorySurgical_Dtl_ID
				AND HSA.SmartControlMaster_ID = 109
				AND HSA.ORG_ID = $PatientProfileInfo_row->Org_ID";
  $N28Vasectomy = $this->ReportModel->data_db->query($sql);
  $N28Vasectomy_num = $N28Vasectomy->num_rows();
  $N28Vasectomy_result = $N28Vasectomy->result();
  $N28Vasectomy_row = $N28Vasectomy->row();
}

//
//<!--- # 29 - History of STD --->
//<cfif Variables.CheckICD9Codes eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N29HistoryOfSTDICD9">
//		SELECT	I9.ICD9Code
//		FROM	ICD9Master I9,
//				ProblemList PL
//		WHERE	PL.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Patient_id#">
//				AND PL.Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Org_id#">
//				AND (PL.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR PL.Hidden is Null)
//				AND PL.ICD9_ID = I9.ICD9_ID
//				AND (
//						(I9.ICD9Code LIKE '597.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="597">) OR
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar" value="V65.45">) 
//					)	
//	</cfquery>
//</cfif>
//

if ($CheckICD9Codes == 1) {
  $sql = "		SELECT	I9.ICD9Code
		FROM	ICD9Master I9,
				ProblemList PL
		WHERE	PL.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND PL.Org_id = $PatientProfileInfo_row->Org_ID
				AND (PL.Hidden = 0 OR PL.Hidden is Null)
				AND PL.ICD9_ID = I9.ICD9_ID
				AND (
						(I9.ICD9Code LIKE '597.%') OR (I9.ICD9Code = '597') OR
						(I9.ICD9Code = 'V65.45') 
					)";
  $N29HistoryOfSTDICD9 = $this->ReportModel->data_db->query($sql);
  $N29HistoryOfSTDICD9_num = $N29HistoryOfSTDICD9->num_rows();
  $N29HistoryOfSTDICD9_result = $N29HistoryOfSTDICD9->result();
}

//
//<!--- # 30 - Age --->
//<cfif Trim(PatientProfileInfo.DOB) neq ''>
//	<cfset Variables.Age = DateDiff("yyyy",PatientProfileInfo.DOB,Now())>
//</cfif>
//

if ($PatientProfileInfo_row && $PatientProfileInfo_row->DOB != "") {
  $Age = dob_to_age($PatientProfileInfo_row->DOB);
}

//
//<!--- #31 - Family History of Colorectal Cancer--->
//<cfif Variables.HasFamilyHistory eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N31FamilyHistColorectalCancer">
//		SELECT	COUNT(HFMA.SmartControlAnswer) AS TOTNUMBER
//		FROM	HistoryFamilyMember_Dtl HFMD,
//				HistoryFamilyMember_Answer HFMA,
//				HistoryFamily HF
//		WHERE	HF.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint"  value="#PatientProfileInfo.PATIENT_ID#">
//				AND HF.Org_id = <cfqueryparam cfsqltype="cf_sql_integer" value="#PatientProfileInfo.ORG_ID#">
//				AND HFMD.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
//				AND HFMA.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
//				AND HFMD.Relationship_HistoryDropdownMaster_ID in (<cfqueryparam cfsqltype="cf_sql_integer" list="yes" value="26,27,28,29,30,31">)
//				AND HFMA.SmartControlAnswer = <cfqueryparam cfsqltype="cf_sql_bit" value="1">
//				AND HFMA.SmartControlMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="121">
//				AND (HFMD.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR HFMD.Hidden is Null)
//		GROUP BY HFMA.SmartControlAnswer			
//	</cfquery>
//</cfif>
//

if ($HasFamilyHistory == 1) {
  $sql = "		SELECT	COUNT(HFMA.SmartControlAnswer) AS TOTNUMBER
		FROM	HistoryFamilyMember_Dtl HFMD,
				HistoryFamilyMember_Answer HFMA,
				HistoryFamily HF
		WHERE	HF.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND HF.Org_id = $PatientProfileInfo_row->Org_ID
				AND HFMD.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
				AND HFMA.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
				AND HFMD.Relationship_HistoryDropdownMaster_ID in (26,27,28,29,30,31)
				AND HFMA.SmartControlAnswer = 1
				AND HFMA.SmartControlMaster_ID = 121
				AND (HFMD.Hidden = 0 OR HFMD.Hidden is Null)
		GROUP BY HFMA.SmartControlAnswer	";
  $N31FamilyHistColorectalCancer = $this->ReportModel->data_db->query($sql);
  $N31FamilyHistColorectalCancer_num = $N31FamilyHistColorectalCancer->num_rows();
  $N31FamilyHistColorectalCancer_result = $N31FamilyHistColorectalCancer->result();
}

//
//
//<!--- # 32 - Inflammatory bowel disease --->
//<cfif Variables.CheckICD9Codes eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N32InflamBowelDisICD9">
//		SELECT	I9.ICD9Code
//		FROM	ICD9Master I9,
//				ProblemList PL
//		WHERE	PL.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Patient_id#">
//				AND PL.Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Org_id#">
//				AND (PL.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR PL.Hidden is Null)
//				AND PL.ICD9_ID = I9.ICD9_ID
//				AND (
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="564.1">) OR
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar" value="564.81">) 
//					)	
//	</cfquery>
//</cfif>
//

if ($CheckICD9Codes == 1) {
  $sql = "		SELECT	I9.ICD9Code
		FROM	ICD9Master I9,
				ProblemList PL
		WHERE	PL.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND PL.Org_id = $PatientProfileInfo_row->Org_ID
				AND (PL.Hidden = 0 OR PL.Hidden is Null)
				AND PL.ICD9_ID = I9.ICD9_ID
				AND (
						(I9.ICD9Code = '564.1') OR
						(I9.ICD9Code = '564.81') 
					)	";
  $N32InflamBowelDisICD9 = $this->ReportModel->data_db->query($sql);
  $N32InflamBowelDisICD9_num = $N32InflamBowelDisICD9->num_rows();
  $N32InflamBowelDisICD9_result = $N32InflamBowelDisICD9->result();
}

//
//
//<!--- # 33 - Ulcerative Colitis --->
//<cfif Variables.CheckICD9Codes eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N33UlcerColitisICD9">
//		SELECT	I9.ICD9Code
//		FROM	ICD9Master I9,
//				ProblemList PL
//		WHERE	PL.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Patient_id#">
//				AND PL.Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Org_id#">
//				AND (PL.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR PL.Hidden is Null)
//				AND PL.ICD9_ID = I9.ICD9_ID
//				AND (
//						(I9.ICD9Code LIKE '556.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="556">) OR
//						(I9.ICD9Code LIKE '558.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="558">) 
//					)	
//	</cfquery>
//</cfif>
//

if ($CheckICD9Codes == 1) {
  $sql = "		SELECT	I9.ICD9Code
		FROM	ICD9Master I9,
				ProblemList PL
		WHERE	PL.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND PL.Org_id = $PatientProfileInfo_row->Org_ID
				AND (PL.Hidden = 0 OR PL.Hidden is Null)
				AND PL.ICD9_ID = I9.ICD9_ID
				AND (
						(I9.ICD9Code LIKE '556.%') OR (I9.ICD9Code = '556') OR
						(I9.ICD9Code LIKE '558.%') OR (I9.ICD9Code = '558') 
					)	";
  $N33UlcerColitisICD9 = $this->ReportModel->data_db->query($sql);
  $N33UlcerColitisICD9_num = $N33UlcerColitisICD9->num_rows();
  $N33UlcerColitisICD9_result = $N33UlcerColitisICD9->result();
}

//
//<!--- # 34 - Crohns disease --->
//<cfif Variables.CheckICD9Codes eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N34CrohnsICD9">
//		SELECT	I9.ICD9Code
//		FROM	ICD9Master I9,
//				ProblemList PL
//		WHERE	PL.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Patient_id#">
//				AND PL.Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Org_id#">
//				AND (PL.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR PL.Hidden is Null)
//				AND PL.ICD9_ID = I9.ICD9_ID
//				AND (
//						(I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="555.9">) 
//					)	 
//	</cfquery>
//</cfif>
//

if ($CheckICD9Codes == 1) {
  $sql = "	SELECT	I9.ICD9Code
		FROM	ICD9Master I9,
				ProblemList PL
		WHERE	PL.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND PL.Org_id = $PatientProfileInfo_row->Org_ID
				AND (PL.Hidden = 0 OR PL.Hidden is Null)
				AND PL.ICD9_ID = I9.ICD9_ID
				AND (
						(I9.ICD9Code = '555.9') 
					)	 ";
  $N34CrohnsICD9 = $this->ReportModel->data_db->query($sql);
  $N34CrohnsICD9_num = $N34CrohnsICD9->num_rows();
  $N34CrohnsICD9_result = $N34CrohnsICD9->result();
}

//
//
//<!--- # 35 - Colon cancer  --->
//<cfif Variables.CheckICD9Codes eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N35ColonCancerICD9">
//		SELECT	I9.ICD9Code
//		FROM	ICD9Master I9,
//				ProblemList PL
//		WHERE	PL.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Patient_id#">
//				AND PL.Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Org_id#">
//				AND (PL.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR PL.Hidden is Null)
//				AND PL.ICD9_ID = I9.ICD9_ID
//				AND (
//						(I9.ICD9Code LIKE '153.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="153">) OR
//						(I9.ICD9Code LIKE '209.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="209">) 
//					)	 
//	</cfquery>
//</cfif>
//

if ($CheckICD9Codes == 1) {
  $sql = "SELECT	I9.ICD9Code
		FROM	ICD9Master I9,
				ProblemList PL
		WHERE	PL.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND PL.Org_id = $PatientProfileInfo_row->Org_ID
				AND (PL.Hidden = 0 OR PL.Hidden is Null)
				AND PL.ICD9_ID = I9.ICD9_ID
				AND (
						(I9.ICD9Code LIKE '153.%') OR (I9.ICD9Code = '153') OR
						(I9.ICD9Code LIKE '209.%') OR (I9.ICD9Code = '209') 
					)	 ";
  $N35ColonCancerICD9 = $this->ReportModel->data_db->query($sql);
  $N35ColonCancerICD9_num = $N35ColonCancerICD9->num_rows();
  $N35ColonCancerICD9_result = $N35ColonCancerICD9->result();
}

//
//
//<!--- # 36 - Family History of Prostate Cancer  --->
//<cfif Variables.HasFamilyHistory eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N36FamilyHistProstateCancer">
//		SELECT	COUNT(HFMA.SmartControlAnswer) AS TOTNUMBER
//		FROM	HistoryFamilyMember_Dtl HFMD,
//				HistoryFamilyMember_Answer HFMA,
//				HistoryFamily HF
//		WHERE	HF.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint"  value="#PatientProfileInfo.PATIENT_ID#">
//				AND HF.Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">
//				AND HFMD.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
//				AND HFMA.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
//				AND HFMD.Relationship_HistoryDropdownMaster_ID in (<cfqueryparam cfsqltype="cf_sql_integer" list="yes" value="27,29">)
//				AND HFMA.SmartControlAnswer = <cfqueryparam cfsqltype="cf_sql_bit" value="1">
//				AND HFMA.SmartControlMaster_ID = <cfqueryparam cfsqltype="cf_sql_integer" value="145">
//				AND (HFMD.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR HFMD.Hidden is Null)
//		GROUP BY HFMA.SmartControlAnswer			
//	</cfquery>
//</cfif>
//

if ($HasFamilyHistory == 1) {
  $sql = "SELECT	COUNT(HFMA.SmartControlAnswer) AS TOTNUMBER
		FROM	HistoryFamilyMember_Dtl HFMD,
				HistoryFamilyMember_Answer HFMA,
				HistoryFamily HF
		WHERE	HF.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND HF.Org_id = $PatientProfileInfo_row->Org_ID
				AND HFMD.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
				AND HFMA.HistoryFamilyMember_Dtl_ID = HF.HistoryFamilyMember_Dtl_ID
				AND HFMD.Relationship_HistoryDropdownMaster_ID in (27,29)
				AND HFMA.SmartControlAnswer = 1
				AND HFMA.SmartControlMaster_ID = 145
				AND (HFMD.Hidden = 0 OR HFMD.Hidden is Null)
		GROUP BY HFMA.SmartControlAnswer";
  $N36FamilyHistProstateCancer = $this->ReportModel->data_db->query($sql);
  $N36FamilyHistProstateCancer_num = $N36FamilyHistProstateCancer->num_rows();
  $N36FamilyHistProstateCancer_result = $N36FamilyHistProstateCancer->result();
}
//
//
//<!--- # 37 - Prostatitis  --->
//<cfif Variables.CheckICD9Codes eq 1>
//	<cfquery datasource="#Variables.EMRDataSource#" name="N37ProstatitisICD9">
//		SELECT	I9.ICD9Code
//		FROM	ICD9Master I9,
//				ProblemList PL
//		WHERE	PL.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Patient_id#">
//				AND PL.Org_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.Org_id#">
//				AND (PL.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR PL.Hidden is Null)
//				AND PL.ICD9_ID = I9.ICD9_ID
//				AND (
//						(I9.ICD9Code LIKE '098.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="098">) OR
//						(I9.ICD9Code LIKE '131.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="131">) OR					
//						(I9.ICD9Code LIKE '601.%') OR (I9.ICD9Code = <cfqueryparam cfsqltype="cf_sql_varchar"  value="601">) 
//					)	 
//	</cfquery>
//</cfif>
//
//

if ($CheckICD9Codes == 1) {
  $sql = "SELECT	I9.ICD9Code
		FROM	ICD9Master I9,
				ProblemList PL
		WHERE	PL.Patient_id = $PatientProfileInfo_row->Patient_ID
				AND PL.Org_id = $PatientProfileInfo_row->Org_ID
				AND (PL.Hidden = 0 OR PL.Hidden is Null)
				AND PL.ICD9_ID = I9.ICD9_ID
				AND (
						(I9.ICD9Code LIKE '098.%') OR (I9.ICD9Code = '098') OR
						(I9.ICD9Code LIKE '131.%') OR (I9.ICD9Code = '131') OR					
						(I9.ICD9Code LIKE '601.%') OR (I9.ICD9Code = '601') 
					)	 ";
  $N37ProstatitisICD9 = $this->ReportModel->data_db->query($sql);
  $N37ProstatitisICD9_num = $N37ProstatitisICD9->num_rows();
  $N37ProstatitisICD9_result = $N37ProstatitisICD9->result();
  $N37ProstatitisICD9_row = $N37ProstatitisICD9->row();
}
//
//
//<!--- Calculate Risk Factors--->
//
//<!--- #1 - Race as applies to Diabetes (Race 3)  --->
//<cfset Variables.TDescription1 = 'Patient Vitals: Race as applies to Diabetes'>
//
$TDescription1 = 'Patient Vitals: Race as applies to Diabetes';
//
//yobi skip
//<cfoutput>(#Variables.TDescription1#[1])</cfoutput>
//
//<cfif PatientProfileInfo.Race_EthnicityMaster_ID eq 9 AND PatientProfileInfo.Ethnicity_EthnicityMaster_ID neq 14>
//	- Patient race is 'White' (Value = 0) <br>
//	<cfset Variables.TValue1 = 0>
//<cfelseif PatientProfileInfo.Race_EthnicityMaster_ID eq 8  AND PatientProfileInfo.Ethnicity_EthnicityMaster_ID eq 15>
//	- Patient race is ‘Black or African-American’ and Ethnicity is 'Not Hispanic or Latino'  (value = 2)<br>
//	<cfset Variables.TValue1 = 2>	
//<cfelseif PatientProfileInfo.Race_EthnicityMaster_ID eq 1>
//	- Patient race is  ‘American Indian or Alaska Native’  (value = 2)<br>
//	<cfset Variables.TValue1 = 2>	
//<cfelseif PatientProfileInfo.Race_EthnicityMaster_ID eq 5>
//	- Patient race is  ‘Asian’  (value = 2)<br>
//	<cfset Variables.TValue1 = 2>	
//<cfelseif PatientProfileInfo.Race_EthnicityMaster_ID eq 8 >
//	- Patient race is ‘Black or African-American’   (value = 1)<br>
//<cfelse>
//	- Patient Race and Ethnicity are none that we are searching for  (value = -1)<br>		
//</cfif>
//

if ($PatientProfileInfo_row->Race_EthnicityMaster_ID == 9 && $PatientProfileInfo_row->Ethnicity_EthnicityMaster_ID != 14) {
  $TValue1 = 0;
} elseif ($PatientProfileInfo_row->Race_EthnicityMaster_ID == 8 && $PatientProfileInfo_row->Ethnicity_EthnicityMaster_ID == 15) {
  $TValue1 = 2;
} elseif ($PatientProfileInfo_row->Race_EthnicityMaster_ID == 1) {
  $TValue1 = 2;
} elseif ($PatientProfileInfo_row->Race_EthnicityMaster_ID == 5) {
  $TValue1 = 2;
} elseif ($PatientProfileInfo_row->Race_EthnicityMaster_ID == 8) {
  
} else {
  
}

//
//<!--- # 2 - Family Hx CVD 1 degree --->
//<cfset Variables.TDescription2 = 'Family History: CVD 1 degree (Heart Disease)'>
//<cfoutput>(#Variables.TDescription2#[2])</cfoutput>
//<cfif Variables.HasFamilyHistory eq 1>
//	<cfif N2FamilyHistHeartDisease.RecordCount eq 0>
//		- No family members gave a yes answer to CVD 1 degree (Heart Disease) (Value = -1)<br>
//		<!---<cfset Variables.TValue2 = 0>--->			
//	<cfelse>
//		- One or more family members gave a yes answer to CVD 1 degree (Heart Disease)  (value = 2) <br>
//		<cfset Variables.TValue2 = 2>							
//	</cfif>
//<cfelse>
//		- The patient has No family members records (Value = -1)<br>
//</cfif>
//

$TDescription2 = 'Family History: CVD 1 degree (Heart Disease)';
if ($HasFamilyHistory == 1) {
  if ($N2FamilyHistHeartDisease_num == 0) {
    
  } else {
    $TValue2 = 2;
  }
}

//
//
//<!--- # 3 - History of LVH ICD9 --->
//<cfset Variables.TDescription3 = 'Problem Reported: History of LVH ICD9'>
//<cfoutput>(#Variables.TDescription3#[3])</cfoutput>
//<cfif Variables.CheckICD9Codes neq 0>
//	<cfif N3HistoryOfLVHICD9.RecordCount eq 0>
//		- No ICD9 426.*, 427.* (History of LVH) record found for the patient (Value = -1)<br>
//		<!---<cfset Variables.TValue3 = 0>--->
//	<cfelse>		
//		- One or More ICD9 426.*, 427.* (History of LVH) record found for the patient (Value = 2)<br>
//		<cfset Variables.TValue3 = 2>
//	</cfif>
//<cfelse>
//	- We do not know if the patient had a Previous Heart Attack (Value = -1)<br>		
//</cfif>
//

$TDescription3 = 'Problem Reported: History of LVH ICD9';
if ($CheckICD9Codes != 0) {
  if ($N3HistoryOfLVHICD9_num == 0) {
    
  } else {
    $TValue3 = 2;
  }
}

//
//<!--- # 4 - HbA1C --->
//<cfset Variables.TDescription4 = 'Lab Results: HbA1C'>
//<cfoutput>(#Variables.TDescription4#[4])</cfoutput>
//<cfif N4HbA1C.RecordCount eq 0>
//	- No HbA1C records found (Value = -1) <br>
//<cfelseif N4HbA1C.ResultsTestResults_num lte 6.049>
//	- HbA1C less than or equal to 6.0 (Value = 0) <br>
//	<cfset Variables.TValue4 = 0>
//<cfelseif N4HbA1C.ResultsTestResults_num gte 6.05 AND N4HbA1C.ResultsTestResults_num lte 6.549>	
//	- HbA1C greater than or equal to 6.1 and less than or equal to 6.5 (Value = 1) <br>
//	<cfset Variables.TValue4 = 1>
//<cfelseif N4HbA1C.ResultsTestResults_num gte 6.55 AND N4HbA1C.ResultsTestResults_num lte 7.049>
//	- HbA1C greater than or equal to  6.6 and less than or equal to 7.0 (Value = 2) <br>
//	<cfset Variables.TValue4 = 2>
//<cfelse> 
//	- HbA1C greater than 7.0  (Value = 3) <br>
//	<cfset Variables.TValue4 = 3>
//</cfif>
//

$TDescription4 = 'Lab Results: HbA1C';
if ($N4HbA1C_num == 0) {
  
} elseif ($N4HbA1C_row->ResultsTestResults_num < 6.049) {
  $TValue4 = 0;
} elseif ($N4HbA1C_row->ResultsTestResults_num > 6.05 && $N4HbA1C_row->ResultsTestResults_num < 6.549) {
  $TValue4 = 1;
} elseif ($N4HbA1C_row->ResultsTestResults_num > 6.55 && $N4HbA1C_row->ResultsTestResults_num < 7.049) {
  $TValue4 = 1;
} else {
  $TValue4 = 3;
}

//
//
//<!--- # 5 - BMI --->
//<cfset Variables.TDescription5 = 'Patient Vitals: BMI'>
//<cfoutput>(#Variables.TDescription5#[5])</cfoutput>
//<cfif Variables.BMI eq -1 >
//	- No useable BMI data (Value = -1)<br>
//<cfelseif Variables.BMI gt -1 AND Variables.BMI lte 25.049  >
//	- BMI data less than or equal to 25 (Value = 0)<br>
//	<cfset Variables.TValue5 = 0>	
//<cfelseif Variables.BMI gte 25.050 AND Variables.BMI lte 29.949>
//	- BMI data between 25.1 and 29.9 (inclusive)  (Value = 1)<br>
//	<cfset Variables.TValue5 = 1>	
//<cfelseif Variables.BMI gte 29.950 AND Variables.BMI lte 35.049> 
//	- BMI data between 30.0 and 35.0 (inclusive) (Value =  2)<br>
//	<cfset Variables.TValue5 = 2>	
//<cfelse>	
//	- BMI data greater than 35.0  (Value = 3)<br>
//	<cfset Variables.TValue5 = 3>	
//</cfif>
//

$TDescription5 = "Patient Vitals: BMI";
if ($BMI == -1) {
  
} elseif ($BMI > -1 && $BMI < 25.49) {
  $TValue5 = 0;
} elseif ($BMI > 25.050 && $BMI < 29.949) {
  $TValue5 = 1;
} elseif ($BMI > 29.950 && $BMI < 35.049) {
  $TValue5 = 2;
} else {
  $TValue5 = 3;
}

//
//
//<!--- #6 - Family History of Diabetes  --->
//<cfset Variables.TDescription6 = 'Family History: Diabetes'>
//<cfoutput>(#Variables.TDescription6#[6])</cfoutput>
//<cfif Variables.HasFamilyHistory eq 1>
//	<cfif N6FamilyHistDiabetes.RecordCount eq 0>
//		- No family members gave either a yes answer to diabetes question (Value = 0)<br>
//		<cfset Variables.TValue6 = 0>			
//	<cfelse>
//		<cfif N6FamilyHistDiabetes.TOTNUMBER eq 1>
//				- Diabetes - One family member (value = 1) <br>
//				<cfset Variables.TValue6 = 1>					
//		<cfelseif N6FamilyHistDiabetes.TOTNUMBER eq 2>
//				- Diabetes - Two family member (value = 2) <br>
//				<cfset Variables.TValue6 = 2>							
//		<cfelse>
//				- Diabetes - More than 2 family member (value = 3) <br>
//				<cfset Variables.TValue6 = 3>							
//		</cfif>
//	</cfif>
//<cfelse>
//		- The patient has No family members records (Value = -1)<br>
//</cfif>
//

$TDescription6 = 'Family History: Diabetes';
if ($HasFamilyHistory == 1) {
  if ($N6FamilyHistDiabetes_num == 0) {
    $TValue6 = 0;
  } else {
    if ($N6FamilyHistDiabetes_row->TOTNUMBER == 1) {
      $TValue6 = 1;
    } elseif ($N6FamilyHistDiabetes_row->TOTNUMBER == 2) {
      $TValue6 = 2;
    } else {
      $TValue6 = 3;
    }
  }
} else {
  
}
//
//
//<!--- #7 - Smoking Status  --->
//<cfset Variables.TDescription7 = 'Patient History: Smoking Status'>
//<cfoutput>(#Variables.TDescription7#[7])</cfoutput>
//<cfif Variables.HasNewHistoryId eq 1>
//	<cfswitch expression="#N7SmokingStatus.TobaccoUse_HistoryDropdownMaster_ID#">
//		<cfcase value="56,63">
//			- Patient was not asked or unknown if they smoke (value = -1) <br>
//		</cfcase>
//		<cfcase value="57,58,61">
//			<cfif N7SmokingStatus.PacksPerDay lte 2>
//				- Patient currently smokes less than or equal to 2 packs per day (Value = 2)<br>
//				<cfset Variables.TValue7 = 2>					
//			<cfelse>
//				- Patient currently smokes more than 2 packs per day (Value = 3)<br>	
//				<cfset Variables.TValue7 = 3>			 
//			</cfif>
//		</cfcase>
//		<cfcase value="59">
//			- Patient is a former smoker (value = 1) <br>
//			<cfset Variables.TValue7 = 1>		
//		</cfcase>
//		<cfcase value="60">
//			- Patient never smoked (value = 0) <br>	
//			<cfset Variables.TValue7 = 0>			 	
//		</cfcase>
//	</cfswitch>
//<cfelse>
//			- Patient was not asked or unknown if they smoke since they don't have a new history record (value = -1) <br>
//</cfif>		
//

$TDescription7 = 'Patient History: Smoking Status';
if ($HasNewHistoryId == 1 && $N7SmokingStatus_num) {


  if ($N7SmokingStatus_row->TobaccoUse_HistoryDropdownMaster_ID == "56,63") {
    
  } elseif ($N7SmokingStatus_row->TobaccoUse_HistoryDropdownMaster_ID == "57,58,61") {
    if ($N7SmokingStatus_row->PacksPerDay < 2) {
      $TValue7 = 2;
    } else {
      $TValue7 = 3;
    }
  } elseif ($N7SmokingStatus_row->TobaccoUse_HistoryDropdownMaster_ID == "59") {
    $TValue7 = 1;
  } elseif ($N7SmokingStatus_row->TobaccoUse_HistoryDropdownMaster_ID == "60") {
    $TValue7 = 0;
  }
}
//
//<!--- #8 - Alcohol Intake  --->
//<cfset Variables.TDescription8 = 'Patient History: Alcohol Intake'>
//<cfoutput>(#Variables.TDescription8#[8])</cfoutput>
//<cfif Variables.HasNewHistoryId eq 1>
//	<cfif N8AlcoholIntake.AlcoholHistory_HistoryDropdownMaster_ID eq 15>
//		- Patient was not asked or unknown if they drink Alcohol (Value = -1) <br>
//	<cfelseif N8AlcoholIntake.AlcoholHistory_HistoryDropdownMaster_ID eq 17>
//		- Patient does not drink Alcohol (Value = 0)<br>
//		<cfset Variables.TValue8 = 0>	
//	<cfelse>
//		<cfset Variables.AlcoholDrinkPerWeekDen = -1>
//		<cfswitch expression="#N8DrinksPerTime.DrinkUnits_HistoryDropdownMaster_ID#">
//			<cfcase value="45">
//				<cfset Variables.AlcoholDrinkPerWeekDen = 1>
//			</cfcase>
//			<cfcase value="46">
//				<cfset Variables.AlcoholDrinkPerWeekDen = 7>
//			</cfcase>
//			<cfcase value="47">
//				<cfset Variables.AlcoholDrinkPerWeekDen = 30>
//			</cfcase>
//			<cfcase value="48">
//				<cfset Variables.AlcoholDrinkPerWeekDen = 365>
//			</cfcase>
//		</cfswitch>
//		<cfif (Variables.AlcoholDrinkPerWeekDen neq -1 AND N8AlcoholIntake.NumberOfDrinks neq 0) >
//			<cfset Variables.DrinksPerWeek = (N8AlcoholIntake.NumberOfDrinks/Variables.AlcoholDrinkPerWeekDen)>
//			<cfif Variables.DrinksPerWeek lt .5>
//				- Patient does not drink Alcohol (Value = 0)<br>
//				<cfset Variables.TValue8 = 0>			
//			<cfelseif Variables.DrinksPerWeek gte .5 AND  Variables.DrinksPerWeek lt 2.5>
//				- Patient drinks 1-2 Glasses of Alcohol per day (Value = 1)<br>
//				<cfset Variables.TValue8 = 1>				
//			<cfelseif Variables.DrinksPerWeek gte 2  AND Variables.DrinksPerWeek lt 5.5>
//				- Patient drinks 2-5 Glasses of Alcohol per day (Value = 2)<br>
//				<cfset Variables.TValue8 = 2>				
//			<cfelse>
//				- Patient drinks 5 or more Glasses of Alcohol per day (Value = 3)<br>
//				<cfset Variables.TValue8 = 3>				
//		</cfif>
//		<cfelse>
//			<!---One or both of the variables needed for drinks/day are unavailable--->
//			- Patient did not tell us how much he drinks, assuming 1-2 Glasses of Alcohol per day (Value = 1)<br>
//			<cfset Variables.TValue8 = 1>		
//		</cfif>	
//	</cfif>
//<cfelse>
//		- Patient don't have a new history record  so it is unknown if they drink Alcohol (Value = -1) <br>
//</cfif>
//

$TDescription8 = 'Patient History: Alcohol Intake';
if ($HasNewHistoryId == 1 && $N8AlcoholIntake_row) {
  if ($N8AlcoholIntake_row->AlcoholHistory_HistoryDropdownMaster_ID == 15) {
    
  } elseif ($N8AlcoholIntake_row->AlcoholHistory_HistoryDropdownMaster_ID == 17) {
    $TValue8 = 0;
  } else {
    $AlcoholDrinkPerWeekDen = -1;
    if ($N8DrinksPerTime_row->DrinkUnits_HistoryDropdownMaster_ID == '45') {
      $AlcoholDrinkPerWeekDen = 1;
    } elseif ($N8DrinksPerTime_row->DrinkUnits_HistoryDropdownMaster_ID == '46') {
      $AlcoholDrinkPerWeekDen = 7;
    } elseif ($N8DrinksPerTime_row->DrinkUnits_HistoryDropdownMaster_ID == '47') {
      $AlcoholDrinkPerWeekDen = 30;
    } elseif ($N8DrinksPerTime_row->DrinkUnits_HistoryDropdownMaster_ID == '48') {
      $AlcoholDrinkPerWeekDen = 365;
    }
    if ($AlcoholDrinkPerWeekDen != -1 && ($N8AlcoholIntake_row && $N8AlcoholIntake_row->NumberOfDrinks != 0)) {
      
    }
  }
}
//
//
//<!--- #9 - Race as applies to Colorectal Cancer (Race 1)  --->
//<cfset Variables.TDescription9 = 'Patient Vitals: Race as applies to Colorectal Cancer'>
//<cfoutput>(#Variables.TDescription9#[9])</cfoutput>
//<cfif PatientProfileInfo.Race_EthnicityMaster_ID eq 9 AND PatientProfileInfo.Ethnicity_EthnicityMaster_ID neq 14>
//	- Patient race is 'White' (Value = 0) <br>
//	<cfset Variables.TValue9 = 0>
//<cfelseif PatientProfileInfo.Race_EthnicityMaster_ID eq 8  AND PatientProfileInfo.Ethnicity_EthnicityMaster_ID eq 14>
//	- Patient race is ‘Black or African-American’ and Ethnicity is 'Hispanic or Latino'  (value = 0)<br>
//	<cfset Variables.TValue9 = 0>		
//<cfelseif PatientProfileInfo.Race_EthnicityMaster_ID eq 8  AND PatientProfileInfo.Ethnicity_EthnicityMaster_ID eq 15>
//	- Patient race is ‘Black or African-American’ and Ethnicity is 'Not Hispanic or Latino'  (value = 2)<br>
//	<cfset Variables.TValue9 = 2>	
//<cfelseif PatientProfileInfo.Race_EthnicityMaster_ID eq 1>
//	- Patient race is  ‘American Indian or Alaska Native’  (value = 2)<br>
//	<cfset Variables.TValue9 = 2>	
//<cfelseif PatientProfileInfo.Race_EthnicityMaster_ID eq 5>
//	- Patient race is  ‘Asian’  (value = 2)<br>
//	<cfset Variables.TValue9 = 2>	
//<cfelseif PatientProfileInfo.Ethnicity_EthnicityMaster_ID eq 14>
//	- Patient Ethnicity is 'Hispanic or Latino'  (value = 2)<br>	
//	<cfset Variables.TValue9 = 2>
//<cfelse>
//	- Patient Race and Ethnicity are none that we are searching for  (value = -1)<br>		
//</cfif>
//

$TDescription9 = 'Patient Vitals: Race as applies to Colorectal Cancer';
if ($PatientProfileInfo_row->Race_EthnicityMaster_ID == 9 && $PatientProfileInfo_row->Ethnicity_EthnicityMaster_ID != 14) {
  $TValue9 = 0;
} elseif ($PatientProfileInfo_row->Race_EthnicityMaster_ID == 8 && $PatientProfileInfo_row->Ethnicity_EthnicityMaster_ID == 14) {
  $TValue9 = 0;
} elseif ($PatientProfileInfo_row->Race_EthnicityMaster_ID == 8 && $PatientProfileInfo_row->Ethnicity_EthnicityMaster_ID == 15) {
  $TValue9 = 2;
} elseif ($PatientProfileInfo_row->Race_EthnicityMaster_ID == 1) {
  $TValue9 = 2;
} elseif ($PatientProfileInfo_row->Race_EthnicityMaster_ID == 5) {
  $TValue9 = 2;
} elseif ($PatientProfileInfo_row->Ethnicity_EthnicityMaster_ID == 14) {
  $TValue9 = 2;
} else {
  
}


//
//
//<!--- #10 - Race as applies to Prostate Cancer (Race 2)  --->
//<cfoutput>(#Variables.TDescription10#[10])</cfoutput>
//<cfset Variables.TDescription10 = 'Patient Vitals: Race as applies to Prostate Cancer'>
//<cfif PatientProfileInfo.Race_EthnicityMaster_ID eq 5 AND PatientProfileInfo.Ethnicity_EthnicityMaster_ID eq 15>
//	- Patient race is ‘Asian’ and Ethnicity is 'Not Hispanic or Latino'  (value = 0)<br>
//	<cfset Variables.TValue10 = 0>
//<cfelseif PatientProfileInfo.Race_EthnicityMaster_ID eq 9 AND PatientProfileInfo.Ethnicity_EthnicityMaster_ID eq 15>
//	- Patient race is ‘White’ and Ethnicity is 'Not Hispanic or Latino'  (value = 0)<br>
//	<cfset Variables.TValue10 = 0>
//<cfelseif PatientProfileInfo.Race_EthnicityMaster_ID eq 9>
//	- Patient race is ‘White’ and Ethnicity is not 'Not Hispanic or Latino'  (value = 1)<br>
//	<cfset Variables.TValue10 = 1>	
//<cfelseif PatientProfileInfo.Ethnicity_EthnicityMaster_ID eq 14>
//	- Patient Ethnicity is 'Hispanic or Latino'  (value = 1)<br>	
//	<cfset Variables.TValue10 = 1>	
//<cfelseif PatientProfileInfo.Race_EthnicityMaster_ID eq 8 >	
//	- Patient race is ‘Black or African-American’ (value = 2)<br>
//	<cfset Variables.TValue10 = 2>		
//<cfelse>	
//	- Patient Race and Ethnicity are none that we are searching for  (value = -1)<br>		
//</cfif>
//
//

$TDescription10 = 'Patient Vitals: Race as applies to Prostate Cancer';
if ($PatientProfileInfo_row->Race_EthnicityMaster_ID = 5 && $PatientProfileInfo_row->Ethnicity_EthnicityMaster_ID == 15) {
  $TValue10 = 0;
} elseif ($PatientProfileInfo_row->Race_EthnicityMaster_ID = 9 && $PatientProfileInfo_row->Ethnicity_EthnicityMaster_ID == 15) {
  $TValue10 = 0;
} elseif ($PatientProfileInfo_row->Race_EthnicityMaster_ID = 9) {
  $TValue10 = 1;
} elseif ($PatientProfileInfo_row->Ethnicity_EthnicityMaster_ID = 14) {
  $TValue10 = 1;
} elseif ($PatientProfileInfo_row->Race_EthnicityMaster_ID = 8) {
  $TValue10 = 2;
} else {
  
}

//
//<!--- # 11 - Blood Pressure Systolic --->
//<cfset Variables.TDescription11 = 'Patient Vitals: Blood Pressure Systolic'>
//<cfoutput>(#Variables.TDescription11#[11])</cfoutput>
//<cfif NOT IsNumeric(Variables.Systolic) or Variables.Systolic eq 0>
//	- Blood Pressure Systolic is either not found or non-numeric (Value = -1) <br>
//<cfelse>
//	<cfif Variables.Systolic lte 139.499>
//		- Blood Pressure Systolic is less than 140 (Value = 0)<br>
//		<cfset Variables.TValue11 = 0>		
//	<cfelseif Variables.Systolic gte 139.5 AND Variables.Systolic lte 160.499>	
//		- Blood Pressure Systolic is between 140 and 160 inclusive (Value = 1)<br>
//		<cfset Variables.TValue11 = 1>				
//	<cfelseif Variables.Systolic gte 160.5 AND Variables.Systolic lte 170.499>		
//		- Blood Pressure Systolic is between 161 and 170 inclusive (Value = 2)<br>
//		<cfset Variables.TValue11 = 2>				
//	<cfelse>	
//		- Blood Pressure Systolic is greater than 170 (Value = 3)<br>
//		<cfset Variables.TValue11 = 3>				
//	</cfif>		
//</cfif>
//
$TDescription11 = 'Patient Vitals: Blood Pressure Systolic';
if (!is_numeric($Systolic) || $Systolic == 0) {
  
} else {
  if ($Systolic < 139.499) {
    $TValue11 = 0;
  } elseif ($Systolic > 139.5 && $Systolic < 160.499) {
    $TValue11 = 1;
  } elseif ($Systolic > 160.5 && $Systolic < 170.499) {
    $TValue11 = 2;
  } else {
    $TValue11 = 3;
  }
}

//
//
//<!--- # 12 - Blood Pressure Diastolic --->
//<cfset Variables.TDescription12 = 'Patient Vitals: Blood Pressure Diastolic'>
//<cfoutput>(#Variables.TDescription12#[12])</cfoutput>
//<cfif NOT IsNumeric(Variables.Diastolic) or Variables.Diastolic eq 0>
//	- Blood Pressure Diastolic is either not found or non-numeric (Value = -1) <br>
//<cfelse>
//	<cfif Variables.Diastolic lte 89.499>
//		- Blood Pressure Diastolic is less than 90 (Value = 0)<br>
//		<cfset Variables.TValue12 = 0>				
//	<cfelseif Variables.Diastolic gte 89.5 AND Variables.Diastolic lte 100.499>	
//		- Blood Pressure Diastolic is between 90 and 100 inclusive (Value = 1)<br>
//		<cfset Variables.TValue12 = 1>			
//	<cfelseif Variables.Diastolic gte 100.5 AND Variables.Diastolic lte 120.499>		
//		- Blood Pressure Diastolic is between 101 and 120 inclusive (Value = 2)<br>
//		<cfset Variables.TValue12 = 2>			
//	<cfelse>	
//		- Blood Pressure Diastolic is greater than 120 (Value = 3)<br>
//		<cfset Variables.TValue12 = 3>			
//	</cfif>		
//</cfif>
//

$TDescription12 = "Patient Vitals: Blood Pressure Diastolic";

if (!is_numeric($Diastolic) || $Diastolic == 0) {
  
} else {
  if ($Diastolic < 89.499) {
    $TValue12 = 0;
  } elseif ($Diastolic > 89.5 && $Diastolic < 100.499) {
    $TValue12 = 1;
  } elseif ($Diastolic > 100.5 && $Diastolic < 120.499) {
    $TValue12 = 2;
  } else {
    $TValue12 = 3;
  }
}

//
//
//<!--- # 13 - Total Cholesterol --->
//<cfset Variables.TDescription13 = 'Lab Results: Total Cholesterol'>
//<cfoutput>(#Variables.TDescription13#[13])</cfoutput>
//<cfif N13TotChol.Recordcount neq 0 AND Trim(N13TotChol.ResultsTestResults_num) neq ''>
//	<cfif N13TotChol.ResultsTestResults_num gt 0 AND N13TotChol.ResultsTestResults_num lte 199.499>
//		- Total Cholesterol was less than 200 (Value = 0)<br>
//		<cfset Variables.TValue13 = 0>			
//	<cfelseif N13TotChol.ResultsTestResults_num gte 199.5 AND N13TotChol.ResultsTestResults_num lte 240.499>	
//		- Total Cholesterol was between 200 and 240 inclusive (Value = 1)<br>
//		<cfset Variables.TValue13 = 1>			
//	<cfelse>
//		- Total Cholesterol was greater than 240 (Value = 2)<br>
//		<cfset Variables.TValue13 = 2>			
//	</cfif>
//<cfelse>
//	- No Total Cholesterol lab test found (Value = -1) <br>
//</cfif>
//

$TDescription13 = 'Lab Results: Total Cholesterol';
if ($N13TotChol_num != 0 && $N13TotChol_row->ResultsTestResults_num != '') {
  if ($N13TotChol_row->ResultsTestResults_num > 0 && $N13TotChol_row->ResultsTestResults_num <= 199.499) {
    $TValue13 = 0;
  } elseif ($N13TotChol_row->ResultsTestResults_num >= 199.5 && $N13TotChol_row->ResultsTestResults_num <= 240.499) {
    $TValue13 = 1;
  } else {
    $TValue13 = 2;
  }
}

//
//<!--- #14 - LDL --->
//<cfset Variables.TDescription14 = 'Lab Results: LDL'>
//<cfoutput>(#Variables.TDescription14#[14])</cfoutput>
//<cfif N14LDL.Recordcount neq 0 AND Trim(N14LDL.ResultsTestResults_num) neq ''>
//	<cfif N14LDL.ResultsTestResults_num gt 0 AND N14LDL.ResultsTestResults_num lte 99.5>
//		- LDL was less than 100 (Value = 0)<br>
//		<cfset Variables.TValue14 = 0>	
//	<cfelseif  N14LDL.ResultsTestResults_num gte 99.5 AND N14LDL.ResultsTestResults_num lte 129.499>
//		- LDL was between 100 and 129 inclusive (Value = 1)<br>
//		<cfset Variables.TValue14 = 1>		
//	<cfelseif  N14LDL.ResultsTestResults_num gte 129.5 AND N14LDL.ResultsTestResults_num lte 159.499>
//		- LDL was between 130 and 159 inclusive (Value = 2)<br>
//		<cfset Variables.TValue14 = 2>		
//	<cfelse>
//		- LDL was greater than 159 (Value = 3)<br>
//		<cfset Variables.TValue14 = 3>			
//	</cfif>
//<cfelse>
//	- No LDL lab test found (Value = -1) <br>
//</cfif>	
//

$TDescription14 = 'Lab Results: LDL';
if ($N14LDL_num != 0 && $N14LDL_row->ResultsTestResults_num != '') {
  if ($N14LDL_row->ResultsTestResults_num > 0 && $N14LDL_row->ResultsTestResults_num < 99.5) {
    $TValue14 = 0;
  } elseif ($N14LDL_row->ResultsTestResults_num > 99.5 && $N14LDL_row->ResultsTestResults_num < 129.5) {
    $TValue14 = 1;
  } elseif ($N14LDL_row->ResultsTestResults_num > 129.5 && $N14LDL_row->ResultsTestResults_num < 159.499) {
    $TValue14 = 2;
  } else {
    $TValue14 = 3;
  }
}


//
//
//<!--- #15 - HDL --->
//<cfset Variables.TDescription15 = 'Lab Results: HDL'>
//<cfoutput>(#Variables.TDescription15#[15])</cfoutput>
//<cfif ((N15HDL.Recordcount neq 0)AND (Trim(N15HDL.ResultsTestResults_num) neq '')) >
//	<cfif PatientProfileInfo.Sex eq 'M'>
//		<cfif N15HDL.ResultsTestResults_num gt 0 AND N15HDL.ResultsTestResults_num lte 39.499>
//			- Patient was Male and HDL was less than 40 (Value = 2)<br>
//			<cfset Variables.TValue15 = 2>	
//		<cfelseif N15HDL.ResultsTestResults_num gte 39.5 AND N15HDL.ResultsTestResults_num lte 60.499>
//			- Patient was Male and HDL was between 40 and 60 inclusive (Value = 1)<br>
//			<cfset Variables.TValue15 = 1>	
//		<cfelse>
//			- Patient was Male and HDL was greater than 60 (Value = 0)<br>
//			<cfset Variables.TValue15 = 0>			
//		</cfif>
//	<cfelseif  PatientProfileInfo.Sex eq 'F'>
//		<cfif N15HDL.ResultsTestResults_num gt 0 AND N15HDL.ResultsTestResults_num lte 49.499>
//			- Patient was Female and HDL was less than 50 (Value = 2)<br>
//			<cfset Variables.TValue15 = 2>	
//		<cfelseif N15HDL.ResultsTestResults_num gte 49.5 AND N15HDL.ResultsTestResults_num lte 60.499>
//			- Patient was Female and HDL was between 50 and 60 inclusive (Value = 1)<br>
//			<cfset Variables.TValue15 = 1>	
//		<cfelse>
//			- Patient was Female and HDL was greater than 60 (Value = 0)<br>
//			<cfset Variables.TValue15 = 0>			
//		</cfif>
//	<cfelse>
//		<cfif N15HDL.ResultsTestResults_num gt 60.499>		
//			- We don't know the patients sex but the value is greater than 60 which has the same value for both sexes (Value = 0)<br>
//			<cfset Variables.TValue15 = 0>	
//		<cfelse>
//			- We don't know the patients sex but the value less than 60 (value = -1)	<br>
//		</cfif>
//	</cfif>	
//<cfelse>
//	- No HDL lab test found (Value = -1) <br>
//</cfif>	
//

$TDescription15 = 'Lab Results: HDL';
if (($N15HDL_num != 0) && ($N15HDL_row->ResultsTestResults_num != '')) {
  if ($PatientProfileInfo_row->Sex == 'M') {
    if ($N15HDL_row->ResultsTestResults_num > 0 && $N15HDL_row->ResultsTestResults_num <= 39.499) {
      $TValue15 = 2;
    } elseif ($N15HDL_row->ResultsTestResults_num > 39.5 && $N15HDL_row->ResultsTestResults_num <= 60.499) {
      $TValue15 = 1;
    } else {
      $TValue15 = 0;
    }
  } elseif ($PatientProfileInfo_row->Sex == 'F') {
    if ($N15HDL_row->ResultsTestResults_num > 0 && $N15HDL_row->ResultsTestResults_num <= 39.499) {
      $TValue15 = 2;
    } elseif ($N15HDL_row->ResultsTestResults_num > 39.5 && $N15HDL_row->ResultsTestResults_num <= 60.499) {
      $TValue15 = 1;
    } else {
      $TValue15 = 0;
    }
  } else {
    if ($N15HDL_row->ResultsTestResults_num > 60.499) {
      $TValue15 = 0;
    } else {
      
    }
  }
} else {
  
}

//
//
//<!--- #16 - Triglycerides (This query in production got 108473 hits for 'Triglycerides' and 218 for 'LDX Triglycerides'---> 
//<cfset Variables.TDescription16 = 'Lab Results: Triglycerides'>
//<cfoutput>(#Variables.TDescription16#[16])</cfoutput>
//<cfif N16Triglycerides.Recordcount neq 0 AND Trim(N16Triglycerides.ResultsTestResults_num) neq ''>
//	<cfif N16Triglycerides.ResultsTestResults_num gt 0 AND N16Triglycerides.ResultsTestResults_num lt 149.499>
//		- Triglycerides were less than 150 (Value = 0)<br>
//		<cfset Variables.TValue16 = 0>			
//	<cfelseif N16Triglycerides.ResultsTestResults_num gte 149.5 AND N16Triglycerides.ResultsTestResults_num lte 199.499>	
//		- Triglycerides were between 150 and 199 inclusive (Value = 1)<br>
//		<cfset Variables.TValue16 = 1>			
//	<cfelseif N16Triglycerides.ResultsTestResults_num gte 199.5 AND N16Triglycerides.ResultsTestResults_num lte 500.499>
//		- Triglycerides were between 200 and 500 inclusive (Value = 2)<br>
//		<cfset Variables.TValue16 = 2>			
//	<cfelse>
//		- Triglycerides were greater than 500 (Value = 3)<br>
//		<cfset Variables.TValue16 = 3>			
//	</cfif>
//<cfelse>
//	- No Triglycerides lab test found (Value = -1) <br>
//</cfif>
//

$TDescription16 = 'Lab Results: Triglycerides';
if ($N16Triglycerides_num != 0 && ($N16Triglycerides_row->ResultsTestResults_num != '')) {
  if ($N16Triglycerides_row->ResultsTestResults_num > 0 && $N16Triglycerides_row->ResultsTestResults_num < 149.499) {
    $TValue16 = 0;
  } elseif ($N16Triglycerides_row->ResultsTestResults_num >= 149.5 && $N16Triglycerides_row->ResultsTestResults_num <= 199.499) {
    $TValue16 = 1;
  } elseif ($N16Triglycerides_row->ResultsTestResults_num >= 199.5 && $N16Triglycerides_row->ResultsTestResults_num <= 500.499) {
    $TValue16 = 2;
  } else {
    $TValue16 = 3;
  }
} else {
  
}


//
//
//<!--- # 17 - Diabetic ICD9 --->
//<cfset Variables.TDescription17 = 'Problem Reported: Diabetes ICD9'>
//<cfoutput>(#Variables.TDescription17#[17])</cfoutput>
//<cfif Variables.CheckICD9Codes neq 0>
//	<cfif N17DiabeticICD9.RecordCount eq 0>
//		- No ICD9 250.* (Diabetes) record found for the patient (Value = -1)<br>
//		<!---<cfset Variables.TValue17 = 0>--->
//	<cfelse>		
//		- One or More ICD9 250.* (Diabetes) record found for the patient (Value = 2)<br>
//		<cfset Variables.TValue17 = 2>
//	</cfif>
//<cfelse>
//	- We do not know if the patient had an ICD9 code for Diabetic (Value = -1)<br>		
//</cfif>
//

$TDescription17 = 'Problem Reported: Diabetes ICD9';
if ($CheckICD9Codes != 0) {
  if ($N17DiabeticICD9_num == 0) {
    $TValue17 = 0;
  } else {
    $TValue17 = 2;
  }
}


//
//
//<!--- # 18 - Socioeconomic Status --->
//
//
//<!--- # 19 - Oral Contraceptives --->
//<!--- Hidden per email from Pete on 17 July 2011 
//<cfset Variables.TDescription19 = 'Patient History: Oral Contraceptives'>
//<cfoutput>(#Variables.TDescription19#[19])</cfoutput>
//<cfif (Variables.HasNewHistoryId eq 1) AND (PatientProfileInfo.Sex eq 'F')>
//	<cfif N19OralContraceptives.Pill eq 1>
//		- Patient uses Oral Contraceptives (Value = 1)<br>
//		<cfset Variables.TValue19 = 1>
//	<cfelseif N19OralContraceptives.Pill eq 0>
//		- Patient does not use Oral Contraceptives (Value = -1)<br>
//		<!---<cfset Variables.TValue19 = 0>	--->
//	<cfelse>	
//		- We do not know if the patient uses Oral Contraceptives or not (Value = -1)<br>
//	</cfif>
//<cfelse>
//	- Either the patient is Male or does not have a history record(Value = -1)<br> 
//</cfif>
//--->
//
//<!--- # 20 - Hormone Replacement Therapy (HRT) --->
//<!--- Hidden per email from Pete on 17 July 2011 
//<cfset Variables.TDescription20 = 'Patient History: Hormone Replacement Therapy (HRT) ICD9'>
//<cfoutput>(#Variables.TDescription20#[20])</cfoutput>
//<cfif Variables.CheckICD9Codes neq 0>
//	<cfif N20HRTICD9.RecordCount eq 0>
//		- No ICD9 S5022, 86277, 84443, 83003, 83002, 82024, 80430 or 80428 (HRT) record found for the patient (Value = -1)<br>
//		<!---<cfset Variables.TValue20 = 0>--->
//	<cfelse>		
//		- One or More ICD9 S5022, 86277, 84443, 83003, 83002, 82024, 80430 or 80428 (HRT)  record found for the patient (Value = 1)<br>
//		<cfset Variables.TValue20 = 1>
//	</cfif>
//<cfelse>
//	- We do not know if the patient had HRT (Value = -1)<br>		
//</cfif>
//--->
//
//<!--- # 21 - Lipoprotein (a) --->
//<cfset Variables.TDescription21 = 'Lab Results: Lipoprotein (a)'>
//<cfoutput>(#Variables.TDescription21#[21])</cfoutput>
//<cfif N21Lipoprotein.Recordcount neq 0 AND Trim(N21Lipoprotein.ResultsTestResults_num) neq ''>
//	<cfif N21Lipoprotein.ResultsTestResults_num gt 0 AND N21Lipoprotein.ResultsTestResults_num lte 13.499>
//		- Lipoprotein (a) was less than 14 (Value = 0)<br>
//		<cfset Variables.TValue21 = 0>			
//	<cfelseif N21Lipoprotein.ResultsTestResults_num gte 13.5 AND N21Lipoprotein.ResultsTestResults_num lte 30.499>	
//		- Lipoprotein (a) was between 14 and 30 inclusive (Value = 1)<br>
//		<cfset Variables.TValue21 = 1>			
//	<cfelseif N21Lipoprotein.ResultsTestResults_num gte 30.5 AND N21Lipoprotein.ResultsTestResults_num lte 50.499>
//		- Lipoprotein (a) was between 31 and 50 inclusive (Value = 2)<br>
//		<cfset Variables.TValue21 = 2>			
//	<cfelse>
//		- Lipoprotein (a) was  greater than 50 (Value = 3)<br>
//		<cfset Variables.TValue21 = 3>			
//	</cfif>
//<cfelse>
//	- No Lipoprotein (a) lab test found (Value = -1) <br>
//</cfif>
//

$TDescription21 = 'Lab Results: Lipoprotein (a)';
if ($N21Lipoprotein_num != 0 && $N21Lipoprotein_row->ResultsTestResults_num != '') {
  if ($N21Lipoprotein_row->ResultsTestResults_num > 0 && $N21Lipoprotein_row->ResultsTestResults_num <= 13.499) {
    $TValue21 = 0;
  } elseif ($N21Lipoprotein_row->ResultsTestResults_num >= 13.5 && $N21Lipoprotein_row->ResultsTestResults_num <= 30.499) {
    $TValue21 = 1;
  } elseif ($N21Lipoprotein_row->ResultsTestResults_num >= 30.5 && $N21Lipoprotein_row->ResultsTestResults_num <= 50.499) {
    $TValue21 = 2;
  } else {
    $TValue21 = 3;
  }
}

//
//
//<!--- # 22 - Gender --->
//<cfset Variables.TDescription22 = 'Patient Vitals: Gender'>
//<cfoutput>(#Variables.TDescription22#[22])</cfoutput>
//<cfif PatientProfileInfo.Sex eq 'M'>
//	- Patient is Male (Value = 1)<br>
//	<cfset Variables.TValue22 = 1>
//<cfelseif PatientProfileInfo.Sex eq 'F'>
//	- Patient is Female (Value = 0)<br>
//	<cfset Variables.TValue22 = 0>
//<cfelse>
//	- We don't know if the patient is male or female (Value = -1)
//</cfif>	
//

$TDescription22 = 'Patient Vitals: Gender';
if ($PatientProfileInfo_row->Sex == 'M') {
  $TValue22 = 1;
} elseif ($PatientProfileInfo_row->Sex == 'F') {
  $TValue22 = 0;
} else {
  
}

//
//<!--- # 23 - Previous Heart Attack --->
//<cfset Variables.TDescription23 = 'Problem Reported: Previous Heart Attack ICD9'>
//<cfoutput>(#Variables.TDescription23#[23])</cfoutput>
//<cfif Variables.CheckICD9Codes neq 0>
//	<cfif N23PreviousHeartAttackICD9.RecordCount eq 0>
//		- No ICD9 410.*, 411.*,412.*,414.* (Previous Heart Attack) record found for the patient (Value = -1)<br>
//		<!---<cfset Variables.TValue23 = 0>--->
//	<cfelse>		
//		- One or More ICD9 410.*, 411.*,412.*,414.* (Previous Heart Attack) record found for the patient (Value = 2)<br>
//		<cfset Variables.TValue23 = 2>
//	</cfif>
//<cfelse>
//	- We do not know if the patient had a Previous Heart Attack (Value = -1)<br>		
//</cfif>
//
$TDescription23 = 'Problem Reported: Previous Heart Attack ICD9';
if ($CheckICD9Codes != 0) {
  if ($N23PreviousHeartAttackICD9_num == 0) {
    
  } else {
    $TValue23 = 2;
  }
} else {
  
}
//
//
//<!--- # 24 - Previous Stroke --->
//<cfset Variables.TDescription24 = 'Patient History: History of Stroke'>
//<cfoutput>(#Variables.TDescription24#[24])</cfoutput>
//<cfif Variables.HasNewHistoryId eq 1>
//	<cfif N24HistOfStroke.SmartControlAnswer eq 0>
//		- The patient did not have a Stroke (Value = -1)<br>
//		<!---<cfset Variables.TValue24 = 0>--->
//	<cfelseif N24HistOfStroke.SmartControlAnswer eq 2>
//		- The patient did have a Stroke (Value = 2)<br>
//		<cfset Variables.TValue22 = 1>
//	<cfelse>
//		- We do not know if the patient had a Stroke (Value = -1)<br>
//	</cfif>
//<cfelse>
//	- We do not know if the patient had a Stroke since they do not have a history record(Value = -1)<br>
//</cfif>
//

$TDescription24 = 'Patient History: History of Stroke';

if ($HasNewHistoryId == 1 && $N24HistOfStroke_row) {
  if ($N24HistOfStroke_row->SmartControlAnswer == 0) {
    
  } elseif ($N24HistOfStroke_row->SmartControlAnswer == 2) {
    $TValue22 = 1;
  } else {
    
  }
} else {
  
}
//
//<!--- #25 - Hx of Atrial Fibrillation or CHD or CF  --->
//<cfset Variables.TDescription25 = 'Patient History: Atrial Fibrillation or CHD or CF'>
//<cfoutput>(#Variables.TDescription25#[25])</cfoutput>
//<cfif (Variables.CheckICD9Codes eq 1) AND (N25HistoryOfAfibICD9.RecordCount neq 0) >
//	- Patient has one or more of either a history of CF and/or a history of 'Atrial Fibrillation (Value = 2)<br>
//	<cfset Variables.TValue25 = 2>	
//<cfelseif (Variables.HasNewHistoryId eq 1) AND (N25HistOfCHD.RecordCount neq 0) >
//	- Patient has a history of heart disease (Value = 2)<br>
//	<cfset Variables.TValue25 = 2>	
//<cfelseif (Variables.CheckICD9Codes eq 1) OR (Variables.HasNewHistoryId eq 1)>
//	- Patient has ICD9 Codes and/or New History info and no hits for Atrial Fibrillation, CHD or CF (Value = -1)<br>
//	<!---<cfset Variables.TValue25 = 0>--->	
//<cfelse>
//	- Patient has neither any ICD9 Codes nor do they have a new history so no data is possible for Atrial Fibrillation, CHD or CF (value = -1)<br>
//</cfif>
//
$TDescription25 = 'Patient History: Atrial Fibrillation or CHD or CF';
if ($CheckICD9Codes == 1 && $N25HistoryOfAfibICD9_num != 0) {
  $TValue25 = 2;
} elseif ($HasNewHistoryId == 1 && $N25HistoryOfAfibICD9_num != 0) {
  $TValue25 = 2;
} elseif ($CheckICD9Codes == 1 || $HasNewHistoryId == 1) {
  
} else {
  
}

//
//
//<!--- #26 - Waist Size  --->
//<cfset Variables.TDescription26 = 'Patient Vitals: Waist Size'>
//<cfoutput>(#Variables.TDescription26#[26])</cfoutput>
//<cfif Variables.WaistInch neq 0 AND Trim(PatientProfileInfo.Sex) neq ''>
//	<cfif PatientProfileInfo.Sex eq 'M'>
//		<cfif Variables.WaistInch lte 34.499>
//			- Male Waist less than or equal to 34 inches (Value = 0)<br>
//			<cfset Variables.TValue26 = 0>
//		<cfelseif Variables.WaistInch gte 34.5 AND Variables.WaistInch lte 38.499>
//			- Male Waist between 35 and 38 inches inclusive (Value = 1)<br>
//			<cfset Variables.TValue26 = 1>			
//		<cfelseif Variables.WaistInch gte 38.5 AND Variables.WaistInch lte 45.499>		
//			- Male Waist between 39 and 45 inches inclusive (Value = 2)<br>
//			<cfset Variables.TValue26 = 2>			
//		<cfelse>
//			- Male Waist greater than 45 inches (Value = 3)<br>
//			<cfset Variables.TValue26 = 3>		
//		</cfif>
//	<cfelseif PatientProfileInfo.Sex eq 'F'>	
//		<cfif Variables.WaistInch lte 30.499>
//			- Female Waist less than 30 inches (Value = 0)<br>
//			<cfset Variables.TValue26 = 0>
//		<cfelseif Variables.WaistInch gte 30.5 AND Variables.WaistInch lte 35.499>
//			- Female Waist between 31 and 35 inches inclusive (Value = 1)<br>
//			<cfset Variables.TValue26 = 1>			
//		<cfelseif Variables.WaistInch gte 35.5 AND Variables.WaistInch lte 40.499>		
//			- Female Waist between 36 and 40 inches inclusive (Value = 2)<br>
//			<cfset Variables.TValue26 = 2>			
//		<cfelse>
//			- Female Waist greater than 40 inches (Value = 3)<br>
//			<cfset Variables.TValue26 = 3>		
//		</cfif>
//	</cfif>
//<cfelse>
//	- We either do not know the sex of the patient or their weight (Value = -1)<br>	
//</cfif>
//

$TDescription26 = 'Patient Vitals: Waist Size';
if ($WaistInch != 0 && $PatientProfileInfo_row->Sex != '') {
  if ($PatientProfileInfo_row->Sex == 'M') {
    if ($WaistInch <= 34.499) {
      $TValue26 = 0;
    } elseif ($WaistInch >= 34.5 && $WaistInch <= 38.499) {
      $TValue26 = 1;
    } elseif ($WaistInch >= 38.5 && $WaistInch <= 45.499) {
      $TValue26 = 2;
    } else {
      $TValue26 = 3;
    }
  } elseif ($PatientProfileInfo_row->Sex == 'F') {
    if ($WaistInch <= 30.499) {
      $TValue26 = 0;
    } elseif ($WaistInch >= 30.5 && $WaistInch <= 35.499) {
      $TValue26 = 1;
    } elseif ($WaistInch >= 35.5 && $WaistInch <= 40.499) {
      $TValue26 = 2;
    } else {
      $TValue26 = 3;
    }
  }
} else {
  
}

//
//
//<!--- # 27 - PSA --->
//<!--- Hidden per email from Pete on 17 July 2011 
//<cfset Variables.TDescription27 = 'Lab Results: PSA'>
//<cfoutput>(#Variables.TDescription27#[27])</cfoutput>
//<cfif N27PSA.RecordCount gt 0>
//	<cfif N27PSA.ResultsTestResults_num lt 3.499>
//		- Patient had a PSA value less than 4 ng/ml (Value = 0)<br>
//		<cfset Variables.TValue27 = 0>
//	<cfelse>
//		- Patient had a PSA value equal to or greater than 4 ng/ml (Value = 2)<br>	
//		<cfset Variables.TValue27 = 2>	
//	</cfif>
//<cfelse>	
//		- Patient had no PSA Tests (Value = -1)<br>
//</cfif>		
//--->
//
//<!--- # 28 - Vasectomy --->
//<cfset Variables.TDescription28 = 'Patient History: Vasectomy'>
//<cfoutput>(#Variables.TDescription28#[28])</cfoutput>
//<cfif Variables.HasNewHistoryId eq 1>
//	<cfif N28Vasectomy.SmartControlAnswer eq 0>
//		- The patient did not have a Vasectomy (Value = 0)<br>
//		<cfset Variables.TValue28 = 0>
//	<cfelseif N28Vasectomy.SmartControlAnswer eq 1>
//		- The patient did have a Vasectomy (Value = 1)<br>
//		<cfset Variables.TValue28 = 1>
//	<cfelse>
//		- We do not know if the patient had a Vasectomy (Value = -1)<br>
//	</cfif>
//<cfelse>
//	- We do not know if the patient had a Vasectomy since they do not have a history record(Value = -1)<br>
//</cfif>
//

$TDescription28 = 'Patient History: Vasectomy';

if ($HasNewHistoryId == 1 && $N28Vasectomy_row) {
  if ($N28Vasectomy_row->SmartControlAnswer == 0) {
    $TValue28 = 0;
  } elseif ($N28Vasectomy_row->SmartControlAnswer == 1) {
    $TValue28 = 1;
  }
}

//
//
//<!--- # 29 - History of STD --->
//<cfset Variables.TDescription29 = 'Problem Reported: History of STD ICD9'>
//<cfoutput>(#Variables.TDescription29#[29])</cfoutput>
//<cfif Variables.CheckICD9Codes neq 0>
//	<cfif N29HistoryOfSTDICD9.RecordCount eq 0>
//		- No ICD9 597.*, V65.45 (History of STDs) record found for the patient (Value = 0)<br>
//		<cfset Variables.TValue29 = 0>
//	<cfelse>		
//		- One or More ICD9 597.*, V65.45 (History of STDs)  record found for the patient (Value = 2)<br>
//		<cfset Variables.TValue29 = 2>
//	</cfif>
//<cfelse>
//	- We do not know if the patient had a History of STDs (Value = -1)<br>	
//</cfif>
//
$TDescription29 = 'Problem Reported: History of STD ICD9';
if ($CheckICD9Codes != 0) {
  if ($N29HistoryOfSTDICD9_num == 0) {
    $TValue29 = 0;
  } else {
    $TValue29 = 2;
  }
} else {
  
}

//
//<!--- # 30 - Age --->
//<cfset Variables.TDescription30 = 'Patient Vitals: Age'>
//<cfoutput>(#Variables.TDescription30#[30])</cfoutput>
//<cfif Trim(PatientProfileInfo.DOB) neq ''>
//	<cfif Variables.Age lt 50>
//		- Patient is less than 50 years old (Value = 0)<br>
//		<cfset Variables.TValue30 = 0>
//	<cfelseif Variables.Age gte 50 AND Variables.Age lte 65>
//		- Patient is between 50 and 65 inclusive (Value = 1)<br>
//		<cfset Variables.TValue30 = 1>
//	<cfelse>
//		- Patient is over 65 (Value = 2)<br>
//		<cfset Variables.TValue30 = 2>
//	</cfif>
//<cfelse>
//	- We do not have a dob for the patient and do not know their age (Value = -1)<br>
//</cfif>		
//

$TDescription30 = 'Patient Vitals: Age';
if ($PatientProfileInfo_row->DOB != '') {
  if ($Age < 50) {
    $TValue30 = 0;
  } elseif ($Age >= 50 && $Age <= 65) {
    $TValue30 = 1;
  } else {
    $TValue30 = 2;
  }
}

//
//<!--- # 31 - Family History of Colorectal cancer --->
//<cfset Variables.TDescription31 = 'Family History: Colorectal Cancer'>
//<cfoutput>(#Variables.TDescription31#[31])</cfoutput>
//<cfif Variables.HasFamilyHistory eq 1>
//	<cfif N31FamilyHistColorectalCancer.RecordCount eq 0>
//		- No family members gave a yes answer to Colorectal Cancer question (Value = 0)<br>
//		<cfset Variables.TValue31 = 0>			
//	<cfelse>
//		- One or more family members said yes to having a history of Colorectal Cancer (value = 2) <br>
//		<cfset Variables.TValue31 = 2>							
//	</cfif>
//<cfelse>
//	- The patient has No family members records (Value = -1)<br>
//</cfif>
//

$TDescription31 = 'Family History: Colorectal Cancer';
if ($HasFamilyHistory == 1) {
  if ($N31FamilyHistColorectalCancer_num == 0) {
    $TValue31 = 0;
  } else {
    $TValue31 = 2;
  }
} else {
  
}
//
//
//<!--- # 32 - Inflammatory bowel disease --->
//<cfset Variables.TDescription32 = 'Problem Reported: Inflammatory Bowel Disease ICD9'>
//<cfoutput>(#Variables.TDescription32#[32])</cfoutput>
//<cfif Variables.CheckICD9Codes neq 0>
//	<cfif N32InflamBowelDisICD9.RecordCount eq 0>
//		- No ICD9 564.1, 564.81 (Inflammatory bowel disease) record found for the patient (Value = -1)<br>
//		<!---<cfset Variables.TValue32 = 0>--->
//	<cfelse>		
//		- One or More 564.1, 564.81 (Inflammatory bowel disease)   record found for the patient (Value = 2)<br>
//		<cfset Variables.TValue32 = 2>
//	</cfif>
//<cfelse>
//	- We do not know if the patient had a Inflammatory bowel disease (Value = -1)<br>	
//</cfif>
//

$TDescription32 = 'Problem Reported: Inflammatory Bowel Disease ICD9';
if ($CheckICD9Codes != 0) {
  if ($N32InflamBowelDisICD9_num == 0) {
    
  } else {
    $TValue32 = 2;
  }
} else {
  
}

//
//
//<!--- # 33 - Ulcerative Colitis --->
//<cfset Variables.TDescription33 = 'Problem Reported: Ulcerative Colitis ICD9'>
//<cfoutput>(#Variables.TDescription33#[33])</cfoutput>
//<cfif Variables.CheckICD9Codes neq 0>
//	<cfif N33UlcerColitisICD9.RecordCount eq 0>
//		- No ICD9 556.*, 558.* (Ulcerative Colitis) record found for the patient (Value = -1)<br>
//		<!---<cfset Variables.TValue33 = 0>--->
//	<cfelse>		
//		- One or More 556.*, 558.* (Ulcerative Colitis) record found for the patient (Value = 2)<br>
//		<cfset Variables.TValue33 = 2>
//	</cfif>
//<cfelse>
//	- We do not know if the patient had an Ulcerative Colitis (Value = -1)<br>	
//</cfif>
//

$TDescription33 = 'Problem Reported: Ulcerative Colitis ICD9';

if ($CheckICD9Codes != 0) {
  if ($N33UlcerColitisICD9_num == 0) {
    
  } else {
    $TValue33 = 2;
  }
}
//
//<!--- # 34 - Crohns disease --->
//<cfset Variables.TDescription34 = 'Problem Reported: Crohns Disease ICD9'>
//<cfoutput>(#Variables.TDescription34#[34])</cfoutput>
//<cfif Variables.CheckICD9Codes neq 0>
//	<cfif N34CrohnsICD9.RecordCount eq 0>
//		- No ICD9 555.9 (Crohns disease) record found for the patient (Value = -1)<br>
//		<!---<cfset Variables.TValue34 = 0>--->
//	<cfelse>		
//		- One or More  555.9 (Crohns disease) record found for the patient (Value = 2)<br>
//		<cfset Variables.TValue34 = 2>
//	</cfif>
//<cfelse>
//	- We do not know if the patient had Crohns disease (Value = -1)<br>	
//</cfif>
//

$TDescription34 = 'Problem Reported: Crohns Disease ICD9';

if ($CheckICD9Codes != 0) {
  if ($N34CrohnsICD9_num == 0) {
    
  } else {
    $TValue34 = 2;
  }
}

//
//<!--- # 35 - Colon cancer  --->
//<cfset Variables.TDescription35 = 'Problem Reported: Colon Cander ICD9'>
//<cfoutput>(#Variables.TDescription35#[35])</cfoutput>
//<cfif Variables.CheckICD9Codes neq 0>
//	<cfif N35ColonCancerICD9.RecordCount eq 0>
//		- No ICD9 153.*, 209.* (Colon cancer) record found for the patient (Value = -1)<br>
//		<!---<cfset Variables.TValue35 = 0>--->
//	<cfelse>		
//		- One or More  153.*, 209.* (Colon cancer)  record found for the patient (Value = 2)<br>
//		<cfset Variables.TValue35 = 2>
//	</cfif>
//<cfelse>
//	- We do not know if the patient had Colon cancer (Value = -1)<br>	
//</cfif>
//
$TDescription35 = 'Problem Reported: Colon Cander ICD9';
if ($CheckICD9Codes != 0) {
  if ($N35ColonCancerICD9_num == 0) {
    
  } else {
    $TValue35 = 2;
  }
}
//
//
//<!--- # 36 - Family History of Prostate Cancer  --->
//<cfset Variables.TDescription36 = 'Family History: Prostate Cancer'>
//<cfoutput>(#Variables.TDescription36#[36])</cfoutput>
//<cfif Variables.HasFamilyHistory eq 1>
//	<cfif N36FamilyHistProstateCancer.RecordCount eq 0>
//		- neither Father nor any brothers gave a yes answer to prostate Cancer question (Value = -1)<br>
//		<!---<cfset Variables.TValue36 = 0>--->			
//	<cfelse>
//		- Prostate Cancer - Father or one or more brothers reports having Prostate Cancer (value = 2) <br>
//		<cfset Variables.TValue36 = 2>							
//	</cfif>
//<cfelse>
//		- The patient has No family members records (Value = -1)<br>
//</cfif>
//

$TDescription36 = 'Family History: Prostate Cancer';
if ($HasFamilyHistory == 1) {
  if ($N36FamilyHistProstateCancer_num == 0) {
    
  } else {
    $TValue36 = 2;
  }
} else {
  
}

//
//<!--- # 37 - Prostatitis  --->
//<cfset Variables.TDescription37 = 'Problem Reported: Prostatitis ICD9'>
//<cfoutput>(#Variables.TDescription37#[37])</cfoutput>
//<cfif Variables.CheckICD9Codes neq 0>
//	<cfif N37ProstatitisICD9.RecordCount eq 0>
//		- No ICD9 098.*,131.*,601.* (Prostatitis) record found for the patient (Value = -1)<br>
//		<!---<cfset Variables.TValue37 = 0>--->
//	<cfelse>		
//		- One or More  098.*,131.*,601.* (Prostatitis)  record found for the patient (Value = 2)<br>
//		<cfset Variables.TValue37 = 2>
//	</cfif>
//<cfelse>
//	- We do not know if the patient had Prostatitis (Value = -1)<br>	
//</cfif>
//

$TDescription37 = 'Problem Reported: Prostatitis ICD9';
if ($CheckICD9Codes != 0) {
  if ($N37ProstatitisICD9_num == 0) {
    
  } else {
    $TValue37 = 2;
  }
}

//
//<!--- # 38 - Gender as it applies to Depression (Gender2) --->
//<cfset Variables.TDescription38 = 'Patient Vitals: Gender as it applies to Depression'>
//<cfoutput>(#Variables.TDescription38#[38])</cfoutput>
//<cfif PatientProfileInfo.Sex eq 'M'>
//	- Patient is Male (Value = 0)<br>
//	<cfset Variables.TValue38 = 0>
//<cfelseif PatientProfileInfo.Sex eq 'F'>
//	- Patient is Female (Value = 1)<br>
//	<cfset Variables.TValue38 = 1>
//<cfelse>
//	- We don't know if the patient is male or female (Value = -1)
//</cfif>	
//
$TDescription38 = 'Patient Vitals: Gender as it applies to Depression';

if ($PatientProfileInfo_row->Sex == 'M') {
  $TValue38 = 0;
} elseif ($PatientProfileInfo_row->Sex == 'F') {
  $TValue38 = 1;
} else {
  
}

//
//
//<!--- # 39 - Age as it applies to Prostate Cancer (Age2)  --->
//<cfset Variables.TDescription39 = 'Patient Vitals: Age as it applies to Prostate Cancer (Age2)'>
//<cfoutput>(#Variables.TDescription39#[39])</cfoutput>
//<cfif Trim(PatientProfileInfo.DOB) neq '' AND PatientProfileInfo.Sex eq 'M'>
//	<cfif Variables.Age lte 50>
//		- Patient is less than 50 years old (Value = 1)<br>
//		<cfset Variables.TValue39 = 1>
//	<cfelse>
//		- Patient is over 50 (Value = 0)<br>
//		<cfset Variables.TValue39 = 0>
//	</cfif>
//<cfelse>
//	- We do not have a dob for the patient and do not know their age and/or they are not male (Value = -1)<br>
//</cfif>		
//

$TDescription39 = 'Patient Vitals: Age as it applies to Prostate Cancer (Age2)';

if ($PatientProfileInfo_row->DOB != '' && $PatientProfileInfo_row->Sex == 'M') {
  if ($Age <= 50) {
    $TValue39 = 1;
  } else {
    $TValue39 = 0;
  }
} else {
  
}

//
//
//
//
//
//<!--- Do Insertion of Records --->
//<cfloop from="1" to="39" index="idx">
//	<cfset DesOut = evaluate("Variables.TDescription#idx#") >
//	<cfset ValOut = evaluate("Variables.TValue#idx#") >
//	<cfset RiskMasterID = evaluate("#idx#")>
//
//	<cftransaction action="begin">
//		<cftry>
//
//<!--- This is now done elsewhere...
//			
//			<!--- Check and see if a record for this encounter and AWACSRiskMaster_ID exist  --->
//			<cfquery datasource="#Variables.EMRDataSource#" name="FindAnyOldInfoRecords">
//				SELECT 	*
//				FROM 	AWACSInput
//				WHERE	AWACSInput.Patient_id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.PATIENT_ID#">
//						AND AWACSInput.ORG_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">
//						AND AWACSInput.Encounter_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#variables.Encounter_id#">
//						AND AWACSInput.AWACSRiskMaster_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#RiskMasterID#" >
//						AND (AWACSInput.Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> OR AWACSInput.Hidden is Null)
//			</cfquery>
//	
//			<!--- Had any existing records for this encounter and AWACSRiskMaster_ID--->
//			<cfif FindAnyOldInfoRecords.RecordCount neq 0>
//				<cfoutput query="FindAnyOldInfoRecords">
//					<cfquery datasource="#Variables.EMRDataSource#" name="HideOldInfoRecords">
//						UPDATE	AWACSInput
//						SET		Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="1">,
//								DateHidden = <cfqueryparam cfsqltype="CF_SQL_TIMESTAMP" value="#CreateODBCDateTime(Now())#">
//						WHERE	AWACSInput_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#FindAnyOldInfoRecords.AWACSInput_ID#">
//					</cfquery>
//				</cfoutput> 
//			</cfif>
//--->
//			<cfif ValOut neq '-1'>				
//				<!--- Add the new Record--->
//				<cfquery datasource="#Variables.EMRDataSource#" name="AddNewAWACSMedicalRecord">	
//					INSERT INTO AWACSInput
//							(
//								Org_ID,
//								Patient_ID,
//								Encounter_ID,
//								AWACSRiskMaster_ID,
//								Description,
//								DataValue
//							)	
//					VALUES	(
//								<cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.ORG_ID#">,
//								<cfqueryparam cfsqltype="cf_sql_bigint" value="#PatientProfileInfo.PATIENT_ID#">,
//								<cfqueryparam cfsqltype="cf_sql_bigint" value="#variables.Encounter_id#">,
//								<cfqueryparam cfsqltype="cf_sql_bigint" value="#RiskMasterID#" >,
//								<cfqueryparam cfsqltype="cf_sql_varchar" value="#DesOut#">,
//								<cfqueryparam cfsqltype="cf_sql_bigint" value="#ValOut#">
//							)	
//				</cfquery>	
//			</cfif>		
//
//			<cftransaction action="commit" />
//			<cfcatch type="any">
//				<cftransaction action="rollback" />
//				<cfoutput>
//					<b>Error</b><br>
//	       			#cfcatch.Message#
//   					#cfcatch.Detail#
//	    		</cfoutput>
//			</cfcatch>
//		</cftry>
//	</cftransaction>		
//</cfloop>	
//

for ($i = 1; $i <= 39; $i++) {
  $DesOut = 'TDescription' . $i;
  $ValOut = 'TValue' . $i;
  $RiskMasterID = $i;


  if ($$ValOut != -1) {
   
    $sql = "INSERT INTO AWACSInput
							(
								Org_ID,
								Patient_ID,
								Encounter_ID,
								AWACSRiskMaster_ID,
								Description,
								DataValue
							)	
					VALUES	(
								$PatientProfileInfo_row->Org_ID,
								$PatientProfileInfo_row->Patient_ID,
								$Encounter_Id,
								$RiskMasterID,
								'" . $$DesOut . "',
								'" . $$ValOut . "'
							)	";
    $this->ReportModel->data_db->trans_begin();
    $AddNewAWACSMedicalRecord = $this->ReportModel->data_db->query($sql);
    $this->ReportModel->data_db->trans_commit();
  }
}
?>