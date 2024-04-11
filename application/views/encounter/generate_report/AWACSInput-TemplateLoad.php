<?php

//
//<!--- The following is from AWACS_RptMenu.cfm --->
//<cfset variables.Encounter_Id = GetEncounterList.Encounter_ID>
//<cfset variables.Org_Id = GetEncounterList.Org_ID>
//<cfset variables.Patient_Id = GetEncounterList.Patient_ID>
//
//
$Encounter_Id = $Encounter_dt->Encounter_ID;
$Org_Id = $Encounter_dt->Org_ID;
$Patient_Id = $Encounter_dt->Patient_ID;

//
//<!---
//<cfquery datasource="#Variables.EMRDataSource#" name="CheckEncDescList">
//	Select top 1
//		isnull(AWACS, 0) as AWACS
//	From EncounterDescriptionList
//	Where EncounterDescription_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Form.EncounterDescription_Id#">
//</cfquery>
//
//<cfif CheckEncDescList.RecordCount NEQ 0>
//	<cfif CheckEncDescList.AWACS EQ 1>
//--->
//
//<!--- This is now done elsewhere...
//
//		<!--- Start by hidding all existing records for this Encounter.  This should only happen if the Encounter gets unlocked. --->
//		<cfquery datasource="#Variables.EMRDataSource#" name="GetAWACSInput">
//			Update AWACSInput
//			Set Hidden = <cfqueryparam cfsqltype="CF_SQL_BIT" value="1">,
//				DateHidden = getdate()
//			Where (Encounter_Id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#Form.EncounterId#">)
//				AND (isnull(Hidden, 0) = <cfqueryparam cfsqltype="CF_SQL_BIT" value="0">)
//		</cfquery>
//--->
//
//		<!--- Now find TBot risk data that needs to be written to AWACSInput --->
//		<cfquery datasource="#Variables.EMRDataSource#" name="GetTemplateData">
//			SELECT
//				t3.TML3_TbotMaster_ID,
//				t3.TML3_TbotData,
//				tm.TBotType
//			FROM ETL3 AS e3
//			INNER JOIN #Variables.TemplateDataSource#.dbo.TML3 AS t3
//    			 ON t3.TML3_ID = e3.TML3_Id
//			INNER JOIN ecastmaster.dbo.TBotMaster AS tm
//			     ON t3.TML3_TbotMaster_ID = tm.TBotMaster_ID
//			JOIN ecastmaster.dbo.awacsriskmap as arm
//				on t3.tml3_tbotmaster_ID = arm.tbotmaster_ID
//			WHERE (e3.Encounter_Id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#variables.Encounter_Id#">) AND (isnull(t3.Hidden, 0) <> <cfqueryparam cfsqltype="CF_SQL_BIT" value="1">) AND (t3.TML3_TbotMaster_ID IS NOT NULL)
//			group by t3.TML3_TbotMaster_ID, t3.TML3_TbotData, tm.TBotType
//		</cfquery>

$sql = "			SELECT
				t3.TML3_TbotMaster_ID,
				t3.TML3_TbotData,
				tm.TBotType
			FROM ETL3 AS e3
			INNER JOIN $template_db.dbo.TML3 AS t3
    			 ON t3.TML3_ID = e3.TML3_Id
			INNER JOIN ecastmaster.dbo.TBotMaster AS tm
			     ON t3.TML3_TbotMaster_ID = tm.TBotMaster_ID
			JOIN ecastmaster.dbo.awacsriskmap as arm
				on t3.tml3_tbotmaster_ID = arm.tbotmaster_ID
			WHERE (e3.Encounter_Id = $Encounter_Id) AND (isnull(t3.Hidden, 0) <> 1) AND (t3.TML3_TbotMaster_ID IS NOT NULL)
			group by t3.TML3_TbotMaster_ID, t3.TML3_TbotData, tm.TBotType";


$GetTemplateData = $this->ReportModel->data_db->query($sql);
$GetTemplateData_num = $GetTemplateData->num_rows();
$GetTemplateData_result = $GetTemplateData->result();


//		<cfoutput query="GetTemplateData">
//			<cfquery datasource="#Variables.EMRDataSource#" name="UpdateAWACSInput">
//			Insert into AWACSInput (
//				Org_Id,
//				Patient_Id,
//				Encounter_Id,
//				TBotMaster_Id,
//				Description,
//				DataValue
//				)
//			Values (
//				<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#variables.Org_Id#">,
//				<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#variables.Patient_Id#">,
//				<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#variables.Encounter_Id#">,
//				<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#GetTemplateData.TML3_TbotMaster_ID#">,
//				<cfqueryparam cfsqltype="CF_SQL_VARCHAR" value="#GetTemplateData.TBotType#">,
//				<cfqueryparam cfsqltype="CF_SQL_VARCHAR" value="#GetTemplateData.TML3_TbotData#">
//				)
//			</cfquery>
//		</cfoutput>
//
//<!---
//	</cfif>
//</cfif>
//
//--->
//

foreach ($GetTemplateData_result as $GetTemplateData_dt) {
  $sql = "Insert into AWACSInput (
				Org_Id,
				Patient_Id,
				Encounter_Id,
				TBotMaster_Id,
				Description,
				DataValue
				)
			Values (
				$Org_Id,
				$Patient_Id,
				$Encounter_Id,
				$GetTemplateData_dt->TML3_TbotMaster_ID,
					'" .str_replace ("'","''", $GetTemplateData_dt->TBotType)."',
					'" .str_replace ("'","''", $GetTemplateData_dt->TML3_TbotData)."' 
				)";

  $this->ReportModel->data_db->trans_begin();
  $UpdateAWACSInput = $this->ReportModel->data_db->query($sql);
  $this->ReportModel->data_db->trans_commit();
}
?>
