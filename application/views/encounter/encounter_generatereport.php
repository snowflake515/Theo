<?php
  $sql = "
    UPDATE EncounterHistory
    SET	AWACSStatus =  10
    WHERE Encounter_Id= $Encounter_Id ";
  $this->ReportModel->data_db->trans_begin();
  $SetProcessingStatus = $this->ReportModel->data_db->query($sql);
  $this->ReportModel->data_db->trans_commit();
?>

<?php

  $AWACSStatus = 99;

  $AWACSMessage = "";

  $EncounterSignedOff = (($Encounter_dt->EncounterSignedOff == TRUE) && ($Encounter_dt->SignedOffSupervising == TRUE)) ? TRUE : FALSE;

  if ($Encounter_dt) {

  if ($EncounterSignedOff == TRUE) {

  $sql = "DELETE
              FROM	".$image_db.".dbo.EncounterDocuments
              WHERE	Encounter_Id= $Encounter_Id";

  $this->ReportModel->data_db->trans_begin();
  $DelFile = $this->ReportModel->data_db->query($sql);
  $this->ReportModel->data_db->trans_commit();
  }

  $sql = "Update AWACSInput
          Set Hidden = 1,
          DateHidden = getdate()
          Where (Encounter_Id = $Encounter_Id)
            AND (isnull(Hidden, 0) = 0)";
  $this->ReportModel->data_db->trans_begin();
  $GetAWACSInput = $this->ReportModel->data_db->query($sql);
  $this->ReportModel->data_db->trans_commit();

  $this->load->view('encounter/generate_report/AWACSInput-TemplateLoad');

  $this->load->view('encounter/generate_report/AWACSInput-MedicalLoad');

  $this->load->view('encounter/generate_report/AWACSResults-SeverityLoad');

  $sql = "SELECT top 1
          EncounterComponents_ID
          FROM EncounterComponents
          WHERE Encounter_Id = $Encounter_Id
            AND HeaderMaster_ID = 147";

  $GetEncComp = $this->ReportModel->data_db->query($sql);
  $GetEncComp_num = $GetEncComp->num_rows();
  $GetEncComp_row = $GetEncComp->row();

  if ($GetEncComp_num == 0) {

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

  $sql = "Update EncounterComponents
          Set ComponentKeys = $Encounter_Id
          Where EncounterComponents_ID = $GetEncComp_row->EncounterComponents_ID";
  $this->ReportModel->data_db->trans_begin();
  $InsertEncComp = $this->ReportModel->data_db->query($sql);
  $this->ReportModel->data_db->trans_commit();
  }


  $sql = "SELECT top 1
          EncounterComponents_ID
          FROM EncounterComponents
          WHERE Encounter_Id = $Encounter_Id
            AND HeaderMaster_ID = 148";

  $GetEncComp = $this->ReportModel->data_db->query($sql);
  $GetEncComp_num = $GetEncComp->num_rows();
  $GetEncComp_row = $GetEncComp->row();

  if ($GetEncComp_num == 0) {

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

    $sql = "Update EncounterComponents Set ComponentKeys = $Encounter_dt->Encounter_ID
              Where EncounterComponents_ID = $GetEncComp_row->EncounterComponents_ID
                                                                          ";
    $this->ReportModel->data_db->trans_begin();
    $InsertEncComp = $this->ReportModel->data_db->query($sql);
    $this->ReportModel->data_db->trans_commit();
  }

    $sql = "SELECT top 1
      EncounterComponents_ID
      FROM EncounterComponents
      WHERE Encounter_Id = $Encounter_dt->Encounter_ID
      AND HeaderMaster_ID = 149";
    $GetEncComp = $this->ReportModel->data_db->query($sql);
    $GetEncComp_num = $GetEncComp->num_rows();
    $GetEncComp_row = $GetEncComp->row();

  if ($GetEncComp_num == 0) {

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

    $sql = " Update EncounterComponents
        Set ComponentKeys = $Encounter_dt->Encounter_ID
        Where EncounterComponents_ID = $GetEncComp_row->EncounterComponents_ID ";
    $this->ReportModel->data_db->trans_begin();
    $InsertEncComp = $this->ReportModel->data_db->query($sql);
    $this->ReportModel->data_db->trans_commit();

  }

  $data['EncounterId'] = $Encounter_dt->Encounter_ID;
  $data['Mode'] = 'WRITE';
  $data['BatchJob']= 1;
  $data['Field'] = $Field;

  $this->load->view('encounter/generate_report/EncounterDocuments', $data);

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

  $sql = "UPDATE EncounterHistory
  SET	AWACSStatus = $AWACSStatus,
  AWACSMessage = '$AWACSMessage'
  WHERE Encounter_Id = $Encounter_dt->Encounter_ID ";

  $this->ReportModel->data_db->trans_begin();
  $SetProcessingStatus = $this->ReportModel->data_db->query($sql);
  $this->ReportModel->data_db->trans_commit();

  }
?>
