<?php 
//
//<!--- AWACSResults-SeverityLoad.cfm --->
//
//<!--- The following is from AWACS_RptMenu.cfm --->
//<cfset variables.Encounter_Id = GetEncounterList.Encounter_ID>  
//<cfset variables.Org_Id = GetEncounterList.Org_ID>
//<cfset variables.Patient_Id = GetEncounterList.Patient_ID>
//
//
$Encounter_Id = $Encounter_dt->Encounter_ID;
$Org_Id = ($Encounter_dt->Org_ID) ? $Encounter_dt->Org_ID : $current_user->Org_Id;
$Patient_Id = $Encounter_dt->Patient_ID;

$data['Encounter_Id'] = $Encounter_Id;
$data['Org_Id'] = $Org_Id;
$data['Patient_Id'] = $Patient_Id;

//
//<!--- We need to hide all current AWACS Results rows for this table --->
//<cfquery datasource="#Variables.EMRDataSource#" name="HideAWACSResults">
//	Update AWACSResults
//	Set Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="1">,
//		DateHidden = GetDate() 
//	Where Encounter_Id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#variables.Encounter_id#">
//		and isnull(Hidden, 0) = <cfqueryparam cfsqltype="cf_sql_bit" value="0"> 
//</cfquery>
//

$sql ="	Update AWACSResults
	  Set Hidden = 1,
		DateHidden = GetDate() 
  	Where Encounter_Id = $Encounter_Id
		and isnull(Hidden, 0) = 0";

$this->ReportModel->data_db->trans_begin();
$HideAWACSResults = $this->ReportModel->data_db->query($sql);
$this->ReportModel->data_db->trans_commit();

//
//<!--- AWACSInput info --->
//<cfquery datasource="#Variables.EMRDataSource#" name="GetAWACSInput">
//	Select 
//		ad.AWACSDiseaseMaster_ID as AWACSDiseaseMaster_ID,
//		ad.displayname,
//		ai.description,
//		ai.datavalue,
//		arm.weight
//	From eCastMaster.dbo.AWACSRiskMap arm 
//	Join eCastMaster.dbo.AWACSDiseaseMaster ad
//		on ad.AWACSDiseaseMaster_ID = arm.AWACSDiseaseMaster_ID
//	Left Join ecastmaster.dbo.tbotmaster t
//		on t.tbotmaster_ID = arm.tbotmaster_ID
//	Join awacsinput ai
//		on ai.tbotmaster_ID = t.tbotmaster_ID
//	Inner Join PatientProfile pp
//		on pp.patient_id = ai.patient_id
//	Where ai.encounter_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#variables.Encounter_id#"> 
//		AND isnull(ai.hidden, 0) = <cfqueryparam cfsqltype="cf_sql_bit" value="0">
//		And ((ad.awacsdiseasemaster_id <> <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="16">) or (pp.sex = <cfqueryparam cfsqltype="CF_SQL_CHAR" value="M">))
//	
//	UNION
//	
//	Select 
//		ad.AWACSDiseaseMaster_ID as AWACSDiseaseMaster_ID,
//		ad.displayname,
//		ai.description,
//		ai.datavalue,
//		arm.weight
//	From eCastMaster.dbo.AWACSRiskMap arm 
//	Join eCastMaster.dbo.AWACSDiseaseMaster ad
//		on ad.AWACSDiseaseMaster_ID = arm.AWACSDiseaseMaster_ID
//	Left Join eCastMaster.dbo.AWACSRiskMaster ar
//		on ar.awacsriskmaster_ID = arm.awacsriskmaster_ID
//	Join awacsinput ai
//		on ai.awacsriskmaster_ID = ar.awacsriskmaster_ID
//	Inner Join PatientProfile pp
//		on pp.patient_id = ai.patient_id
//	Where ai.encounter_ID = <cfqueryparam cfsqltype="cf_sql_bigint" value="#variables.Encounter_id#"> 
//		AND isnull(ai.hidden, 0) = <cfqueryparam cfsqltype="cf_sql_bit" value="0">
//		And ((ad.awacsdiseasemaster_id <> <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="16">) or (pp.sex = <cfqueryparam cfsqltype="CF_SQL_CHAR" value="M">))
//
//	Order By AWACSDiseaseMaster_ID
//</cfquery>
//
//

$sql ="	Select 
		ad.AWACSDiseaseMaster_ID as AWACSDiseaseMaster_ID,
		ad.displayname,
		ai.description,
		ai.datavalue,
		arm.weight
	From ".$master_db.".dbo.AWACSRiskMap arm 
	Join ".$master_db.".dbo.AWACSDiseaseMaster ad
		on ad.AWACSDiseaseMaster_ID = arm.AWACSDiseaseMaster_ID
	Left Join ".$master_db.".dbo.tbotmaster t
		on t.tbotmaster_ID = arm.tbotmaster_ID
	Join ".$data_db.".dbo.awacsinput ai
		on ai.tbotmaster_ID = t.tbotmaster_ID
	Inner Join ".$data_db.".dbo.PatientProfile pp
		on pp.patient_id = ai.patient_id
	Where ai.encounter_ID = $Encounter_Id
		AND isnull(ai.hidden, 0) = 0
		And ((ad.awacsdiseasemaster_id <> 16) or (pp.sex = 'M'))
	
	UNION
	
	Select 
		ad.AWACSDiseaseMaster_ID as AWACSDiseaseMaster_ID,
		ad.displayname,
		ai.description,
		ai.datavalue,
		arm.weight
	From ".$master_db.".dbo.AWACSRiskMap arm 
	Join ".$master_db.".dbo.AWACSDiseaseMaster ad
		on ad.AWACSDiseaseMaster_ID = arm.AWACSDiseaseMaster_ID
	Left Join ".$master_db.".dbo.AWACSRiskMaster ar
		on ar.awacsriskmaster_ID = arm.awacsriskmaster_ID
	Join ".$data_db.".dbo.awacsinput ai
		on ai.awacsriskmaster_ID = ar.awacsriskmaster_ID
	Inner Join ".$data_db.".dbo.PatientProfile pp
		on pp.patient_id = ai.patient_id
	Where ai.encounter_ID = $Encounter_Id
		AND isnull(ai.hidden, 0) = 0
		And ((ad.awacsdiseasemaster_id <> 16) or (pp.sex = 'M'))
	Order By AWACSDiseaseMaster_ID";

$GetAWACSInput = $this->ReportModel->data_db->query($sql);
$GetAWACSInput_num = $GetAWACSInput->num_rows();
$GetAWACSInput_result = $GetAWACSInput->result();

//
//<cfset variables.AWACSDiseaseMaster_ID = 0>
//<cfset variables.DataValueSum = 0>
//<cfset variables.DataValueCount = 0>
//

$data['AWACSDiseaseMaster_ID']= 0;
$data['DataValueSum'] = 0;
$data['DataValueCount'] = 0;


//
//<cfoutput query="GetAWACSInput">
//	<cfif variables.AWACSDiseaseMaster_ID EQ 0>
//		<!--- Initialize --->
//		<cfset variables.AWACSDiseaseMaster_ID = GetAWACSInput.AWACSDiseaseMaster_ID>
//	</cfif>
//
//	<cfif variables.AWACSDiseaseMaster_ID NEQ GetAWACSInput.AWACSDiseaseMaster_ID>
//
//		<!--- Need to compute the average, find the Severity Master, and write to AWACSResults --->
//		<cfinclude template="AWACSResults-PutResults.cfm">
//	
//
//		<cfset variables.AWACSDiseaseMaster_ID = GetAWACSInput.AWACSDiseaseMaster_ID>
//		<cfset variables.DataValueSum = 0>
//		<cfset variables.DataValueCount = 0>
//
//	</cfif>
//
//	<!--- Process row --->	
//	<cfset variables.DataValueSum = variables.DataValueSum + (GetAWACSInput.DataValue * GetAWACSInput.Weight)>
//	<cfset variables.DataValueCount = variables.DataValueCount + 1>
//
//</cfoutput>
//



foreach ($GetAWACSInput_result as $GetAWACSInput_dt) {
  $data['GetAWACSInput_dt'] = $GetAWACSInput_dt;
  if($data['AWACSDiseaseMaster_ID'] == 0){
    $data['AWACSDiseaseMaster_ID'] = $GetAWACSInput_dt->AWACSDiseaseMaster_ID;
  }
  
  if($data['AWACSDiseaseMaster_ID'] != $GetAWACSInput_dt->AWACSDiseaseMaster_ID){
    $this->load->view('encounter/generate_report/AWACSResults-PutResults', $data);
    $data['AWACSDiseaseMaster_ID'] = $GetAWACSInput_dt->AWACSDiseaseMaster_ID;
    $data['DataValueSum'] = 0;
    $data['DataValueCount'] = 0;
  }
  
  $data['DataValueSum'] = $data['DataValueSum'] + ($GetAWACSInput_dt->datavalue * $GetAWACSInput_dt->weight);
  $data['DataValueCount'] = $data['DataValueCount'] + 1;
}

//
//
//<!--- Must write last entry --->
//<cfinclude template="AWACSResults-PutResults.cfm">
//
$this->load->view('encounter/generate_report/AWACSResults-PutResults', $data);
//


?>