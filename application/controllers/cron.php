<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Cron extends CI_Controller
{
  var $current_user;

  function __construct()
  {
    parent::__construct();
    $this->load->model('TheoConnectModel');
    $this->load->model('FreeModel');
    $this->load->model('ProviderReportLogModel');
    $this->load->model('PhqModel');
    $this->load->model('Tml2Model');
    $this->load->model('Tml3Model');
    //dev
    // $this->current_user = array(
    //   'User_Id' => 'dburke',
    //   'org_id' => 341,
    //   'User_PK' => 4325,
    //   "tml1_ID" => 6666
    // ); 

    //prod
    $this->current_user = array(
      "735" => array(
        "OrgName" => "Oschner - Actone health",
        "office key" => "154265",
        'User_Id' => 'ARU100', //'dburke',
        'org_id' => 735, //341,
        'User_PK' => 9069, //4325,
        "tml1_ID" => 4497, // 6666
        'Dept_ID' => 749,
        'authKey' => "BSX7ThS6spFaWGtDwO7mahgYMDSCPzNq8"
      ),
      "740" => array(
        "OrgName" => "Blue Cross Blue Shield",
        "office key" => "150310",
        'User_Id' => 'bcb201', //'dburke',
        'org_id' => 740, //341,
        'User_PK' => 9088, //4325,
        "tml1_ID" => 4553, // 6666
        'Dept_ID' => 753,
        'authKey' => "QJVMa9Pk8zdzO1xE1TsiE4A38wXRZX4LKMc"
      )
    );


    // $this->current_user = array(
    //   'User_Id' => 'ARU100', //'dburke',
    //   'org_id' => 735, //341,
    //   'User_PK' => 9069, //4325,
    //   "tml1_ID" => 4497, // 6666
    //   'Dept_ID' => 749
    // );
  }

  function index()
  {
    foreach ($this->current_user as $org_array) {
      $org_id = $org_array['org_id'];
      $tml1_ID = $org_array['tml1_ID'];
      $sql = "SELECT  TOP 1 EncounterHistory.Encounter_ID , ReportLog.ID as log_enq_ID,  Appointments.*  FROM Wellness_eCastEMR_Data.dbo.Appointments Appointments JOIN Wellness_eCastEMR_Data.dbo.EncounterHistory EncounterHistory ON EncounterHistory.Appointments_ID=Appointments.Appointments_ID  LEFT JOIN Wellness_eCastEMR_Template.dbo.TheoConnect TheoConnect ON TheoConnect.Encounter_ID = EncounterHistory.Encounter_ID  LEFT JOIN Wellness_eCastEMR_Data.dbo.ReportLog ReportLog ON ReportLog.ID = EncounterHistory.Encounter_ID and ReportLog.ReportCategory = 'PROVIDER' LEFT JOIN Wellness_eCastEMR_Data.dbo.TabletTriggersData TabletTriggers ON TabletTriggers.Encounter_ID = EncounterHistory.Encounter_ID WHERE  TabletTriggers.TabletTriggers_ID IS NOT NULL AND ReportLog.ID IS NULL AND Appointments.Org_ID=$org_id  ORDER BY Appointments.Appointments_ID DESC";
      $this->FreeModel->data_db()->trans_begin();
      $query = $this->FreeModel->data_db()->query($sql);
      foreach ($query->result() as $Appointments) {
        $Appointments_ID = $Appointments->Appointments_ID;
        $Encounter_ID = $Appointments->Encounter_ID;
        echo '<pre>Appointments_ID =' . $Appointments_ID . '<br>';
        $this->add_template_data((int) $Encounter_ID, (int) $tml1_ID, $org_array);
        // exit(print_r($Appointments->Appointments_ID));
      }
      $this->FreeModel->data_db()->trans_commit();
    }
  }


  function add_template_data($encounter_ID, $tml1, $org_array)
  {
    echo '<pre>encounter_id =' . $encounter_ID . '<br>';
    echo 'tml1 =' . $tml1 . '<br>';
    $encounter = $this->get_db_encounter_row($encounter_ID, $org_array);
    $check_tml1_theo = $this->check_theo_link($tml1, $encounter, $org_array);
    $check_theo_connect = $check_tml1_theo['check_theo_connect'];

    $VideoPlay_ID = !empty($check_theo_connect->TheoVideoPlay_ID) ? $check_theo_connect->TheoVideoPlay_ID : 0;
    $Session_ID = !empty($check_theo_connect->TheoSession_ID) ? $check_theo_connect->TheoSession_ID : 0;
    $Account_ID = !empty($check_theo_connect->TheoAccount_ID) ? $check_theo_connect->TheoAccount_ID : 0;

    if ($tml1) {
      $con = '(Hidden = 0 OR Hidden IS NULL)';
      $template2 = $this->Tml2Model->get_by_field('TML1_ID', $tml1, $con)->result();
      $options = array(
        "realnumber" => "* Real Number eg:99.99",
        "integer" => "* Integer, max length 3, eg:123",
        "letters_only" => "* Letters Only",
        "alphanumeric" => "* AlphaNumeric",
      );

      foreach ($template2 as $val) {
        $tml_3 = $this->Tml3Model->get_by_field('TML2_ID', $val->TML2_ID, $con)->result();
        foreach ($tml_3 as $tml3) {

          $TML3_ValueInput = NULL;

          if ($tml3->PreSelected == 1 && $tml3->TypeInput != 'radio_btn') {

            $dt_insert = array(
              'Encounter_Id' => $encounter_ID,
              'TML3_Id' => $tml3->TML3_ID,
            );

            $cek_etl3 = $this->ETL3Model->get_by_field('Encounter_ID', $encounter_ID, 'TML3_Id = ' . $tml3->TML3_ID);
            if ($cek_etl3->num_rows() == 0) {
              $this->ETL3Model->insert($dt_insert);
            }

            $cek_tml3_input = $this->ETL3InputModel->get_by_field('Encounter_ID', $encounter_ID, 'TML3_Id = ' . $tml3->TML3_ID);
            if ($cek_tml3_input->num_rows() == 0) {
              $this->ETL3InputModel->insert($dt_insert);
            }


            $check_tabletinput = $this->TabletInputModel->get_by_field('TML3_ID', $tml3->TML3_ID, 'Encounter_ID = ' . $encounter_ID);
            if ($check_tabletinput->num_rows() == 0) {
              $dt_inset = array(
                'Encounter_ID' => $encounter_ID,
                'TML1_ID' => $tml1,
                'TML2_ID' => $tml3->TML2_ID,
                'TML3_ID' => $tml3->TML3_ID,
                'TML3_Value' => NULL
              );
              $this->TabletInputModel->insert($dt_inset);
            }
          }



          $data_check = $this->TabletInputModel->get_data($tml3->TML3_ID, $encounter_ID)->row();
          $check_cb = ($data_check && $data_check->Status !== 'X') ? TRUE : FALSE;
          $TML3_ValueInput = ($check_cb) ? $data_check->TML3_Value : NULL;

          $post_params = array(
            "tml3_ID" => $tml3->TML3_ID,
            "tml2_ID" => $tml3->TML2_ID,
            "tml1_ID" =>  $tml1,
            "checked" => $check_cb,
            "input" => $TML3_ValueInput,
            "Encounter_ID" => $encounter_ID,
            "theoquestion_ID" => $tml3->TheoQuestion_ID,
            "theoanswer_ID" => $tml3->TheoAnswer_ID,
            "theosession_ID" => $Session_ID,
            "theovideoplay_ID" => $VideoPlay_ID,
            "theoaccount_ID" => $Account_ID
          );
          echo '<br>post_params<br>';
          print_r($post_params);
          $this->save_tml3($post_params, $org_array);
        }
      }
    }
    $this->generate_careplan($encounter->Encounter_ID, $check_theo_connect->TheoSession_ID);
    $this->generate_report($encounter->Encounter_ID, $org_array);
  }
  private function get_db_encounter_row($encounterKey = null, $org_array)
  {
    echo "checking EncounterHistory for $encounterKey<br>";
    $dt = $this->EncounterHistoryModel->patient_history(
      'EncounterHistory.Encounter_ID',
      $encounterKey,
      array(
        'EncounterHistory.Org_ID' => $org_array['org_id'] // $this->current_user['org_id']
      )
    )->row();
    if ($dt) {
      echo 'Encounter is found!<br>';
    }
    if (empty($dt)) {
      echo 'Encounter not found!<br>';
    } elseif (!empty($dt->EncounterSignedOff) && $dt->EncounterSignedOff == 1) {
      echo 'Encounter is Locked!<br>';
    }
    return $dt;
  }

  private function check_theo_link($tml1, $encounter, $org_array)
  {
    echo "tml1=" . $tml1 . '<br>';
    $tml1_dt = $this->Tml1Model->get_by_id($tml1)->row();
    $check_theo_connect = NULL;
    $patient_vitals = NULL;
    echo 'check_theo_link.<br>';
    // exit(print_r($tml1_dt));
    if ($encounter) {
      if (!empty($tml1_dt->TML1_Theo_Link) && $tml1_dt->TML1_Theo_Link == 1) {
        $theo_connect_data = array(
          'TML1_ID' => $tml1,
          'Encounter_ID' => $encounter->Encounter_ID,
          'Org_ID' => $org_array['org_id']
        );
        $check_theo_connect  = $this->TheoConnectModel->get_where($theo_connect_data)->row();
        echo 'check_theo_connect.<br>';
        // exit(print_r($check_theo_connect));
        if (
          !empty($check_theo_connect->TheoVideoPlay_ID)
          && !empty($check_theo_connect->TheoSession_ID)
          && !empty($check_theo_connect->TheoAccount_ID)
        ) {
          echo 'already have theo_connect_data.<br>';
          // $this->generate_careplan($encounter->Encounter_ID, $check_theo_connect->TheoSession_ID);
          // $this->generate_report($encounter->Encounter_ID,$org_array['org_id']);
          //do nothing
        } else {
          if (!empty($tml1)) {
            echo 'inputSessionTheo.<br>';
            //input session theo
            $this->inputSessionTheo($encounter, $tml1, $org_array);
            $check_theo_connect  = $this->TheoConnectModel->get_where($theo_connect_data)->row();
            // echo '<br>New theo_connect  entry<br>';
            // print_r($check_theo_connect);
          }
        }
        // $patient_get_vitals = $this->getTheoVitalsFirst($encounter, $tml1, $check_theo_connect);
        // $patient_vitals = $this->inputTheoVitalsFirst($encounter, $tml1, $check_theo_connect);
      }
    }

    return array(
      'check_theo_connect' => $check_theo_connect,
      // 'patient_vitals' => $patient_vitals
    );
  }

  private function inputSessionTheo($encounter, $tml1, $org_array)
  {
    $dt_patient = $this->PatientProfileModel->get_by_id($encounter->Patient_ID)->row();

    $url = THEO_LINK . "/api/session/manual/";
    // exit(print_r($dt_patient));
    $content = json_encode(
      array(
        "patientDOBString" =>  date('Y-m-d', strtotime($dt_patient->DOB)),
        "patientIdentifier" => $dt_patient->MedicalRecordNumber,
        "latitude" =>  "-6.927040815359844",
        "longitude" =>  "107.5543816668052",
        "patientName" =>  $dt_patient->FirstName . ' ' . $dt_patient->LastName,
        "orgId" => $org_array['org_id'],
        "userId" => $org_array['User_Id']
      )
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    $response = json_decode($json_response, true);
    if (!empty($response['videoPlayId']) &&  !empty($response['sessionId']) && !empty($response['accountId'])) {
      $video_play = $response['videoPlayId'];
      $session_id = $response['sessionId'];
      $account_id = $response['accountId'];

      $input_theo_connect = array(
        'TML1_ID' => $tml1,
        'Encounter_ID' => $encounter->Encounter_ID,
        'Patient_ID' => $encounter->Patient_ID,
        'User_ID' => $org_array['User_Id'], //$this->current_user->User_Id,
        'Org_ID' => $org_array['org_id'], //$org_array->org_id,
        'TheoVideoPlay_ID' => $video_play,
        'TheoSession_ID' => $session_id,
        'TheoAccount_ID' => $account_id,
        'DateCreated' => date('Y-m-d H:i:s'),
      );
      echo 'input_theo_connect';
      print_r($input_theo_connect);

      $this->TheoConnectModel->insert($input_theo_connect);
      // $this->generate_careplan($encounter->Encounter_ID, $session_id);
      // $this->generate_report($encounter->Encounter_ID,$org_array['org_id']);
    }
  }
  function generate_careplan($encounter_id = 0, $session_id = 0)
  {

    $url = THEO_LINK . "/api/session/generate-careplan-new/$session_id/en";

    try {
      $json = @file_get_contents($url);
      $obj = json_decode($json);
    } catch (\Exception $e) {
      echo 'operation failed';
    }

    if (!empty($obj->urlCarePlan)) {
      print_r($obj);
      echo '<br>aws link = ' . URL_AWS_S3 . $obj->urlCarePlan . '<br>';
      // redirect(URL_AWS_S3 . $obj->urlCarePlan);
    } else {
      echo 'Something Wrong, PLease Try Again!';
    }
  }
  public function generate_report($encounterKey = 0, $org_array)
  {
    $dt = $this->get_db_encounter_row($encounterKey, $org_array);
    $this->save_encounter_ajax($dt, $org_array);
    $this->PhqModel->check_all_phq($dt->Encounter_ID);

    $this->EncounterHistoryModel->process_hcc($dt->Encounter_ID);

    $check =  $this->ReportLogModel->select_db()
      ->where('ID', (int) $encounterKey)
      ->where('TableName', 'EncounterHistory')
      ->get()->num_rows();

    $check2 = $this->ReportLogModel->select_db()
      ->where('ID', (int) $encounterKey)
      ->where('TableName', 'EncounterHistory')
      ->where('DateUpdated is NULL')
      ->get()->num_rows();

    if ($dt->EncounterSignedOff != 1 || $check == 0 || $check2 > 0) {
      $this->generate_all_reports($org_array['org_id'], $dt, 'PROVIDER', 0, 0);
      // $this->generate_all_reports($dt, 'PATIENT', 1, 0);
      // $this->generate_all_reports($dt, 'SUMMARY', 0, 1);
    }

    //xml generate
    $this->save_report('provider', $encounterKey, 'xml', 1, $org_array);

    $status = "success";
    $msg = $this->mylib->get_client() . " Report has been generated";

    // if ($json) {
    echo json_encode(array(
      'status' => $status,
      'msg' => $msg
    ));
    // exit();
    // }
  }
  private function save_encounter_ajax($dt, $org_array)
  {
    $dt_post = array(
      'ChiefComplaint' => $this->input->post('ChiefComplaint'),
      'Dept_ID' => $org_array['Dept_ID'],
      'EncounterDate' => $this->input->post('EncounterDate'),
      'EncounterDescription_ID' => $this->input->post('EncounterDescription_ID'),
      'Provider_ID' => $this->input->post('Provider_ID'),
      'SupProvider_ID' => $this->input->post('SupProvider_ID'),
      'Facility_ID' => $this->input->post('Facility_ID'),
    );

    $dt_update = array(
      'Notes' => $this->input->post('ChiefComplaint'),
      'Facility_ID' => $this->input->post('Facility_ID'),
      'Provider_ID' => $this->input->post('Provider_ID'),
      'EncounterDescription_ID' => $this->input->post('EncounterDescription_ID'),
    );
    if (!empty($_POST['Provider_ID'])) {
      $this->EncounterHistoryModel->update($dt->Encounter_ID, $dt_post);
      $this->AppointmentModel->update($dt->Appointments_ID, $dt_update);
    }
  }

  private function generate_all_reports($org_array, $dt, $cat, $PrintPatientOnly = 0, $summary_report = false, $print_mode = '')
  {
    $data = $this->data_set();
    $data['id'] = $dt->Encounter_ID;
    $data['Encounter_Id'] = $dt->Encounter_ID;
    $data['Encounter_dt'] = $dt;

    $data['print_mode'] = $print_mode;
    $data['PrintPatientOnly'] = $PrintPatientOnly;
    if ($summary_report) {
      $data['summary_report'] = $summary_report;
    }

    $html = $this->load->view($data['partial'], $data, TRUE);
    if ($summary_report) {
      $sum_html = '';
      $sum_html .= '<h4>Summary Report</h4>';
      $sum_html .= $html;
      $sum_html .= '<style> @media print { .no-print{ display: none; } }</style>';
      $st_break = '<div style="height: 20px; margin: 80px auto; width: 7.0in; background: #f2f2f2" class="no-print"></div>';
      $sum_html .= $st_break;
      $sum_html .= '<div style="page-break-after: always;"></div> ';

      //provider report
      $report =  $this->get_row_report('Provider', $dt->Encounter_ID);
      $r = !empty($report->Report) ? json_decode($report->Report) : NULL;
      $sum_html .= !empty($r->html) ? $r->html : 'Please Generate Report First!';

      $html = $sum_html;
    }

    $q = array(
      'ID' => $dt->Encounter_ID,
      'ReportCategory' => $cat,
      'TableName' => 'EncounterHistory'
    );

    $html =  utf8_encode($html);
    $data_ins = $q + array(
      'Report' => json_encode(array('html' => $html)),
      'Org_ID' => $org_array['org_id'],
      'User_PK' => $org_array['User_PK'],
      'DateUpdated' => date('Y-m-d H:i:s')
    );

    $check = $this->ReportLogModel->select_db()->where($q)->get()->row();
    if (!empty($check)) {
      $this->ReportLogModel->update($check->ReportLog_ID, $data_ins);
    } else {
      $data_ins['DateCreated'] = date('Y-m-d H:i:s');
      $this->ReportLogModel->insert($data_ins);
    }

    return $html;
  }
  private function data_set()
  {
    $data['data_db'] = $this->load->database('data', TRUE)->database;
    $data['template_db'] = $this->load->database('template', TRUE)->database;
    $data['user_db'] = $this->load->database('user', TRUE)->database;
    $data['audit_db'] = $this->load->database('audit', TRUE)->database;
    $data['image_db'] = $this->load->database('image', TRUE)->database;
    $data['master_db'] = $this->load->database('master', TRUE)->database;
    // $data['current_user'] =$org_array;
    $data['partial'] = "../views/encounter/encounter_generatereport";
    $data['HeaderNeeded'] = FALSE;
    $data['OutputMasterKey'] = 0;
    $data['NeedTemplateHeader'] = TRUE;
    return $data;
  }

  private function  get_row_report($cat = NULL, $encounterKey = 0)
  {
    return $this->ReportLogModel->select_db()
      ->where('ID', (int) $encounterKey)
      ->where('TableName', 'EncounterHistory')
      ->where('ReportCategory', strtoupper($cat))
      ->get()->row();
  }

  public function save_report($cat = NULL, $encounterKey = 0, $format = NULL, $is_donwload = 0, $org_array)
  {
    $dt = $this->get_db_encounter_row($encounterKey, $org_array);
    $report =  $this->get_row_report($cat, $dt->Encounter_ID);
    $r = !empty($report->Report) ? json_decode($report->Report) : NULL;
    if (empty($r->html)) {
      echo 'Please Generate Report First!';
      return false;
      //exit();
    }

    $patient = $this->PatientProfileModel->get_by_id($dt->Patient_ID)->row();
    $org = $this->OrgProfileModel->get_by_id($dt->Org_ID)->row();
    $html = 'Policy Number: ' . $patient->MedicalRecordNumber . ' ' . date('m/d/Y');

    $url_footer = "./reports/footer/" . $patient->Patient_ID . ".html";
    file_put_contents($url_footer, $html, LOCK_EX);

    $print_mode = $report->ReportCategory;
    if ($print_mode == "PROVIDER") {
      $url_html = "./reports/" . $encounterKey . '.html';
      $url_pdf = "./reports/" . $encounterKey . '.pdf';
      $b_file = 'Provider Report';
    } elseif ($print_mode == "PATIENT") {
      $url_html = "./reports/patient_reports/" . $encounterKey . '.html';
      $url_pdf = "./reports/patient_reports/" . $encounterKey . '.pdf';
      $b_file = 'Patient Report';
    } elseif ($print_mode == "SUMMARY") {
      $url_html = "./reports/summary_reports/" . $encounterKey . '.html';
      $url_pdf = "./reports/summary_reports/" . $encounterKey . '.pdf';
      $b_file = 'Summary Report';
    }

    $html_report = $r->html;
    if ($print_mode == "SUMMARY") {
      $st_break = '<div style="height: 20px; margin: 80px auto; width: 7.0in; background: #f2f2f2" class="no-print"></div>';
      $html_report = str_replace($st_break, '', $html_report);
    }
    file_put_contents($url_html, $html_report, LOCK_EX);

    $data_db = $this->load->database('data', TRUE)->database;
    $Encounter_dt = $this->EncounterHistoryModel->get_by_id($encounterKey)->row();
    $sql = "Select TOP 1
       AccountNumber,
       FirstName, LastName, MedicalRecordNumber,
       FirstName+' '+MiddleName+' '+LastName AS PatientFullName
        From " . $data_db . ".dbo.PatientProfile
       Where Patient_Id = $Encounter_dt->Patient_ID  ";
    $PatientHeader = $this->ReportModel->data_db->query($sql)->row();

    $this->load->helper('download');
    $exe = realpath('./assets/wkhtmltopdf/bin/wkhtmltopdf.exe');
    $temp_ex = explode('/', $exe);
    $exe_url = "";
    foreach ($temp_ex as $temp_dt) {
      if (strpos($temp_dt, " ") !== false) {
        $temp_dt = '"' . $temp_dt . '"';
      }
      $exe_url = $exe_url . $temp_dt;
    }

    $out = shell_exec($exe_url . ' --margin-bottom 10mm --margin-top 10mm  --footer-html ' . $url_footer . ' --footer-line  ' . $url_html . ' ' . $url_pdf . ' 2>&1');
    if ($format == 'xml' && $cat == 'provider') {
      if (!empty($org->XMLReport) && $org->XMLReport == 1) {
        $xml['org'] = $org;
        $xml['dt'] = $dt;
        $xml['patient'] = $patient;
        if (file_exists($url_pdf)) {
          $pdf = file_get_contents($url_pdf);
          $xml['base64'] =   base64_encode($pdf);
        }
        // header('Content-disposition: attachment; filename="newfile.xml"');
        // header('Content-type: "text/xml"; charset="utf8"');
        // $dw = '[' . SERVER_APP . ']-[' . ucwords($PatientHeader->PatientFullName) . ']'.'.xml';
        // header('Content-Disposition: filename='.$dw);
        $xml_report = $this->load->view('encounter/encounter_xml', $xml, TRUE);
        $path  = "./reports/xml/" . $dt->Org_ID;
        if (!file_exists($path)) {
          mkdir($path, 0777, true);
        }
        $url_xml =  $path . '/' . '[' . SERVER_APP . ']-[' . ucwords($PatientHeader->PatientFullName) . ']-' . $dt->Patient_ID . '-' . $b_file . '.xml';
        $xml_file = file_put_contents($url_xml, $xml_report, LOCK_EX);
        //readfile($url_xml);
        //force_download('[' . SERVER_APP . ']-[' . ucwords($PatientHeader->PatientFullName) . ']'.'.xml', $xml_file);
      }
    } elseif ($format == 'autoproviderreport' && $cat == 'provider') {
      if (file_exists($url_pdf)) {
        $fold = './reports/provider_reports/' . $org->Org_ID;
        if (!file_exists($fold)) {
          mkdir($fold,  0777, true);
        }
        $npf = 'WT1PR_' . date('Ymd') . '_' . str_replace(' ', '',  $PatientHeader->FirstName . ' ' . $PatientHeader->LastName) . '_' . $encounterKey . '_' . $PatientHeader->AccountNumber;
        copy($url_pdf, $fold . '/' . $npf . '.pdf');
        $this->ProviderReportLogModel->insert(
          array(
            'PatientFname' => $PatientHeader->FirstName,
            'PatientLname' => $PatientHeader->LastName,
            'Encounter_ID' => $encounterKey,
            'MedicalRecordNumber' => $PatientHeader->MedicalRecordNumber,
            'Patient_ID' => $Encounter_dt->Patient_ID,
            'Org_ID' => $org_array['org_id'],
            'Report_name' => $npf . '.pdf',
            'AccountNumber' => $PatientHeader->AccountNumber,
            'Record_Created' => date('Y-m-d H:i:s'),
            'Record_Updated' => date('Y-m-d H:i:s')
          )
        );
      }
    } else {
      if (file_exists($url_pdf)) {
        $pdf = file_get_contents($url_pdf);
        force_download('[' . SERVER_APP . ']-[' . ucwords($PatientHeader->PatientFullName) . ']-' . $b_file . '.pdf', $pdf);
      } else {
        echo "Please Generate Report First!";
      }
    }
  }

  function save_tml3($data, $org_array)
  {
    // $this->check_user();
    $tml1_id = (int) $data['tml1_ID'];
    $tml2_id = (int) $data['tml2_ID'];
    $tml3_id = (int) $data['tml3_ID'];
    $encounter_ID = (int) $data['Encounter_ID'];

    $checked = (int) $data['checked'];
    $tml3 = $this->Tml3Model->get_data_save(array('TML3_ID' => $tml3_id))->row();
    $phq9_total = 0;
    $phq2_total = 0;

    $theoanswer_ID = (int) $data['theoanswer_ID'];
    $theoquestion_ID = (int) $data['theoquestion_ID'];
    $theosession_ID = (int) $data['theosession_ID'];
    $theovideoplay_ID = (int) $data['theovideoplay_ID'];
    $theoaccount_ID = (int) $data['theoaccount_ID'];

    $ress = array(
      'status' => 'true',
      'phq9_total' => 0,
      'phq2_total' => 0
    );
    if ($tml3 && $encounter_ID) {
      $dt_post = array(
        'Encounter_ID' => $encounter_ID,
        'TML1_ID' => $tml3->TML1_ID,
        'TML2_ID' => $tml3->TML2_ID,
        'TML3_ID' => $tml3->TML3_ID,
        'TML3_Value' => $data['input'],
        'Status' => ($checked == 1) ? NULL : 'X'
      );
      $TabletInput = $this->TabletInputModel->get_data($tml3->TML3_ID, $encounter_ID)->row();
      if ($TabletInput) {
        $this->TabletInputModel->update($TabletInput->TabletInput_ID, $dt_post);
      } else {
        $this->TabletInputModel->insert($dt_post);
      }

      if ($checked == 1 && $tml3->TypeInput == 'radio_btn' && !empty($tml3->RadioName)) {
        $sql = "TML3.TML3_ID NOT IN ($tml3->TML3_ID) AND TML3.TML2_ID = $tml3->TML2_ID AND TML3.RadioName = '$tml3->RadioName'";
        $tml3_radio_btn = $this->Tml3Model->get_data_save($sql)->result();
        foreach ($tml3_radio_btn as $rd) {
          $tb = $this->TabletInputModel->get_data($rd->TML3_ID, $encounter_ID)->row();
          if ($tb) {
            $this->TabletInputModel->update($tb->TabletInput_ID, array('Status' => 'X'));
            $this->ETL3Model->delete_where(array('Encounter_ID' => $encounter_ID, 'TML3_Id' => $tb->TML3_ID,));
            $this->ETL3InputModel->delete_where(array('Encounter_Id' => $encounter_ID, 'TML3_Id' => $tb->TML3_ID));
          }
        }
      }


      //etl
      $cek_tml = $this->ETLModel->get_by_field('Encounter_Id', $encounter_ID)->num_rows();
      if ($cek_tml == 0) {
        $dt_insert = array(
          'Encounter_Id' => $encounter_ID,
          'ETLSaved' => 1
        );
        $this->ETLModel->insert($dt_insert);
      }

      //etl1
      $cek_tml = $this->ETL1Model->get_by_field('Encounter_Id', $encounter_ID)->num_rows();
      if ($cek_tml == 0) {
        $dt_insert = array(
          'Encounter_Id' => $encounter_ID,
          'TML1_Id' => $tml1_id
        );
        $this->ETL1Model->insert($dt_insert);
      }

      //etl2
      $cek_tml2 = $this->ETL2Model->get_by_field('Encounter_Id', $encounter_ID, 'TML2_Id = ' . $tml3->TML2_ID)->num_rows();
      if ($cek_tml2 == 0) {
        $dt_insert = array(
          'Encounter_Id' => $dt_post['Encounter_ID'],
          'TML2_Id' => $tml3->TML2_ID
        );
        $this->ETL2Model->insert($dt_insert);
      }

      //etl3
      $cek_tml3 = $this->ETL3Model->get_by_field('Encounter_Id', $encounter_ID, 'TML3_Id = ' . $tml3->TML3_ID)->num_rows();
      $dt_insert = array(
        'Encounter_Id' => $encounter_ID,
        'TML3_Id' => $tml3->TML3_ID
      );
      if ($cek_tml3 == 0) {
        $this->ETL3Model->insert($dt_insert);
      } elseif ($checked != 1) {
        $this->ETL3Model->delete_where($dt_insert);
      }

      //etl3_input
      $cek_tml3_input = $this->ETL3InputModel->get_by_field('Encounter_Id', $encounter_ID, 'TML3_Id = ' . $tml3->TML3_ID);
      $dt_insert = array(
        'Encounter_Id' => $encounter_ID,
        'TML3_Id' => $tml3->TML3_ID,
        'ETL3Input' => $data['input']
      );
      if ($cek_tml3_input->num_rows() == 0) {
        $this->ETL3InputModel->insert($dt_insert);
      } else {
        $row = $cek_tml3_input->row();
        $this->ETL3InputModel->update_where(array('Encounter_Id' => $encounter_ID, 'TML3_Id' => $tml3->TML3_ID), array('ETL3Input' => $data['input']));
        if ($checked != 1) {
          $this->ETL3InputModel->delete_where(array('Encounter_Id' => $encounter_ID, 'TML3_Id' => $tml3->TML3_ID));
        }
      }

      //PHQ2 
      $phq2_ids = explode(',', $this->PhqModel->phq2_ids);
      if (in_array($tml3->TML3_TBotMaster_ID, $phq2_ids)) {
        $this->PhqModel->phq2($tml1_id, $encounter_ID);
      }
      $phq2_total = $this->PhqModel->phq2($tml1_id, $encounter_ID, TRUE);

      //PHQ9 PROCESS
      $phqr_ids_arr = explode(',', $this->PhqModel->phq9_ids);
      if (in_array($tml3->TML3_TBotMaster_ID, $phqr_ids_arr)) {
        $this->PhqModel->phq9($tml1_id, $encounter_ID);
      }
      $phq9_total =  $this->PhqModel->phq9($tml1_id, $encounter_ID, TRUE);
      $dt_patient = $this->EncounterHistoryModel->patient_history('EncounterHistory.Encounter_ID', $encounter_ID, array('EncounterHistory.Org_ID' => $org_array['org_id']))->row();
      if (!empty($dt_patient->Patient_ID)) {
        $this->PatientProfileModel->update($dt_patient->Patient_ID, array('LastAWVDate' => date('Y-m-d H:i:s')));
      }

      $data_theo = array(
        'theoanswer_ID' => $theoanswer_ID,
        'theoquestion_ID' => $theoquestion_ID,
        'theosession_ID' => $theosession_ID,
        'theovideoplay_ID' => $theovideoplay_ID,
        'theoaccount_ID' => $theoaccount_ID
      );

      $data_template = array(
        'tml1_id' => $tml1_id,
        'tml2_id' => $tml2_id,
        'tml3_id' => $tml3_id,
        'encounter_ID' => $encounter_ID,
        'checked' => $checked
      );

      //Theo push
      if (
        !empty($theoanswer_ID)
        && !empty($theoquestion_ID)
        && !empty($theosession_ID)
        && !empty($theovideoplay_ID)
      ) {

        $this->send_theo_answer($data_theo, $data_template, $tml3, $org_array);
      }

      //Theo Vital Push
      $patient_vitals = array();
      $tmaster_vitals =  array(423, 424, 425, 426);
      if (
        in_array($tml3->TML3_TBotMaster_ID, $tmaster_vitals)
        && !empty($theosession_ID)
        && !empty($theoaccount_ID)
      ) {
        $patient_vitals = $this->send_theo_viatals($data_theo, $data_template, $tml3);
      }



      $ress = array(
        'status' => 'true',
        'phq9_total' => (int)$phq9_total,
        'phq2_total' => (int)$phq2_total,
        'patient_vitals' => $patient_vitals
      );
    }

    print_r($ress);
    // $this->json_output($ress);
  }

  private function send_theo_answer($data_theo, $data_template, $tml3, $org_array)
  {

    $url = THEO_LINK . "/api/answer/manual/" . $data_theo['theovideoplay_ID'];

    $ids = array();
    if ($tml3->TypeInput == "radio_btn") {
      $ids = array($data_theo['theoanswer_ID']);
    } elseif ($tml3->TypeInput == "checkbox") {
      $con = "(Hidden = 0 OR Hidden IS NULL)
              AND TheoQuestion_ID = " . $data_theo['theoquestion_ID'] . "
              AND TypeInput = 'checkbox'
              AND SubTitle = 0
              AND (TheoAnswer_ID is not NULL OR TheoAnswer_ID > 0 )";
      $tml_3 = $this->Tml3Model->get_by_field('TML2_ID', $data_template['tml2_id'], $con)->result();

      $tml3_checkbo_ids = array();
      foreach ($tml_3 as $tm) {
        $tml3_checkbo_ids[] = $tm->TML3_ID;
      }
      $tml3_checkbo_ids = implode(',', $tml3_checkbo_ids);
      $tml3_checkbo_ids = !empty($tml3_checkbo_ids) ? $tml3_checkbo_ids : '0';

      $con = "(Status <> 'X' OR Status is null)
              AND TML3_ID IN ($tml3_checkbo_ids)
              AND (Hidden = 0 OR Hidden IS NULL)";
      $tml_3 = $this->TabletInputModel->get_by_field('Encounter_ID', $data_template['encounter_ID'], $con)->result();

      foreach ($tml_3 as $tm) {
        $row = $this->Tml3Model->get_by_id($tm->TML3_ID)->row();
        if (!empty($row->TheoAnswer_ID)) {
          $ids[] = $row->TheoAnswer_ID;
        }
      }
    }

    $content = json_encode(
      array(
        "language" =>  "en",
        "answerIds" =>  $ids,
        "questionId" =>  $data_theo['theoquestion_ID'],
        "orgId" => $org_array['org_id'],
      )
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    $response = json_decode($json_response, true);
  }

  private function send_theo_viatals($data_theo, $data_template, $tml3)
  {
    $session_id = $data_theo['theosession_ID'];
    $account_id = $data_theo['theoaccount_ID'];
    $encounter = $this->EncounterHistoryModel->get_by_id($data_template['encounter_ID'])->row();
    $patient_id = !empty($encounter->Patient_ID) ? $encounter->Patient_ID : 0;
    $provider_id = !empty($encounter->Provider_ID) ? $encounter->Provider_ID : 0;

    $url = THEO_LINK . "/api/session/vitals/manual/$session_id/$account_id/$patient_id/$provider_id";


    $tmaster_vitals = array(423, 424, 425, 426);

    $vitals_value = array();
    foreach ($tmaster_vitals as $tmaster_vital) {
      $con = "(Hidden = 0 OR Hidden IS NULL)
              AND TypeInput = 'text_input'
              AND SubTitle = 0
              AND TML3_TBotMaster_ID = $tmaster_vital ";
      $tml_3_vitals = $this->Tml3Model->get_by_field('TML2_ID', $data_template['tml2_id'], $con)->row();
      $get_tml3 = !empty($tml_3_vitals->TML3_ID) ? $tml_3_vitals->TML3_ID : 0;
      $con = "(Status <> 'X' OR Status is null)
              AND TML3_ID = $get_tml3
              ";
      $tml_3_vital = $this->TabletInputModel->get_by_field('Encounter_ID', $data_template['encounter_ID'], $con)->row();
      $vitals_value[] =  !empty($tml_3_vital->TML3_Value) ? $tml_3_vital->TML3_Value : 0;
    }


    $height     = !empty($vitals_value[0]) ? $vitals_value[0] : 0;
    $weight     = !empty($vitals_value[1]) ? $vitals_value[1] : 0;
    $systolic   = !empty($vitals_value[2]) ? $vitals_value[2] : 0;
    $diastolic  = !empty($vitals_value[3]) ? $vitals_value[3] : 0;


    $patient_vitals = array(
      "password" =>  "",
      "languageIdentifier" => "en",
      "systolic" =>  (int)$systolic,
      "diastolic" =>  (int)$diastolic,
      "weight" => (int)$weight,
      "height" => (int)$height,
      "provider_id" => $provider_id,
    );

    $content    = json_encode($patient_vitals);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    $response = json_decode($json_response, true);

    return $content;
  }

  public function save()
  {
    foreach ($this->current_user as $org_array) {
      $org_id = $org_array['org_id'];
      // $sql = "SELECT  TOP 1 select t1.Encounter_ID,t1.TabletTriggers_ID,t4.AccountNumber,t2.TTPhysician,t3.AdvanceMDTemp_ID from TabletTriggersData as t1 JOIN TabletTriggers AS t2 on t1.TabletTriggers_ID=t2.TabletTriggers_ID JOIN Actone_HwTemplates AS t3 on t1.TabletTriggers_ID=t3.TabletTriggers_ID JOIN PatientProfile as t4 on t1.Patient_ID=t4.Patient_ID where t1.Org_ID=$org_id ORDER BY Appointments.Appointments_ID DESC";
      $sql = "SELECT  TOP 2000 Actone_HwTemplates.AdvanceMDTemp_ID ,EncounterHistory.Encounter_ID , EncounterHistory.ClinicalTriggerView , Appointments.* , TabletTriggers.TabletTriggers_ID FROM Wellness_eCastEMR_Data.dbo.Appointments Appointments JOIN Wellness_eCastEMR_Data.dbo.EncounterHistory  ON EncounterHistory.Appointments_ID=Appointments.Appointments_ID LEFT JOIN Wellness_eCastEMR_Data.dbo.TabletTriggersData TabletTriggers ON TabletTriggers.Encounter_ID = EncounterHistory.Encounter_ID LEFT JOIN Wellness_eCastEMR_Data.dbo.Actone_HwTemplates Actone_HwTemplates on TabletTriggers.TabletTriggers_ID=Actone_HwTemplates.TabletTriggers_ID WHERE TabletTriggers.TabletTriggers_ID IS NULL  AND Appointments.Org_ID=$org_id and EncounterHistory.EncounterSignedOff=1  ORDER BY Appointments.Appointments_ID DESC";


      $this->FreeModel->data_db()->trans_begin();
      $query = $this->FreeModel->data_db()->query($sql);
      // exit(print_r($query->result()));
      foreach ($query->result() as $Appointments) {
        $Appointments_ID = $Appointments->Appointments_ID;
        $Encounter_ID = $Appointments->Encounter_ID;
        $AdvanceMDTemp_ID = $Appointments->AdvanceMDTemp_ID;
        echo '<pre>Appointments_ID =' . $Appointments_ID . '<br>';
        $response = $this->encounter((int) $Encounter_ID, $AdvanceMDTemp_ID, $org_array);
        if ($response) {
          echo 'done';
          exit();
        }
        // exit(print_r($Appointments->Appointments_ID));
      }
    }
  }


  public function encounter($encounter_id = 0, $AdvanceMDTemp_ID, $org_array)
  {
    $ecounter = $this->get_db_encounter_row($encounter_id, $org_array);

    $data['err'] = NULL;
    $data['patient'] = NULL;

    //update view report
    $data_up = array('ClinicalTriggerView' => 1, 'Dept_ID' => $org_array['Dept_ID']);
    $this->EncounterHistoryModel->update($ecounter->Encounter_ID, $data_up);


    $data['patient'] = $this->PatientProfileModel->get_by_id($ecounter->Patient_ID)->row();
    $data['ecounter'] = $ecounter;


    //get trigger master
    $trigger_master = $this->TabletTriggersModel->get_by_data($org_array['org_id'], $ecounter->Provider_ID);
    if ($trigger_master->num_rows() > 0) {
      $trigger_master_result = $trigger_master->result();
    } else {
      $trigger_master = $this->TabletTriggersModel->get_by_data($org_array['org_id'], 0);
      if ($trigger_master->num_rows() > 0) {
        $trigger_master_result = $trigger_master->result();
      } else {
        $trigger_master_result = $this->TabletTriggersModel->get_by_data(0, 0)->result();
      }
    }

    if ($ecounter && $ecounter->Encounter_ID) {
      $trigger_arr = array();
      foreach ($trigger_master_result as $trigger) {
        $query = $trigger->TTCriteria;
        if (!empty($query)) {
          $trigger_arr[] = $trigger->TabletTriggers_ID;
        }
      }

      $this->TabletTriggersDataModel->delete_trigger_data($trigger_arr, $ecounter->Encounter_ID, $ecounter->Patient_ID);
      $this->prep_insert_trgger_data($ecounter, $trigger_arr, $org_array);
      $data['err'] = NULL;
    }


    $data['clinical_trigger'] = NULL;
    if ($data['err'] == NULL) {
      $data['clinical_trigger'] = $this->TabletTriggersDataModel->show_trigger_data($data['patient'], $ecounter);
    }

    $data['partial'] =  "clinical_trigger/show_clinical_trigger";
    // if ($print == "print") {
    $html = $this->opening_report_html();
    if ($data['patient']) {
      $html .= '<h2 class="cts_for">Clinical Triggers for ' . $data['patient']->LastName . ', ' . $data['patient']->FirstName . '</h2>';
    }
    $html .= $this->load->view("clinical_trigger/loop_trigger", $data, TRUE);
    $html .= "</div>";
    $html .= '</body></html> ';

    //echo $html;
    $url_html = "./reports/cts/" . $encounter_id . '.html';
    file_put_contents($url_html, $html);
    //$this->save_as_pdf($encounter_id, $org_array); // $AdvanceMDTemp_ID);

    return $this->send_xml($encounter_id, $org_array);
  }
  private function prep_insert_trgger_data($ecounter, $trigger, $org_array)
  {
    foreach ($trigger as $id) {
      $trigger = $this->TabletTriggersModel->get_by_id($id)->row();
      //print_r($trigger->TTCriteria);
      //echo '<br><br>';
      $check_trigger = $this->TabletTriggersModel->data_tirger($ecounter->Encounter_ID, $trigger->TTCriteria);
      if ($check_trigger == TRUE) {
        $this->insert_trgger_data($ecounter, $trigger, $org_array);
      }
    }
  }
  private function insert_trgger_data($ecounter, $trigger, $org_array)
  {
    $data_inst = array(
      'Org_ID' => $org_array['org_id'],
      'Provider_ID' => $ecounter->Provider_ID,
      'Encounter_ID' => $ecounter->Encounter_ID,
      'Patient_ID' => $ecounter->Patient_ID,
      'TabletTriggers_ID' => $trigger->TabletTriggers_ID,
      'DateAdded' => date('Y-m-d H:i:s'),
      'Hidden' => 0
    );
    //exit(print_r($data_inst));
    $con = 'TabletTriggers_ID = ' . $trigger->TabletTriggers_ID . ' AND Encounter_ID =' . $ecounter->Encounter_ID;
    $check = $this->TabletTriggersDataModel->get_by_field('Patient_ID', (int) $ecounter->Patient_ID, $con);
    if ($check->num_rows() > 0) {
      $check = $check->row();
      // $this->TabletTriggersDataModel->update($check->TabletTriggersData_ID, $data_inst);
    } else {
      $this->TabletTriggersDataModel->insert($data_inst);

      //log
      $id = $this->TabletTriggersDataModel->get_last_insert();
      $patient_id = ($ecounter) ? $ecounter->Patient_ID : 0;
      $ApplicationSpecificText = "Insert CTs";
      // $this->mylib->action_audit_log($ApplicationSpecificText, "CTS", "A", $id, $patient_id);
    }
  }

  private function opening_report_html()
  {
    $html = '<html lang="en"><head><meta charset="utf-8" /> <link href="' . base_url() . 'assets/ace/css/bootstrap.min.css" rel="stylesheet" />
              </head><body>';
    $html .= '<div style="width: 800px; margin: 40px auto;"> ';
    $html .= '<style>

            /*new cts helper*/
            .cts_for{
              margin-bottom: 20px;
              padding-top: 30px;
            }
            .ctshelper2 {
              font-family: "Century Gothic", Tahoma, Geneva, Verdana, sans-serif;
            }

            .ctshelper2 .subtitle {
              font-size: 21px;
              color: rgb(0,102,166) !important;
              text-decoration: underline;
              background-color: rgb(255,255,255);
            }

            .ctshelper2 a:link {
              color: rgb(33,64,154);
              text-decoration: underline;
            }

            .ctshelper2 a:visited {
              color: rgb(33,64,154);
              text-decoration: underline;
            }

            .ctshelper2 a:hover {
              color: rgb(255,255,255);
              background-color: rgb(33,64,154);
            }

            .ctshelper2 a:active {
              color: rgb(33,64,154);
              text-decoration: underline;
            }

            .ctshelper2 .wt1-box {
              border: 1px solid rgb(0,0,0);
              padding: 10px;
              font-family: "Century Gothic" !important;
              font-size: 16px !important;
              color: rgb(0,0,0);
            }

            .ctshelper2 .title_line > span {
              background-color: rgb(171,186,195);
              color: rgb(255,255,255);
              border: none !important;
              padding: 5px !important;
              font-size: 24px !important;
              font-weight: normal !important;
              font-family: "Century Gothic" !important;
            }
            </style>';

    return $html;
  }

  public function send_xml($encounterID, $org_array)
  {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>
<hwdocument>
  <ppmdmsglist>';
    // $sql = "SELECT  Actone_HwTemplates.AdvanceMDTemp_ID ,EncounterHistory.Encounter_ID , EncounterHistory.ClinicalTriggerView , Appointments.* , TabletTriggers.TabletTriggers_ID FROM Wellness_eCastEMR_Data.dbo.Appointments Appointments JOIN Wellness_eCastEMR_Data.dbo.EncounterHistory  ON EncounterHistory.Appointments_ID=Appointments.Appointments_ID LEFT JOIN Wellness_eCastEMR_Data.dbo.TabletTriggersData TabletTriggers ON TabletTriggers.Encounter_ID = EncounterHistory.Encounter_ID LEFT JOIN Wellness_eCastEMR_Data.dbo.Actone_HwTemplates Actone_HwTemplates on TabletTriggers.TabletTriggers_ID=Actone_HwTemplates.TabletTriggers_ID WHERE TabletTriggers.TabletTriggers_ID IS NULL  AND Appointments.Org_ID=$org_id  ORDER BY Appointments.Appointments_ID DESC";

    $sql = "SELECT PatientProfile.AccountNumber, TabletTriggersData.*,Actone_HwTemplates.*,TabletTriggers.* FROM Wellness_eCastEMR_Data.dbo.TabletTriggersData left join Wellness_eCastEMR_Data.dbo.Actone_HwTemplates as Actone_HwTemplates on TabletTriggersData.TabletTriggers_ID=Actone_HwTemplates.TabletTriggers_ID left join Wellness_eCastEMR_Data.dbo.TabletTriggers on TabletTriggersData.TabletTriggers_ID=TabletTriggers.TabletTriggers_ID left join Wellness_eCastEMR_Data.dbo.PatientProfile on TabletTriggersData.Patient_ID=PatientProfile.Patient_ID where Actone_HwTemplates.Trigger_active=1 and  TabletTriggersData.Encounter_ID=$encounterID";
    $this->FreeModel->data_db()->trans_begin();
    $query = $this->FreeModel->data_db()->query($sql);
    // exit(print_r($query->result()));
    foreach ($query->result() as $triggers) {
      $TTPhysician = $triggers->TTPhysician;
      $Encounter_ID = $triggers->Encounter_ID;
      $AccountNumber = $triggers->AccountNumber;
      $AdvanceMDTemp_ID = $triggers->AdvanceMDTemp_ID;

      $xml .= '
    <ppmdmsg action="addehrhwplan" class="api" msgtime="' . date("m/d/Y H:i:s A") . '" templateid="' . $AdvanceMDTemp_ID . '">
      <hmplanlist>
        <hmplan Title="ActOneGap" Item_Type="GAPSINCARE" Text="' . $TTPhysician . '" ApptTypeFID="7" Autocreateflag="0" Autoinprogressflag="0" Autocompleteflag="0" Start_Num="0" Start_Var="Days" Frequency_Num="90" Frequency_Var="Days" Numtimes="0" patientid="' . $AccountNumber . '"/>
      </hmplanlist>
    </ppmdmsg>';
    }
    $xml .= '
  </ppmdmsglist>
</hwdocument>';

    if ($query->result()) {
      $url = 'https://api-02.hendricksongroup.net/message/Send';

      $curl = curl_init($url);
      // curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);

      $authKey = $org_array['authKey']; // "QJVMa9Pk8zdzO1xE1TsiE4A38wXRZX4LKMc";
      $authHeaders = array();
      $authHeaders[] = 'Content-Type: text/plain';
      $authHeaders[] = 'Authorization: ' . $authKey;
      curl_setopt($curl, CURLOPT_HTTPHEADER, $authHeaders);

      $json_response = curl_exec($curl);
      $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      if (curl_errno($curl)) {
        $error_msg = curl_error($curl);
        print_r($error_msg);
      }
      curl_close($curl);
      $response = json_decode($json_response, true);
      return true;
    } else {
      return false;
    }
  }

  function signedReport()
  {
    foreach ($this->current_user as $org_array) {
      $org_id = $org_array['org_id'];
      $sql = "SELECT TOP 2 * FROM [Wellness_eCastEMR_Data].[dbo].[EncounterHistory] where UpdatedEncounter=1 and Org_ID=$org_id";
      $this->FreeModel->data_db()->trans_begin();
      $query = $this->FreeModel->data_db()->query($sql);
      foreach ($query->result() as $Appointments) {
        $Encounter_ID = $Appointments->Encounter_ID;
        $this->generate_signed_report((int) $Encounter_ID, $org_array);
      }
      $this->FreeModel->data_db()->trans_commit();
    }
  }

  public function generate_signed_report($encounterKey = 0, $org_array)
  {
    $dt = $this->get_db_encounter_row($encounterKey, $org_array);


    // $this->save_encounter_ajax($dt);
    $this->PhqModel->check_all_phq($dt->Encounter_ID);

    $this->EncounterHistoryModel->process_hcc($dt->Encounter_ID);

    $check =  $this->ReportLogModel->select_db()
      ->where('ID', (int) $encounterKey)
      ->where('TableName', 'EncounterHistory')
      ->get()->num_rows();

    $check2 = $this->ReportLogModel->select_db()
      ->where('ID', (int) $encounterKey)
      ->where('TableName', 'EncounterHistory')
      ->where('DateUpdated is NULL')
      ->get()->num_rows();

    if ($dt->EncounterSignedOff != 1 || $check == 0 || $check2 > 0) {
      $dt->EncounterSignedOff = 1;
      $dt->SignedOffSupervising = 1;
      $dt->SupProvider_Id = 2774;
      $dt->RenderingSignedOffDate = date('Y-m-d H:i:s');
      $dt->SupervisingSignedOffDate = date('Y-m-d H:i:s');
      // echo 'dtttt<br>';
      // print_r($dt);
      // echo '<br>';
      // exit(print_r('EncounterSignedOff != 1'));

      $update = array(
        'UpdatedEncounter' => 0,
        'EncounterSignedOff' => 1,
        'SignedOffSupervising' => 1,
        'SupProvider_Id' => 2774,
        'Dept_ID' => $org_array['Dept_ID'],
        'RenderingSignedOffDate' => date('Y-m-d H:i:s'),
        'SupervisingSignedOffDate' => date('Y-m-d H:i:s'),
      );
      $this->EncounterHistoryModel->update($dt->Encounter_ID, $update);

      $this->generate_all_reports($org_array, $dt, 'PROVIDER', 0, 0);



      // $this->generate_all_reports($dt, 'PATIENT', 1, 0);
      // $this->generate_all_reports($dt, 'SUMMARY', 0, 1);
    }

    //xml generate
    $this->save_report('provider', $encounterKey, 'xml', 1, $org_array);

    $status = "success";
    // $msg = $this->mylib->get_client() . " Report has been generated";

    // // if ($json) {
    // echo json_encode(array(
    //   'status' => $status,
    //   'msg' => $msg
    // ));
    // exit();
    // }
  }
}
