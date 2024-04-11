<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Encounter2 extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->current_user = $this->sessionlib->current_user();
    $this->self = $this->router->fetch_class();
    exit();
  }

  function start($id_appt = null) {
    if(empty($id_appt)){
      redirect('schedule');
    }
    $data = $this->encounter_get_data();
    $data['dt'] = $this->EncounterHistoryModel->get_last_history($id_appt)->row();
    //var_dump($data['dt']);
    if (!$data['dt']) {
      $this->insert_encounter_history2($id_appt);
      $data['dt'] = $this->EncounterHistoryModel->get_last_history($id_appt)->row();
    }
    $org = $this->OrgProfileModel->get_by_id($this->current_user->Org_Id)->row();
    $data['sm'] = ($org && $org->SummarySheet == 1) ? TRUE : FALSE;

    if ($data['dt']) {
      $p = ($data['dt']) ? 'and Provider_ID = ' . $data['dt']->Provider_ID : NULL;
      $con = '(Hidden = 0 OR Hidden IS NULL) ' . $p;
      $data['encounter'] = $this->EncounterDescriptionListModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con);
      $data['appt'] = $this->AppointmentModel->get_by_id($id_appt)->row();
      $data['patient'] = $this->PatientProfileModel->get_by_id($data['dt']->Patient_ID)->row();
      $data['encounter_history'] = $this->EncounterHistoryModel->patient_history("EncounterHistory.Patient_ID", $data['dt']->Patient_ID)->result();
      $data['partial'] = $this->self . "/encounter_edit";
    } else {
      $data['partial'] = $this->self . "/encounter_not_found";
    }
    $this->load->view('layout', $data);
  }

  function encounter_edit($id) {
    $data = $this->encounter_get_data();
    $data['dt'] = $this->encounterModel->get_by_id($id)->row();
    $data['id'] = $id;
    $data['ecounter_history'] = $this->EncounterHistoryModel->get_last_history($id)->row();
    $data['partial'] = $this->self . "/encounter_edit";
    $this->load->view('layout', $data);
  }

  function encounter_update() {
    $dt_appt = $this->input->post();
    $id = (int) $this->input->post('Encounter_ID');
    $Appointments_ID = $this->input->post('Appointments_ID');
    $template = $this->input->post('template');
    $dt_en = $this->EncounterHistoryModel->get_by_id($id)->row();
    if ($dt_en && $dt_en->EncounterSignedOff == 1) {
      redirect('encounter/start/' . $Appointments_ID);
      exit();
    }

    if (isset($dt_appt['params'])) {
      $dt_appt['RenderingSignedOffDate'] = date('Y-m-d H:i:s');
    }

    if (isset($dt_appt['SignedOffSupervising'])) {
      $dt_appt['EncounterSignedOff'] = 1;
    }

    if (!isset($dt_appt['params']['Rendering_id'])) {
      $dt_appt['RenderingSignedOffDate'] = null;
    }

    unset($dt_appt['Encounter_ID']);
    unset($dt_appt['params']);
    unset($dt_appt['template']);

    $this->form_validation->set_rules($this->EncounterHistoryModel->validation());
    $this->form_validation->set_error_delimiters(ERRORS_STYLE_OPEN, ERRORS_STYLE_END);
    $dt_appt['Org_ID'] = $this->current_user->Org_Id;
    $dt_appt['Hidden'] = 0;
    $dt_appt['Users_PK'] = $this->current_user->ID;
    $dt_appt['EncounterDate'] = (isset($dt_appt['EncounterDate'])) ? $this->convert_ymd($dt_appt['EncounterDate']) : NULL;
    if ($this->form_validation->run() == FALSE) {
      $this->start($this->input->post('Appointments_ID'));
    } else {
      $this->EncounterHistoryModel->update($id, $dt_appt);

      /*
        $dt_update = array(
        'Notes' => $dt_appt['ChiefComplaint'],
        'Facility_ID' => $dt_appt['Facility_ID'],
        'Provider_ID' => $dt_appt['Provider_ID'],
        'EncounterDescription_ID' => $dt_appt['EncounterDescription_ID']
        );
        $this->AppointmentModel->update($Appointments_ID, $dt_update);
       *
       */

      //log
      $patient_id = ($dt_en) ? $dt_en->Patient_ID : 0;
      $ApplicationSpecificText = "Update Encounter";
      $this->mylib->action_audit_log($ApplicationSpecificText, "E", "U", $id, $patient_id);

      $this->session->set_userdata('CURRENT_CALENDAR', $dt_appt['EncounterDate']);

      redirect('encounter/start/' . $Appointments_ID);
    }
  }

  function patient_save() {
    $encounter_id = $this->input->post('Encounter_ID');
    $app_id = $this->input->post('Appointments_ID');
    $Patient_ID = (int) $this->input->post('Patient_ID');
    $data_update['PhoneHome'] = $this->input->post('PhoneHome');
    $this->load->helper('email');
    if (valid_email($this->input->post('Email'))) {
      $data_update['Email'] = $this->input->post('Email');
    }
    $this->PatientProfileModel->update($Patient_ID, $data_update);
    redirect('template_v2/start/' . $encounter_id );
  }

  function unlock($id = 0) {
    $id = (int) $id;
    $dt_en = $this->EncounterHistoryModel->get_by_id($id)->row();
    if ($this->mylib->only_supper_admin() && $dt_en) {
      $update = array(
          'EncounterSignedOff' => 0,
          'SignedOffSupervising' => 0,
          'RenderingSignedOffDate' => NULL,
          'SupervisingSignedOffDate' => NULL,
      );
      $con = array(
          'Encounter_ID' => $id,
          'Org_ID' => $this->current_user->Org_Id
      );
      $Appointments_ID = $dt_en->Appointments_ID;
      $this->EncounterHistoryModel->update_where($update, $con);
      redirect('encounter/start/' . $Appointments_ID);
    } else {
      redirect('/');
    }
  }

  function convert_ymd($str = 0) {
    $result = NULL;
    $s = explode('-', $str);
    if ($str && $s && count($s) == 3) {
      $result = $s[2] . '-' . $s[0] . '-' . $s[1];
    }
    return $result;
  }

  function encounter_destroy($id) {
    $this->encounterModel->delete($id);
    redirect($this->self . '/encounter_new');
  }

  private function encounter_get_data() {
    $con = '(Hidden = 0 OR Hidden IS NULL)';
    $data['encounter'] = $this->EncounterDescriptionListModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con);
    $data['provider'] = $this->ProviderProfileModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con);
    $data['facility'] = $this->FacilityListModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con);
    $data['department'] = $this->Deptprofilemodel->get_by_field('Org_Id', $this->current_user->Org_Id, $con);
    $data['ecounter_history'] = NULL;
    return $data;
  }

  private function insert_encounter_history($dt_ecounter, $dt_appt, $id_encounter) {
    $dt_ecounter["encounters_ID"] = $id_encounter;
    $dt_ecounter['Provider_ID'] = $dt_appt['Provider_ID'];
    $dt_ecounter['Patient_ID'] = $dt_appt['Patient_ID'];
    $dt_ecounter['EncounterSignedOff'] = 0;
    $dt_ecounter['EncounterPrinted'] = 0;
    $this->EncounterHistoryModel->insert($dt_ecounter);

    //log
    $id = $this->EncounterHistoryModel->get_last_insert();
    $ApplicationSpecificText = "Insert Encounter";
    $this->mylib->action_audit_log($ApplicationSpecificText, "E", "A", $id, $dt_appt['Patient_ID']);
  }

  private function insert_encounter_history2($id_app = 0) {
    $dt_app = $this->AppointmentModel->get_by_id($id_app)->row();
    if ($dt_app) {
      $dt_insert = array(
          'EncounterDate' => $dt_app->ApptStart,
          'Provider_ID' => $dt_app->Provider_ID,
          'Appointments_ID' => $dt_app->Appointments_ID,
          'EncounterDescription' => substr($dt_app->Notes, 0, 50),
          'EncounterDescription_ID' => $dt_app->EncounterDescription_ID,
          'Patient_ID' => $dt_app->Patient_ID,
          'ChiefComplaint' => $dt_app->Notes,
          'EncounterSignedOff' => 0,
          'EncounterPrinted' => 0,
          'Hidden' => 0,
          'ChiefComplaint' => $dt_app->Notes,
          'Org_ID' => $dt_app->Org_ID,
          'User_ID' => $this->current_user->User_Id,
          'Facility_ID' => $dt_app->Facility_ID
      );

      $this->EncounterHistoryModel->insert($dt_insert);

      //log
      $id = $this->EncounterHistoryModel->get_last_insert();
      $patient_id = ($dt_app) ? $dt_app->Patient_ID : 0;
      $ApplicationSpecificText = "Insert Encounter";
      $this->mylib->action_audit_log($ApplicationSpecificText, "E", "A", $id, $patient_id);

      //echo $this->EncounterHistoryModel->get_last_insert();
    }
  }

  function report($encounterKey = 0, $print_mode = "", $PrintPatientOnly = 0, $ress = "") {
    $encounter = $this->EncounterHistoryModel->get_by_id((int)$encounterKey)->row();
    if($encounter){
      $this->EncounterHistoryModel->update($encounter->Encounter_ID, array('[AWACSDate]' => date('Y-m-d H:i:s')));
    }
    if ($PrintPatientOnly == 1) {
      $data['id'] = $encounterKey;
      $data['Encounter_Id'] = $encounterKey;
      $data['Encounter_dt'] = $this->EncounterHistoryModel->get_by_id($data['Encounter_Id'])->row();
      $data['print_mode'] = $print_mode;

      $data['data_db'] = $this->load->database('data', TRUE)->database;
      $data['template_db'] = $this->load->database('template', TRUE)->database;
      $data['user_db'] = $this->load->database('user', TRUE)->database;
      $data['audit_db'] = $this->load->database('audit', TRUE)->database;
      $data['image_db'] = $this->load->database('image', TRUE)->database;
      $data['current_user'] = $this->current_user;

      $data['HeaderNeeded'] = FALSE;
      $data['OutputMasterKey'] = 0;
      $data['NeedTemplateHeader'] = TRUE;

      $data['PrintPatientOnly'] = $PrintPatientOnly;

      $data['partial'] = $this->self . "/encounter_report";
      $html = ''; //'<div style="max-width: 672px; margin: 0 auto">';
      $html .= $this->load->view($data['partial'], $data, TRUE);
      $html .= ''; //'</div>';
      $url_html = "./reports/patient_reports/" . $encounterKey . '.html';
      file_put_contents($url_html, $html, LOCK_EX);
      //echo $html;
      /* echo "<script>
        window.setTimeout(function(){
        window.location = '" . site_url('encounter/download_patient_report/' . $encounterKey) . "';
        }, 5000);
        </script>"; */
    } else {
      $url_html = "./reports/" . $encounterKey . '.html';

      if (file_exists($url_html)) {
        $html = file_get_contents($url_html);
        if ($print_mode == 'print') {
          /* echo "<script>
            window.setTimeout(function(){
            window.location = '" . site_url('encounter/save_pdf/' . $encounterKey) . "';
            }, 5000);
            </script>"; */
        }
      } else {
        $html = "Please Generate Report First!";
      }
    }

    if ($ress == 'json') {
      return TRUE;
    } else {
      echo $html;
    }
  }

  function summary_report($encounterKey, $print_mode = "", $PrintPatientOnly = 0, $ress = "") {
    $data['id'] = $encounterKey;
    $data['Encounter_Id'] = $encounterKey;
    $data['Encounter_dt'] = $this->EncounterHistoryModel->get_by_id($data['Encounter_Id'])->row();
    $data['print_mode'] = $print_mode;

    $data['data_db'] = $this->load->database('data', TRUE)->database;
    $data['template_db'] = $this->load->database('template', TRUE)->database;
    $data['user_db'] = $this->load->database('user', TRUE)->database;
    $data['audit_db'] = $this->load->database('audit', TRUE)->database;
    $data['image_db'] = $this->load->database('image', TRUE)->database;
    $data['current_user'] = $this->current_user;
    $data['summary_report'] = TRUE;

    $data['HeaderNeeded'] = FALSE;
    $data['OutputMasterKey'] = 0;
    $data['NeedTemplateHeader'] = TRUE;

    $data['PrintPatientOnly'] = 0;

    $data['partial'] = $this->self . "/encounter_report";
    $html = ''; //'<div style="max-width: 672px; margin: 0 auto">';
    $html .= '<h4>Summary Report</h4>';
    $html .= $this->load->view($data['partial'], $data, TRUE);
    $html .= '<style> @media print {
          .no-print{
            display: none;
          }
        }</style>';
    $st_break = '<div style="height: 20px; margin: 80px auto; width: 7.0in; background: #f2f2f2" class="no-print"></div>';
    $html .= $st_break;
    $html .= '<div style="page-break-after: always;"></div> ';

    $url_html = "./reports/" . $encounterKey . '.html';
    if (file_exists($url_html)) {
      $html .= file_get_contents($url_html);
    } else {
      $html .= "Please Generate Provider Report First!";
    }
    $html .= ''; //'</div>';
    $url_html = "./reports/summary_reports/" . $encounterKey . '.html';
    $html_r = str_replace($st_break, '', $html);
    file_put_contents($url_html, $html_r, LOCK_EX);
    if ($ress == 'json') {
      return TRUE;
    } else {
      echo $html;
    }

    /* echo "<script>
      window.setTimeout(function(){
      window.location = '" . site_url('encounter/download_summary_report/' . $encounterKey) . "';
      }, 5000);
      </script>"; */
  }

  function generate_report($encounterKey = 0) {
    $this->save_encounter();
    $this->EncounterHistoryModel->process_hcc($encounterKey);

    $data['Encounter_Id'] = $encounterKey;
    $data['Encounter_dt'] = $this->EncounterHistoryModel->get_by_id($data['Encounter_Id'])->row();
    $data['data_db'] = $this->load->database('data', TRUE)->database;
    $data['template_db'] = $this->load->database('template', TRUE)->database;
    $data['user_db'] = $this->load->database('user', TRUE)->database;
    $data['audit_db'] = $this->load->database('audit', TRUE)->database;
    $data['image_db'] = $this->load->database('image', TRUE)->database;
    $data['master_db'] = $this->load->database('master', TRUE)->database;
    $data['current_user'] = $this->current_user;
    $data['PrintPatientOnly'] = 0;
    $data['partial'] = $this->self . "/encounter_generatereport";
    $this->load->view($data['partial'], $data, TRUE);
    $this->create_report_html($encounterKey);


    //pateint report
    $this->report($encounterKey, 'print', 1, "json");
    //sumary report
    $this->summary_report($encounterKey, 'print', 0, $ress = "json");


    $url_html = "./reports/" . $encounterKey . '.html';

    $status = "fail";
    $msg = $this->mylib->get_client()." Report failed to generated";
    if (file_exists($url_html)) {
      $status = "success";
      $msg = $this->mylib->get_client()." Report has been generated";
    }
    $ress = array(
        'status' => $status,
        'msg' => $msg
    );
    echo json_encode($ress);

    //redirect("encounter/start/" . $data['Encounter_dt']->Appointments_ID);
  }

  private function save_encounter() {
    $dt_post = $this->input->post();
    $dt_post = array(
        'ChiefComplaint' => $this->input->post('ChiefComplaint'),
        'Dept_ID' => $this->input->post('Dept_ID'),
        'EncounterDate' => $this->input->post('EncounterDate'),
        'EncounterDescription_ID' => $this->input->post('EncounterDescription_ID'),
        'Provider_ID' => $this->input->post('Provider_ID'),
        'SupProvider_ID' => $this->input->post('SupProvider_ID'),
        'Facility_ID' => $this->input->post('Facility_ID'),
    );
    $enc_id = (int) $this->input->post('Encounter_ID');
    $this->EncounterHistoryModel->update($enc_id, $dt_post);

    $dt_update = array(
        'Notes' => $dt_post['ChiefComplaint'],
        'Facility_ID' => $dt_post['Facility_ID'],
        'Provider_ID' => $dt_post['Provider_ID'],
        'EncounterDescription_ID' => $dt_post['EncounterDescription_ID']
    );
    $appt_id = (int) $this->input->post('Appointments_ID');
    $this->AppointmentModel->update($appt_id, $dt_update);
  }

  private function create_report_html($encounterKey = 0, $print_mode = "") {
    $data['id'] = $encounterKey;
    $data['Encounter_Id'] = $encounterKey;
    $data['Encounter_dt'] = $this->EncounterHistoryModel->get_by_id($data['Encounter_Id'])->row();
    $data['print_mode'] = $print_mode;

    $data['data_db'] = $this->load->database('data', TRUE)->database;
    $data['template_db'] = $this->load->database('template', TRUE)->database;
    $data['user_db'] = $this->load->database('user', TRUE)->database;
    $data['audit_db'] = $this->load->database('audit', TRUE)->database;
    $data['image_db'] = $this->load->database('image', TRUE)->database;
    $data['current_user'] = $this->current_user;

    $data['HeaderNeeded'] = FALSE;
    $data['OutputMasterKey'] = 0;
    $data['NeedTemplateHeader'] = TRUE;

    $data['partial'] = $this->self . "/encounter_report";
    $html = ''; //'<div style="max-width: 672px; margin: 0 auto">';
    $html .= $this->load->view($data['partial'], $data, TRUE);
    $html .= ''; //'</div>';

    $url_html = "./reports/" . $encounterKey . '.html';
    file_put_contents($url_html, $html, LOCK_EX);
  }

  function save_pdf($encounterKey = 0, $print_mode = "") {
    $Encounter_dt = $this->EncounterHistoryModel->get_by_id($encounterKey)->row();
    $patient = $this->PatientProfileModel->get_by_id($Encounter_dt->Patient_ID)->row();
    $html = 'MRN: ' . $patient->MedicalRecordNumber . ' ' . date('m/d/Y');

    $url_footer = "./reports/footer/" . $patient->Patient_ID . ".html";
    file_put_contents($url_footer, $html, LOCK_EX);

    $url_html = "./reports/" . $encounterKey . '.html';
    $url_pdf = "./reports/" . $encounterKey . '.pdf';

    $b_file = 'Provider Report.pdf';
    if ($print_mode == "patient_report") {
      $url_html = "./reports/patient_reports/" . $encounterKey . '.html';
      $url_pdf = "./reports/patient_reports/" . $encounterKey . '.pdf';
      $b_file = 'Patient Report.pdf';
    } elseif ($print_mode == "summary_report") {
      $url_html = "./reports/summary_reports/" . $encounterKey . '.html';
      $url_pdf = "./reports/summary_reports/" . $encounterKey . '.pdf';
      $b_file = 'Summary Report.pdf';
    }

    $data_db = $this->load->database('data', TRUE)->database;
    $Encounter_dt = $this->EncounterHistoryModel->get_by_id($encounterKey)->row();
    $sql = "Select TOP 1
       AccountNumber,
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
    if (file_exists($url_pdf)) {
      $pdf = file_get_contents($url_pdf);
      force_download('[' . SERVER_APP . ']-[' . ucwords($PatientHeader->PatientFullName) . ']-' . $b_file, $pdf);
    } else {
      echo "Please Generate Report First!";
    }
  }

  function download_patient_report($encounterKey) {
    $this->save_pdf($encounterKey, 'patient_report');
  }

  function download_summary_report($encounterKey) {
    $this->save_pdf($encounterKey, 'summary_report');
  }

}
