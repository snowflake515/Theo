<?php

//<!--- Compute the average to get the Risk --->
//<cfset variables.WeightedAverage = variables.DataValueSum / variables.DataValueCount>
//
//
$WeightedAverage = ($DataValueSum != 0)? ($DataValueSum / $DataValueCount) : 0;
//
//<cfset variables.severity = fix(variables.WeightedAverage)>
//<cfif variables.severity GT 3>
//	<cfset variables.severity = 3>
//<cfelseif variables.severity LT 0>
//	<cfset variables.severity = 0>
//</cfif>
//

$severity = floor($WeightedAverage); 

if($severity > 3){
  $severity = 3;
}elseif($severity < 0){
  $severity = 0;
}

//
//<!--- Find the proper AWACSSeverityMaster row --->
//<cfquery datasource="#Variables.EMRDataSource#" name="getAWACSSeverity">
//	Select top 1
//		AWACSSeverityMaster_Id,
//		Description,
//		SeverityDescription
//	From ecastmaster.dbo.AWACSSeverityMaster
//	Where AWACSDiseaseMaster_Id = <cfqueryparam cfsqltype="cf_sql_bigint" value="#variables.AWACSDiseaseMaster_ID#">
//		And Severity = <cfqueryparam cfsqltype="cf_sql_integer" value="#variables.Severity#">
//		And Hidden = <cfqueryparam cfsqltype="cf_sql_bit" value="0">
//</cfquery>
//
$sql = "	Select top 1
		AWACSSeverityMaster_Id,
		Description,
		SeverityDescription
	From ecastmaster.dbo.AWACSSeverityMaster
	Where AWACSDiseaseMaster_Id = $AWACSDiseaseMaster_ID
		And Severity = $severity
		And Hidden = 0";

$getAWACSSeverity = $this->ReportModel->data_db->query($sql);
$getAWACSSeverity_num = $getAWACSSeverity->num_rows();
$getAWACSSeverity_row = $getAWACSSeverity->row();

//
//<!--- Write info to AWACSResults --->
//<cfif getAWACSSeverity.RecordCount NEQ 0>
//	<cfquery datasource="#Variables.EMRDataSource#" name="putAWACSResults">
//		Insert Into AWACSResults (
//			Org_Id,
//			Patient_Id,
//			Encounter_Id,
//			AWACSSeverityMaster_Id,
//			Description,
//			SeverityDescription,
//			DateCreated,
//			WeightedAverage)
//		Values (
//			<cfqueryparam cfsqltype="cf_sql_bigint" value="#variables.Org_Id#">,
//			<cfqueryparam cfsqltype="cf_sql_bigint" value="#variables.Patient_Id#">,
//			<cfqueryparam cfsqltype="cf_sql_bigint" value="#variables.Encounter_Id#">,
//			<cfqueryparam cfsqltype="cf_sql_bigint" value="#getAWACSSeverity.AWACSSeverityMaster_Id#">,
//			<cfqueryparam cfsqltype="cf_sql_varchar" value="#getAWACSSeverity.Description#">,
//			<cfqueryparam cfsqltype="cf_sql_varchar" value="#getAWACSSeverity.SeverityDescription#">,
//			GetDate(),
//			<cfqueryparam cfsqltype="cf_sql_decimal" scale="5"  value="#Variables.WeightedAverage#">)
//	</cfquery>
//</cfif>
//

if($getAWACSSeverity_num != 0){
  $sql ="		Insert Into AWACSResults (
			Org_Id,
			Patient_Id,
			Encounter_Id,
			AWACSSeverityMaster_Id,
			Description,
			SeverityDescription,
			DateCreated,
			WeightedAverage)
		Values (
			$Org_Id,
			$Patient_Id,
			$Encounter_Id,
			$getAWACSSeverity_row->AWACSSeverityMaster_Id,
			'$getAWACSSeverity_row->Description',
			'$getAWACSSeverity_row->SeverityDescription',
			GetDate(),
			$WeightedAverage)";
  
    $this->ReportModel->data_db->trans_begin();
    $this->ReportModel->data_db->query($sql);
    $this->ReportModel->data_db->trans_commit();
}

?>