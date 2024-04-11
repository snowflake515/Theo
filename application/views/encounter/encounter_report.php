<?php

//==========================================================
// This form encounterPreview.cfm
//========================================================= 
// 
//
//
//
////<!---
//@Author 
//	David Henry 07/29/2008
//@Description
//	Generate encounter chart notes if encounter is unlocked
//	Show encoutner chart notes if encounter is locked, do not regenerate		
//								
//--->
//<cfsilent>
//	<cflock scope="Session" type="EXCLUSIVE" timeout="10">
//		<cfset Variables.sPatientId = Session.Patient_Id />
//		<cfset Variables.sProviderId = Session.Provider_Id />
//		<cfset Variables.sDeptId = Session.Dept_Id />
//	</cflock>
//
//	<cfquery datasource="#Variables.EMRDataSource#" Name="SelectEncounter">
//	 Select EH.EncounterDate,
//	        EH.Provider_Id,
//			EH.Dept_Id,
//			EH.Patient_Id,
//			EH.EncounterDescription_ID,
//			EH.ChiefComplaint,
//			EH.Encounter_Id,
//			(Select TOP 1 'YES'  from #Variables.DSNPrefix#eCastEMR_Images.dbo.EncounterDocuments ED where ED.Encounter_id=EH.Encounter_Id) as ZIPFILE
//	   FROM EncounterHistory EH
//	  Where EH.Encounter_ID = <cfqueryparam cfsqltype="CF_SQL_INTEGER" value="#url.Encounter_Id#">
//	</cfquery>
//</cfsilent>
//
//


$sql = "Select EH.EncounterDate,
	    EH.Provider_Id,
			EH.Dept_Id,
			EH.Patient_Id,
			EH.EncounterDescription_ID,
			EH.ChiefComplaint,
			EH.Encounter_Id,
			(Select TOP 1 'YES'  from ".$image_db.".dbo.EncounterDocuments ED where ED.Encounter_id=EH.Encounter_Id) as ZIPFILE
	   FROM ".$data_db.".dbo.EncounterHistory EH
	   Where EH.Encounter_ID = $id";

$query = $this->ReportModel->data_db->query($sql);
$get_dt = $query->row();


//
//<cf_showloading message="Loading..."/>
//
//
//<cfif SelectEncounter.ZIPFILE EQ "YES">
//	<cfoutput>
//		<iframe src="encounterDocuments.cfm?mode=view&encounterId=#url.encounter_Id#" height="100%" width="100%"></iframe>
//	</cfoutput>
//<cfelse>
//	<cfset url.primarykey = SelectEncounter.Encounter_Id />
//	<cfset url.providerkey = SelectEncounter.Provider_Id />
//	<cfset url.deptkey = SelectEncounter.Dept_Id />
//	<cfset url.patientkey = SelectEncounter.Patient_Id />
//	<cfset url.datekey = SelectEncounter.EncounterDate />
//	<cfset url.EncounterDescriptionKey= SelectEncounter.EncounterDescription_Id />
//	<cfset url.EncounterId = SelectEncounter.Encounter_Id />
//	<cfinclude template="printchartnotes.cfm">
//</cfif> 
//

if ($get_dt->ZIPFILE == "YES") {
  //something iframe encounterDocuments.cfm direct load here 
} else {
  $data['id'] = $id;
  $this->load->view('encounter/printchartnotes', $data);
}
?>