<!--- Expects GetEncounterList to contain information of EncounterHistory row to process. --->

<!--- Set the initial processing status --->
<?php
//<cfquery datasource="#Variables.EMRDataSource#" name="SetProcessingStatus">
//	SET NoCount On;
//	UPDATE EncounterHistory
//	SET	AWACSStatus = <cfqueryparam cfsqltype="CF_SQL_INTEGER" value="10">
//	WHERE Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#GetEncounterList.Encounter_ID#">
//	SELECT UpdateCount = @@ROWCOUNT
//	SET NoCount Off;
//</cfquery>

$sql = "
	UPDATE EncounterHistory
	SET	AWACSStatus =  10
	WHERE Encounter_Id= $Encounter_Id ";
$this->ReportModel->data_db->trans_begin();
$SetProcessingStatus = $this->ReportModel->data_db->query($sql);
$this->ReportModel->data_db->trans_commit();
?>


<!---
    AND (AWACSStatus <> <cfqueryparam cfsqltype="CF_SQL_INTEGER" value="10">)
    AND (AWACSStatus <> <cfqueryparam cfsqltype="CF_SQL_INTEGER" value="99">)
--->
<?php
//
//<cfset variables.AWACSStatus = 99>
//
$AWACSStatus = 99;
//
//  <cfset variables.AWACSMessage = "">
//
//
$AWACSMessage = "";

//    <cfset variables.EncounterSignedOff = (GetEncounterList.encountersignedoff EQ True) and (GetEncounterList.SignedOffSupervising EQ True)>
//
//
$EncounterSignedOff = (($Encounter_dt->EncounterSignedOff == TRUE) && ($Encounter_dt->SignedOffSupervising == TRUE)) ? TRUE : FALSE;
//
//           <cfif SetProcessingStatus.UpdateCount NEQ 0>
if ($Encounter_dt) {

//          <!--- Delete Locked Encounter --->
//          <cfif variables.EncounterSignedOff EQ True>

if ($EncounterSignedOff == TRUE) {



//          <cfquery datasource="#Variables.ImageDataSource#" name="DelFile">
//            DELETE
//            FROM	EncounterDocuments
//            WHERE	Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#GetEncounterList.Encounter_ID#">
//          </cfquery>
//        </cfif>
$sql = "DELETE
            FROM	".$image_db.".dbo.EncounterDocuments
            WHERE	Encounter_Id= $Encounter_Id";

$this->ReportModel->data_db->trans_begin();
$DelFile = $this->ReportModel->data_db->query($sql);
$this->ReportModel->data_db->trans_commit();
}

//
//        <!--- Remove any existing AWACSInput data for this Encounter --->
//        <cfquery datasource="#Variables.EMRDataSource#" name="GetAWACSInput">
//          Update AWACSInput
//          Set Hidden = <cfqueryparam cfsqltype="CF_SQL_BIT" value="1">,
//            DateHidden = getdate()
//            Where (Encounter_Id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#GetEncounterList.Encounter_ID#">)
//              AND (isnull(Hidden, 0) = <cfqueryparam cfsqltype="CF_SQL_BIT" value="0">)
//        </cfquery>
//
$sql = "Update AWACSInput
           Set Hidden = 1,
            DateHidden = getdate()
           Where (Encounter_Id = $Encounter_Id)
             AND (isnull(Hidden, 0) = 0)";
$this->ReportModel->data_db->trans_begin();
$GetAWACSInput = $this->ReportModel->data_db->query($sql);
$this->ReportModel->data_db->trans_commit();

//
//
//                <!--- Load AWACSInput with Template T-Bot data --->
//                <cfinclude template="AWACSInput-TemplateLoad.cfm">
//
$this->load->view('encounter/generate_report/AWACSInput-TemplateLoad');

//
//                  <!--- Load AWACSInput with additional Risk info --->
//                  <cfinclude template="AWACSInput-MedicalLoad.cfm">
//
$this->load->view('encounter/generate_report/AWACSInput-MedicalLoad');
//                    <!--- Load Risk Factors --->
//                    <cfinclude template="AWACSResults-SeverityLoad.cfm">
//
$this->load->view('encounter/generate_report/AWACSResults-SeverityLoad');
//
//                      <!--- Create/Update Encounter Component Records --->
//                      <cfquery datasource="#Variables.EMRDataSource#" name="GetEncComp">
//                        SELECT top 1
//                        EncounterComponents_ID
//                        FROM EncounterComponents
//                        WHERE Encounter_Id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#GetEncounterList.Encounter_ID#">
//                          AND HeaderMaster_ID = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="147">
//                        </cfquery>
//

$sql = "SELECT top 1
                        EncounterComponents_ID
                        FROM EncounterComponents
                        WHERE Encounter_Id = $Encounter_Id
                          AND HeaderMaster_ID = 147";

$GetEncComp = $this->ReportModel->data_db->query($sql);
$GetEncComp_num = $GetEncComp->num_rows();
$GetEncComp_row = $GetEncComp->row();

//                            <cfif GetEncComp.RecordCount EQ 0>

if ($GetEncComp_num == 0) {



//
//                                  <cfquery name="InsertEncComp" datasource="#Variables.EMRDataSource#">
//                                Insert Into EncounterComponents (
//                                Patient_Id,
//                                Encounter_Id,
//                                EncounterDate,
//                                HeaderMaster_Id,
//                                ComponentKeys,
//                                DateCreated)
//                                VALUES (
//                                <cfqueryparam value="#GetEncounterList.Patient_ID#" cfsqltype="cf_sql_bigint">,
//                                  <cfqueryparam value="#GetEncounterList.Encounter_ID#" cfsqltype="cf_sql_bigint">,
//                                    <cfqueryparam value="#GetEncounterList.EncounterDate#" cfsqltype="CF_SQL_TIMESTAMP">,
//                                      <cfqueryparam value="147" cfsqltype="cf_sql_bigint">,
//                                        <cfqueryparam value="#GetEncounterList.Encounter_ID#" cfsqltype="cf_sql_varchar">,
//                                          GetDate())
//                                          </cfquery>
//
//
//
$sql = "
      Insert Into EncounterComponents (
              Patient_Id,
                               Encounter_Id,
                               EncounterDate,
                              HeaderMaster_Id,
                             ComponentKeys,
                               DateCreated)
                               VALUES (
                                $Encounter_dt->Patient_ID,
                                 $Encounter_Id,
                                   '$Encounter_dt->EncounterDate',  147,
                                       $Encounter_Id,
                                          GetDate())";
$this->ReportModel->data_db->trans_begin();
$InsertEncComp = $this->ReportModel->data_db->query($sql);
$this->ReportModel->data_db->trans_commit();
} else {
//
//                                          <cfelse>
//                                            <cfquery name="InsertEncComp" datasource="#Variables.EMRDataSource#">
//                                              Update EncounterComponents
//                                              Set ComponentKeys = <cfqueryparam value="#GetEncounterList.Encounter_ID#" cfsqltype="cf_sql_varchar">
//                                                Where EncounterComponents_ID = <cfqueryparam value="#GetEncComp.EncounterComponents_ID#" cfsqltype="cf_sql_bigint">
//                                                  </cfquery>
//
//                                                  </cfif>

$sql = "Update EncounterComponents
                                              Set ComponentKeys = $Encounter_Id
                                                Where EncounterComponents_ID = $GetEncComp_row->EncounterComponents_ID";
$this->ReportModel->data_db->trans_begin();
$InsertEncComp = $this->ReportModel->data_db->query($sql);
$this->ReportModel->data_db->trans_commit();
}


//
//                                                  <cfquery datasource="#Variables.EMRDataSource#" name="GetEncComp">
//                                                    SELECT top 1
//                                                    EncounterComponents_ID
//                                                    FROM EncounterComponents
//                                                    WHERE Encounter_Id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#GetEncounterList.Encounter_ID#">
//                                                      AND HeaderMaster_ID = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="148">
//                                                   </cfquery>
//

$sql = "SELECT top 1
                                                   EncounterComponents_ID
                                                   FROM EncounterComponents
                                                   WHERE Encounter_Id = $Encounter_Id
                                                     AND HeaderMaster_ID = 148";

$GetEncComp = $this->ReportModel->data_db->query($sql);
$GetEncComp_num = $GetEncComp->num_rows();
$GetEncComp_row = $GetEncComp->row();

//
//                                                        <cfif GetEncComp.RecordCount EQ 0>
//
if ($GetEncComp_num == 0) {




//                                                              <cfquery name="InsertEncComp" datasource="#Variables.EMRDataSource#">
//                                                            Insert Into EncounterComponents (
//                                                            Patient_Id,
//                                                            Encounter_Id,
//                                                            EncounterDate,
//                                                            HeaderMaster_Id,
//                                                            ComponentKeys,
//                                                            DateCreated)
//                                                            VALUES (
//                                                            <cfqueryparam value="#GetEncounterList.Patient_ID#" cfsqltype="cf_sql_bigint">,
//                                                              <cfqueryparam value="#GetEncounterList.Encounter_ID#" cfsqltype="cf_sql_bigint">,
//                                                                <cfqueryparam value="#GetEncounterList.EncounterDate#" cfsqltype="CF_SQL_TIMESTAMP">,
//                                                                  <cfqueryparam value="148" cfsqltype="cf_sql_bigint">,
//                                                                    <cfqueryparam value="#GetEncounterList.Encounter_ID#" cfsqltype="cf_sql_varchar">,
//                                                                      GetDate())
//                                                                      </cfquery>
//

$sql = " Insert Into EncounterComponents (
                                                          Patient_Id,
                                                           Encounter_Id,
                                                          EncounterDate,
                                                          HeaderMaster_Id,
                                                          ComponentKeys,
                                                           DateCreated)
                                                           VALUES (
                                                          $Encounter_dt->Patient_ID,
                                                          $Encounter_dt->Encounter_ID,
                                                             '$Encounter_dt->EncounterDate',
                                                                 148,
                                                                    $Encounter_dt->Encounter_ID,
                                                                     GetDate())";
$this->ReportModel->data_db->trans_begin();
$InsertEncComp = $this->ReportModel->data_db->query($sql);
$this->ReportModel->data_db->trans_commit();
} else {
//                                                                      <cfelse>
//                                                                        <cfquery name="InsertEncComp" datasource="#Variables.EMRDataSource#">
//                                                                          Update EncounterComponents
//                                                                          Set ComponentKeys = <cfqueryparam value="#GetEncounterList.Encounter_ID#" cfsqltype="cf_sql_varchar">
//                                                                            Where EncounterComponents_ID = <cfqueryparam value="#GetEncComp.EncounterComponents_ID#" cfsqltype="cf_sql_bigint">
//                                                                              </cfquery>
//                                                                              </cfif>
//
$sql = "Update EncounterComponents Set ComponentKeys = $Encounter_dt->Encounter_ID
           Where EncounterComponents_ID = $GetEncComp_row->EncounterComponents_ID
                                                                      ";
$this->ReportModel->data_db->trans_begin();
$InsertEncComp = $this->ReportModel->data_db->query($sql);
$this->ReportModel->data_db->trans_commit();
}
//                                                                              <cfquery datasource="#Variables.EMRDataSource#" name="GetEncComp">
//                                                                                SELECT top 1
//                                                                                EncounterComponents_ID
//                                                                                FROM EncounterComponents
//                                                                                WHERE Encounter_Id = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#GetEncounterList.Encounter_ID#">
//                                                                                  AND HeaderMaster_ID = <cfqueryparam cfsqltype="CF_SQL_BIGINT" value="149">
//                                                                               </cfquery>
//

$sql = "SELECT top 1
   EncounterComponents_ID
   FROM EncounterComponents
   WHERE Encounter_Id = $Encounter_dt->Encounter_ID
   AND HeaderMaster_ID = 149";
$GetEncComp = $this->ReportModel->data_db->query($sql);
$GetEncComp_num = $GetEncComp->num_rows();
$GetEncComp_row = $GetEncComp->row();

//
//                                                                                    <cfif GetEncComp.RecordCount EQ 0>
//
if ($GetEncComp_num == 0) {



//                                                                                          <cfquery name="InsertEncComp" datasource="#Variables.EMRDataSource#">
//                                                                                        Insert Into EncounterComponents (
//                                                                                        Patient_Id,
//                                                                                        Encounter_Id,
//                                                                                        EncounterDate,
//                                                                                        HeaderMaster_Id,
//                                                                                        ComponentKeys,
//                                                                                        DateCreated)
//                                                                                        VALUES (
//                                                                                        <cfqueryparam value="#GetEncounterList.Patient_ID#" cfsqltype="cf_sql_bigint">,
//                                                                                          <cfqueryparam value="#GetEncounterList.Encounter_ID#" cfsqltype="cf_sql_bigint">,
//                                                                                            <cfqueryparam value="#GetEncounterList.EncounterDate#" cfsqltype="CF_SQL_TIMESTAMP">,
//                                                                                              <cfqueryparam value="149" cfsqltype="cf_sql_bigint">,
//                                                                                                <cfqueryparam value="#GetEncounterList.Encounter_ID#" cfsqltype="cf_sql_varchar">,
//                                                                                                  GetDate())
//                                                                                                  </cfquery>
//                                                                                                  <cfelse>
//
$sql = " Insert Into EncounterComponents (
   Patient_Id,
   Encounter_Id,
   EncounterDate,
   HeaderMaster_Id,
   ComponentKeys,
   DateCreated)
   VALUES (
    $Encounter_dt->Patient_ID,
    $Encounter_dt->Encounter_ID,
    '$Encounter_dt->EncounterDate',
    149,
    $Encounter_dt->Encounter_ID,
    GetDate())";
$this->ReportModel->data_db->trans_begin();
$InsertEncComp = $this->ReportModel->data_db->query($sql);
$this->ReportModel->data_db->trans_commit();
} else {
//                                                                                                    <cfquery name="InsertEncComp" datasource="#Variables.EMRDataSource#">
//                                                                                                      Update EncounterComponents
//                                                                                                      Set ComponentKeys = <cfqueryparam value="#GetEncounterList.Encounter_ID#" cfsqltype="cf_sql_varchar">
//                                                                                                        Where EncounterComponents_ID = <cfqueryparam value="#GetEncComp.EncounterComponents_ID#" cfsqltype="cf_sql_bigint">
//                                                                                                          </cfquery>
//                                                                                                          </cfif>
$sql = " Update EncounterComponents
    Set ComponentKeys = $Encounter_dt->Encounter_ID
    Where EncounterComponents_ID = $GetEncComp_row->EncounterComponents_ID ";
$this->ReportModel->data_db->trans_begin();
$InsertEncComp = $this->ReportModel->data_db->query($sql);
$this->ReportModel->data_db->trans_commit();

}
//                                                                                                          <!--- Generate new Encounter with AWACS Tables --->
//                                                                                                          <cfif variables.EncounterSignedOff EQ True>
// #disabled this  if($Encounter_dt->EncounterSignedOff == TRUE){
//                                                                                                            <cfset URL.EncounterId = Trim(GetEncounterList.encounter_ID)>
//                                                                                                              <cfset URL.Mode = "">
//                                                                                                                <cfset URL.BatchJob = "1">

$data['EncounterId'] = $Encounter_dt->Encounter_ID;
$data['Mode'] = 'WRITE';
$data['BatchJob']= 1;
//                                                                                                            <!---			<cftry> --->
//                                                                                                                  <cfinclude template="EncounterDocuments.cfm">
//
$this->load->view('encounter/generate_report/EncounterDocuments', $data);
//                                                                                                                    <!--- Write record to AWACSReportList --->
//                                                                                                                    <cfquery name="putAWACSReportList" datasource="#Variables.EMRDataSource#">
//                                                                                                                      Insert Into AWACSReportList (
//                                                                                                                      Org_Id,
//                                                                                                                      Provider_Id,
//                                                                                                                      Patient_Id,
//                                                                                                                      Encounter_Id,
//                                                                                                                      ReportDate)
//                                                                                                                      VALUES (
//                                                                                                                      <cfqueryparam value="#GetEncounterList.Org_ID#" cfsqltype="cf_sql_bigint">,
//                                                                                                                        <cfqueryparam value="#GetEncounterList.Provider_ID#" cfsqltype="cf_sql_bigint">,
//                                                                                                                          <cfqueryparam value="#GetEncounterList.Patient_ID#" cfsqltype="cf_sql_bigint">,
//                                                                                                                            <cfqueryparam value="#GetEncounterList.Encounter_ID#" cfsqltype="cf_sql_bigint">,
//                                                                                                                              GetDate())
//                                                                                                                              </cfquery>

$sql = " Insert Into AWACSReportList (
         Org_Id,
         Provider_Id,
         Patient_Id,
         Encounter_Id,
         ReportDate)
         VALUES (
         $Encounter_dt->Org_ID,
         $Encounter_dt->Provider_ID ,
         $Encounter_dt->Patient_ID,
         $Encounter_dt->Encounter_ID,
         GetDate())";

$this->ReportModel->data_db->trans_begin();
$putAWACSReportList = $this->ReportModel->data_db->query($sql);
$this->ReportModel->data_db->trans_commit();
//                                                                                                                              <!---
//                                                                                                                                    <cfcatch type = "ANY">
//                                                                                                                                      <cfset variables.AWACSStatus = 80>
//                                                                                                                                      <cfset variables.AWACSMessage = "Failure creating Encounter Document">
//                                                                                                                                      <cfpop username="#Variables.PopSupportUser#" password="#Variables.PopSupportPassword#" action="GETHEADERONLY" server="#Variables.MailServer#" name="StartEncounterDocuments" maxrows="1">
//                                                                                                                                      <cfmail to="jyoung@ecastcorp.com" from="#Variables.PopSupportUser#" subject="Error AWACS Encounter Documents" server="#Variables.MailServer#" type="HTML">
//                                                                                                                                        Encounter_ID: #GetEncounterList.Encounter_ID#<BR><BR>
//                                                                                                                                        <cfif Debug NEQ 0>
//                                                                                                                                          <cfdump var="#cfcatch#">
//                                                                                                                                        <cfelse>
//                                                                                                                                          Type: #cfcatch.type#<BR>
//                                                                                                                                          Message: #cfcatch.message#<BR>
//                                                                                                                                          Detail: #cfcatch.detail#
//                                                                                                                                        </cfif>
//                                                                                                                                      </cfmail>
//                                                                                                                                    </cfcatch>
//                                                                                                                                  </cftry>
//                                                                                                                              --->
//                                                                                                                              </cfif>

//  disabled this }

//
//                                                                                                                              <!--- OK, now update the AWACS status of the encounter --->
//                                                                                                                              <cfquery datasource="#Variables.EMRDataSource#" name="SetProcessingStatus">
//                                                                                                                                UPDATE EncounterHistory
//                                                                                                                                SET	AWACSStatus = <cfqueryparam cfsqltype="CF_SQL_INTEGER" value="#variables.AWACSStatus#">,
//                                                                                                                                  AWACSMessage = <cfqueryparam cfsqltype="CF_SQL_VARCHAR" value="#variables.AWACSMessage#">
//                                                                                                                                    WHERE Encounter_Id=<cfqueryparam cfsqltype="CF_SQL_BIGINT" value="#GetEncounterList.Encounter_ID#">
//
//
//                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 </cfquery>

$sql = "UPDATE EncounterHistory
SET	AWACSStatus = $AWACSStatus,
AWACSMessage = '$AWACSMessage'
WHERE Encounter_Id = $Encounter_dt->Encounter_ID ";

  $this->ReportModel->data_db->trans_begin();
    $SetProcessingStatus = $this->ReportModel->data_db->query($sql);
    $this->ReportModel->data_db->trans_commit();
//                                                                                                                                      </cfif>
//
}
?>
