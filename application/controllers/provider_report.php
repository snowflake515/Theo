<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Provider_report extends CI_Controller
{

  function __construct()
  {
    parent::__construct();
    $this->self = 'encounter';
    $this->load->model('TheoConnectModel');
    $this->load->model('TheoVitalsModel');
    $this->load->model('ProviderReportModel');
    $this->load->model('ClinicalTriggerModel');
    $this->load->model('PhqModel'); 
  }

  function generate($session_id)
  {
    $session_id = (int)$session_id; //101095;
    $session = $this->get_session($session_id);
    // echo '<hr>';
    // var_dump($session);  

    $current_user = $this->current_user($session);
    // echo '<hr>';
    // var_dump($current_user);

    $tml1 = $this->get_tml1($session);
    // echo '<hr>';
    // var_dump($tml1);

    $encounter = $this->get_encounter($session, $tml1, $current_user);
    // echo '<hr>';
    // var_dump($encounter);  

    $theo_count = $this->ProviderReportModel->theo_count($session);         
    $current_theo_count =  $this->ProviderReportModel->current_theo_count($tml1, $session, $encounter->Encounter_ID);


    $q = array(
      'ID' => $encounter->Encounter_ID,
      'ReportCategory' => 'PROVIDER',
      'TableName' => 'EncounterHistory'
    ); 
    $check_html = $this->ReportLogModel->select_db()->where($q)->get()->row();

    if($theo_count == $current_theo_count && $check_html){  
      $url_report = $this->save_report('provider', $encounter); 
    }else{
      $this->ProviderReportModel->bulk_template($tml1->TML1_ID, $encounter->Encounter_ID, $session_id);
      $this->ClinicalTriggerModel->process_clinical_trigger($encounter);
      $url_report = $this->generate_report($encounter, $current_user);
      $this->ProviderReportModel->update_theo_count($tml1, $session, $encounter->Encounter_ID, $theo_count); 
    }
    

    if($url_report){
      $url_report = str_replace('./', '',$url_report);
      $ress = array(
        'status' => 'ok',
        'provider_report_url' => base_url($url_report.'?d='.date('YmdHis')),
      );
    }else{
      $ress = array(
        'status' => 'error',
        'message' => 'Ups, We cant reganrate report please try again',
      );
    }
  

    $this->json_output($ress);
  } 
  function test(){
    echo $this->input->get('datestart');
  }

  private function generate_report($encounter, $current_user)
  {
    $dt = $encounter;
    $this->current_user = $current_user;
    $this->PhqModel->check_all_phq($dt->Encounter_ID);
    $this->EncounterHistoryModel->process_hcc($dt->Encounter_ID);
    $this->generate_all_reports($dt, 'PROVIDER', 0, 0);
    return $this->save_report('provider', $dt);
  }


  private function generate_all_reports($dt, $cat, $PrintPatientOnly = 0, $summary_report = false, $print_mode = '')
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

    $html = $this->load->view($data['partial'], $data, true); 

    $q = array(
      'ID' => $dt->Encounter_ID,
      'ReportCategory' => $cat,
      'TableName' => 'EncounterHistory'
    );
	
	$html =  utf8_encode($html);
	
    $data_ins = $q + array(
      'Report' => json_encode(array('html' => $html)),
      'Org_ID' => $this->current_user->Org_Id,
      'User_PK' => $this->current_user->ID,
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


  private function  get_row_report($cat = null, $encounterKey = 0)
  {
    return $this->ReportLogModel->select_db()
      ->where('ID', (int)$encounterKey)
      ->where('TableName', 'EncounterHistory')
      ->where('ReportCategory', strtoupper($cat))
      ->get()->row();
  }

  public function save_report($cat = null, $encounter, $format = null, $is_donwload = 0)
  {
    $encounterKey = $encounter->Encounter_ID;
    $dt = $encounter;
    $url_pdf = FALSE;
    $report =  $this->get_row_report($cat, $dt->Encounter_ID);
    $r = !empty($report->Report) ? json_decode($report->Report) : null;
    if (!empty($r->html)) {

      $patient = $this->PatientProfileModel->get_by_id($dt->Patient_ID)->row(); 
      $html = 'MRN: ' . $patient->MedicalRecordNumber . ' ' . date('m/d/Y');

      $url_footer = "./reports/footer/" . $patient->Patient_ID . ".html";
      file_put_contents($url_footer, $html, LOCK_EX);

      $url_html = "./reports/" . $encounterKey . '.html';
      $url_pdf = "./reports/" . $encounterKey . '.pdf';
      $b_file = 'Provider Report';

      $html_report = $r->html; 
      file_put_contents($url_html, $html_report, LOCK_EX);

       
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
    }
    if ($url_pdf && file_exists($url_pdf)) {
      return $url_pdf;
    } else {
      return false;
    }
  }


  private function data_set()
  {
    $data['data_db'] = $this->load->database('data', true)->database;
    $data['template_db'] = $this->load->database('template', true)->database;
    $data['user_db'] = $this->load->database('user', true)->database;
    $data['audit_db'] = $this->load->database('audit', true)->database;
    $data['image_db'] = $this->load->database('image', true)->database;
    $data['master_db'] = $this->load->database('master', true)->database;
    $data['current_user'] = $this->current_user;
    $data['partial'] = $this->self . "/encounter_generatereport";
    $data['HeaderNeeded'] = false;
    $data['OutputMasterKey'] = 0;
    $data['NeedTemplateHeader'] = true;
    return $data;
  }




  private function get_encounter($session, $tml1, $current_user)
  {
    $encounter = $this->ProviderReportModel->prepare_encounter($session, $tml1, $current_user);

    if ($encounter) {
      return $encounter;
    } else {
      $ress = array(
        'status' => 'error',
        'message' => 'Encounter Not found',
      );
      $this->json_output($ress);
    }
  }


  private function get_tml1($session)
  {
    $get_Theo_Default = $this->Tml1Model->get_by_field(
      'TML1_Org_ID',
      $session->Org_ID,
      array('TML1_Theo_Default' => 1)
    )->row();
	
	$get_Theo_Default_all = $this->Tml1Model->get_by_field(
      'TML1_Org_ID',
      0,
      array('TML1_Theo_Default' => 1)
	)->row();

    if ($get_Theo_Default) {
		
      return $get_Theo_Default;
	  
    }elseif ($get_Theo_Default_all){
		
	  return $get_Theo_Default_all; 
	  
    }else {
		
      $ress = array(
        'status' => 'error',
        'message' => 'No temlplate is setup in this ORG',
      );
      $this->json_output($ress);
    }
  }

  private function current_user($session)
  {
    $user_id = (int)$session->ID;
    $user = $this->UserModel->get_by_id($user_id)->row();
    if ($user) {
      return  $user;
    } else {
      $ress = array(
        'status' => 'error',
        'message' => 'User Not Found',
      );
      $this->json_output($ress);
    }
  }


  private function get_session($session_id)
  {
    $session = $this->ProviderReportModel->get_session($session_id);
    if ($session) {
      return $session;
    } else {
      $ress = array(
        'status' => 'error',
        'message' => 'Session not Found',
      );
      $this->json_output($ress);
    }
  }


  private function json_output($ress)
  {
    $this->output
      ->set_status_header(200)
      ->set_content_type('application/json')
      ->_display(json_encode($ress));
    exit(0);
  }
}
