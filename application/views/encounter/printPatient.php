<?php

  $dt = $this->EncounterHistoryModel->get_by_id($id)->row();
  $Encounter_Id = (int) $id;
  $Patient_Id = (int) $dt->Patient_ID;
  $Provider_Id = (int) $dt->Provider_ID;
  $EncounterDescription_Id = (int) $dt->EncounterDescription_ID;
  $patient_dt =  $this->PatientProfileModel->get_by_id($Patient_Id)->row();

  $sql = " Select DISTINCT
        T.TML2_HeaderMaster_Id
          From " . $template_db . ".dbo.TML2 T,
              " . $data_db . ".dbo.ETL2 E
        Where E.Encounter_Id= $Encounter_Id
          And E.TML2_Id=T.TML2_Id  ";
  $template_master_id = $this->ReportModel->data_db->query($sql);
  $template_master_id_num = $template_master_id->num_rows();
  $template_master_result = $template_master_id->result();

  $TemplateStruct = array();
  if ($template_master_id_num > 0) {
    foreach ($template_master_result as $template_master_dt) {
      $TemplateStruct[] = $template_master_dt->TML2_HeaderMaster_Id;
    }
  }

  $sql = "Select TOP 1
        AccountNumber,
        FirstName+' '+MiddleName+' '+LastName AS PatientFullName
          From " . $data_db . ".dbo.PatientProfile
        Where Patient_Id = $Patient_Id  ";
  $PatientHeader = $this->ReportModel->data_db->query($sql);
?>

<!DOCTYPE html>
<html>
  <head>
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
     <?php
      $title = "Print Chart Notes";
     if(!empty($patient_dt->FirstName)){
       $title.=" for ".implode(', ', array_filter(array($patient_dt->LastName, $patient_dt->FirstName)));
     }
     ?>
    <title><?php echo $title?></title>
    <link rel="shortcut icon" href="<?php echo base_url('assets/ace/img/faicon.ico') ?>">
    <style type="text/css" media="all">
      body {margin-top: 0.2in;
            margin-left: -0.2in;
            margin-right: 0.25in;
            margin-bottom: 0.25in;
            word-wrap: break-word;
      }
    </style>
    <script language="JavaScript" defer>
      function ScrollBar()
      {
        document.body.style.scrollbarBaseColor = '#808080';
        document.body.style.scrollbarArrowColor = '#FFFFFF';
        document.body.style.scrollbarHighlightColor = '#FFFFFF';
      }
    </script>

    <?php
    if ($print_mode == 'print') {
      ?>
    <?php
    }
    ?>
  </head>


<?php
  $sql = "Select TOP 1
          HeaderIds
          From " . $data_db . ".dbo.SOChartHeaders
          Where Encounter_Id= $Encounter_Id";

  $so_headers = $this->ReportModel->data_db->query($sql);
  $so_headers_num = $so_headers->num_rows();
  $so_headers_row = $so_headers->row();

  $PrintPHAOnly = $PrintPatientOnly; 
  if ($so_headers_num == 0 || $so_headers_row->HeaderIds == "") {
    $add = "";
    if ($PrintPHAOnly) {
      $add = " And (H.HeaderMaster_Id = 149 or H.HeaderMaster_Id = 148) ";
    }
    $sql = "Select
			H.Header_Id,
			H.HeaderMaster_Id,
			H.HeaderOrder,
			M.Component,
			M.FreeTextYN
      From " . $data_db . ".dbo.EncounterHeaders H
      Left Outer Join " . $data_db . ".dbo.HeaderMaster M
        On H.HeaderMaster_Id=M.HeaderMaster_Id
      Where H.Provider_Id = $Provider_Id
        And H.EncounterDescription_Id = $EncounterDescription_Id
        And (H.Hidden <> 1 Or H.Hidden IS NULL)
        $add " .
        "Order By H.HeaderOrder";
    $ModuleSettings = $this->ReportModel->data_db->query($sql);
    $ModuleSettings_result = $ModuleSettings->result();
  } else {
    if ($PrintPHAOnly) {
      $add = " And (H.HeaderMaster_Id = 149 or H.HeaderMaster_Id = 148) ";
    }
    $sql = "Select
			H.Header_Id,
			H.HeaderMaster_Id,
			H.HeaderOrder,
			M.Component,
			M.FreeTextYN
      From " . $data_db . ".dbo.EncounterHeaders H
      Left Outer Join " . $data_db . ".dbo.HeaderMaster M
        On H.HeaderMaster_Id=M.HeaderMaster_Id
      Where H.Header_Id IN ($HeaderIds)
        $add
      Order By H.HeaderOrder";

    $ModuleSettings = $this->ReportModel->data_db->query($sql);
    $ModuleSettings_result = $ModuleSettings->result();
  }

  $sql = "Select EncounterConfig_Id
  From " . $data_db . ".dbo.EncounterConfig
  Where EncounterDescription_ID= $EncounterDescription_Id
   And Provider_Id=$Provider_Id";
  $DefaultConfig = $this->ReportModel->data_db->query($sql);
  $DefaultConfig_num = $DefaultConfig->num_rows();
  $DefaultConfig_result = $DefaultConfig->result();
  $DefaultConfig_row = $DefaultConfig->row();


  if ($DefaultConfig_num == 0) {
    $sql = "Select EncounterConfig_ID
	   From " . $data_db . ".dbo.EncounterConfig
	   Where EncounterDescription_ID = 0
	   And Provider_Id =$Provider_Id";
    $ProviderDefaultConfig = $this->ReportModel->data_db->query($sql);
    $ProviderDefaultConfig_row = $ProviderDefaultConfig->row();
    $ProvDefConfig = ($ProviderDefaultConfig_row) ? $ProviderDefaultConfig_row->EncounterConfig_ID : 0;
  } else {
    $ProvDefConfig = $DefaultConfig_row->EncounterConfig_Id;
  }

  $sql = "Select HeaderMaster_Id,
       EncounterComponents_Id
   From EncounterComponents
   Where (((EncounterText IS NOT NULL) AND (EncounterText NOT LIKE '')) OR ComponentKeys IS NOT NULL)
   And Patient_Id=$Patient_Id
   And Encounter_Id= $Encounter_Id";

  $HeaderMasterExist = $this->ReportModel->data_db->query($sql);
  $HeaderMasterExist_result = $HeaderMasterExist->result();

  $HeaderMasterStruct = array();
  if ($HeaderMasterExist->num_rows() != 0) {
    foreach ($HeaderMasterExist_result as $value) {
      $HeaderMasterStruct[] = $value->HeaderMaster_Id;
    }
  }

  ?>
  <body bgcolor="#ffffff" leftmargin="0" topmargin="0" >
    <div style="max-width: 672px; margin: 0 auto">
    <?php
    if ($DefaultConfig_num == 0) {
      $this->load->view('encounter/print/defaultheader');
      $EncounterConfig_Id = 0;
    } else {
      $data['ConfigKey'] = $DefaultConfig_row->EncounterConfig_Id;
      $data['PrintPatientOnly'] = $PrintPatientOnly;
      $this->load->view('encounter/print/patientHeader', $data);
      $EncounterConfig_Id = $DefaultConfig_row->EncounterConfig_Id;
    }
?>
<?php


    $sql = "Select Top 1
            Amendment_ID
              From EncounterHistory
            Where Encounter_Id= $Encounter_Id ";
    $GetAdmendmentID = $this->ReportModel->data_db->query($sql);
    $GetAdmendmentID_row = $GetAdmendmentID->row();
    if ($GetAdmendmentID_row->Amendment_ID != "") {
      $sql = "	Select Top 1
	       Convert(Char,EncounterDate,101) AS TheDate
          From EncounterHistory
         Where Encounter_Id= $Encounter_Id 	";
      $GetOriginalEncounterDate = $this->ReportModel->data_db->query($sql);
      $GetOriginalEncounterDate_row = $GetOriginalEncounterDate->row();
      ?>
      <div align="center" style="font-size: 14px; color: Black; font-weight: bold; font-family: 'Times New Roman';">Amendment to Previous Encounter of <?php echo $GetOriginalEncounterDate_row->TheDate ?></div>
      <?php
    }

    foreach ($ModuleSettings_result as $ModuleSetting_dt) {
      $data['ConfigKey'] = $EncounterConfig_Id;
      $data['ModuleSetting_dt'] = $ModuleSetting_dt;
      $data['HeaderKey'] = $ModuleSetting_dt->Header_Id;
      $data['PatientKey'] = $Patient_Id;
      $data['HeaderMasterKey'] = $ModuleSetting_dt->HeaderMaster_Id;
      $data['FreeTextKey'] = $ModuleSetting_dt->FreeTextYN;
      $data['SOHeaders'] = $so_headers_num;
      $data['data_db'] = $data_db;
      $data['PrimaryKey'] = $Encounter_Id;
      $data['ProviderKey'] = $Provider_Id;
      $data['OutPutMasterKey'] = $OutputMasterKey;
      $data['EncounterDescriptionKey'] = $EncounterDescription_Id;

      if (($ModuleSetting_dt->HeaderMaster_Id != 1) || ($ModuleSetting_dt->HeaderMaster_Id == 29)) {
        $keys = encountercomponents($data);
        $data['ComponentKey'] = $keys['ComponentKey'];
        $data['EncounterComponentKey'] = $keys['EncounterComponentKey'];
        $data['UseDetailKeys'] = $keys['UseDetailKeys'];

        if ($ModuleSetting_dt->Component && $data['ComponentKey'] != '0') {
          $com_print = str_replace('.cfm', '', $ModuleSetting_dt->Component);
          if ($com_print === 'comp_screeningschedule') {
            $this->load->view("encounter/comp_print/comp_patient", $data);
          }
        }
      }
    }

    $GenerateAVS = 0; 

    if ($PrintPHAOnly != 1 && $GenerateAVS != 1) {
      $data['PatientKey'] = $Patient_Id;
      $data['PrimaryKey'] = $Encounter_Id;
      $data['ProviderKey'] = $Provider_Id;
      if (!isset($summary_report)) {
        $this->load->view("encounter/print/signature", $data);
      }
    }

    $IncludeAttachments = 1;
    ?>
      </div>
  </body>
</html>