<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Encounter extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->current_user = $this->sessionlib->current_user();
    $this->self = $this->router->fetch_class();
    $this->load->model('ProviderReportLogModel');
    $this->load->model('PhqModel');
  }

  public function start($id_appt = null, $flag = 0) {
    $get_dt = $this->get_encounter_by_appt($id_appt);
    if($this->input->post('submit') == 'save'){
      $this->save_encounter($get_dt['dt'], $get_dt['appt']);
    }
    if($this->input->post('submit') == 'unlock'){
      $this->unlock($get_dt['dt']);
    }
    if($this->input->post('submit_form') == 'patient_save'){
      $this->patient_save($get_dt['dt']);
    }
    $data['dt'] = $get_dt['dt'];
    $data['appt'] = $get_dt['appt'];
    $data += $this->encounter_get_data($get_dt['dt']);
    $data['partial'] = $this->self . "/encounter_edit";
    if ($flag == 1) {
      redirect('template_v2/start/' . $data['dt']->Encounter_ID);
    }else{
      $this->load->view('layout', $data);
    }
  }

  public function generate_report($encounterKey = 0, $json = true) {
    $dt = $this->get_db_encounter_row($encounterKey, TRUE);
    $this->save_encounter_ajax($dt);
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

    if($dt->EncounterSignedOff != 1 || $check == 0 || $check2 > 0 ){
      $this->generate_all_reports($dt, 'PROVIDER', 0, 0);
      $this->generate_all_reports($dt, 'PATIENT' , 1, 0);
      $this->generate_all_reports($dt, 'CLINICAL' , 0, 1);
    }

    //xml generate
    $this->save_report('provider', $encounterKey , 'xml', 1);

    $status = "success";
    $msg = $this->mylib->get_client()." Report has been generated";

    if($json){
      echo json_encode(array(
          'status' => $status,
          'msg' => $msg
      ));
      exit();
    }

  }

  public function save_report($cat = NULL, $encounterKey = 0, $format = NULL, $is_donwload = 0){
    $dt = $this->get_db_encounter_row($encounterKey);
    $report =  $this->get_row_report($cat, $dt->Encounter_ID);
    $r = !empty($report->Report) ? json_decode($report->Report) : NULL;
    if(empty($r->html)){
      echo 'Please Generate Report First!';
      exit();
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
    } elseif ($print_mode == "CLINICAL") {
      $url_html = "./reports/clinical_reports/" . $encounterKey . '.html';
      $url_pdf = "./reports/clinical_reports/" . $encounterKey . '.pdf';
      $b_file = 'Clinical Report';
    }

    $html_report = $r->html;
    if($print_mode == "SUMMARY"){
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
    if($format == 'xml' && $cat == 'provider' ){
      if(!empty($org->XMLReport) && $org->XMLReport == 1){
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
        $path  = "./reports/xml/".$dt->Org_ID;
        if (!file_exists($path)) {
          mkdir($path, 0777, true);
        }
        $url_xml =  $path.'/'. '[' . SERVER_APP . ']-[' . ucwords($PatientHeader->PatientFullName) . ']-'.$dt->Patient_ID.'-'. $b_file. '.xml';
        $xml_file = file_put_contents($url_xml, $xml_report, LOCK_EX);
        //readfile($url_xml);
        //force_download('[' . SERVER_APP . ']-[' . ucwords($PatientHeader->PatientFullName) . ']'.'.xml', $xml_file);
      }
    }elseif($format == 'autoproviderreport' && $cat == 'provider' ){
      if (file_exists($url_pdf)) {
        $fold = './reports/provider_reports/'.$org->Org_ID;
        if(!file_exists($fold)){
          mkdir($fold,  0777, true);
        }
        $npf= 'WT1PR_'.date('Ymd').'_'.str_replace(' ','',  $PatientHeader->FirstName.' '.$PatientHeader->LastName).'_'.$encounterKey.'_'.$PatientHeader->AccountNumber  ;
        copy ( $url_pdf , $fold.'/'.$npf. '.pdf');
        $this->ProviderReportLogModel->insert(
          array(
            'PatientFname' => $PatientHeader->FirstName,
            'PatientLname' => $PatientHeader->LastName,
            'Encounter_ID' => $encounterKey,
            'MedicalRecordNumber' => $PatientHeader->MedicalRecordNumber,
            'Patient_ID' => $Encounter_dt->Patient_ID,
            'Org_ID' => $this->current_user->Org_Id,
            'Report_name' => $npf.'.pdf',
            'AccountNumber' => $PatientHeader->AccountNumber,
            'Record_Created' => date('Y-m-d H:i:s'),
            'Record_Updated' => date('Y-m-d H:i:s')
          )
        );
      }
    }
    else{
      if (file_exists($url_pdf)) {
        $pdf = file_get_contents($url_pdf);
        force_download('[' . SERVER_APP . ']-[' . ucwords($PatientHeader->PatientFullName) . ']-' . $b_file. '.pdf', $pdf);
      } else {
        echo $url_pdf;
        // echo "Please Generate Report First!111";
      }
    }
  }

  public function report($cat = NULL, $encounterKey = 0){
    $dt = $this->get_db_encounter_row($encounterKey);
    $this->EncounterHistoryModel->update($dt->Encounter_ID, array('[AWACSDate]' => date('Y-m-d H:i:s')));
    $report =  $this->get_row_report($cat, $dt->Encounter_ID);
    $r = !empty($report->Report) ? json_decode($report->Report) : NULL;
    echo !empty($r->html) ? $r->html : 'Please Generate Report First!' ;
  }

  private function  get_row_report($cat = NULL, $encounterKey = 0){
    return $this->ReportLogModel->select_db()
              ->where('ID', (int) $encounterKey)
              ->where('TableName', 'EncounterHistory')
              ->where('ReportCategory', strtoupper($cat))
              ->get()->row();
  }
  
  function  debug_report($encounterKey = 0){
	  $dt = $this->get_db_encounter_row($encounterKey, TRUE);
	  echo $this->generate_all_reports($dt, 'PROVIDER', 0, 0);
  }


  private function generate_all_reports($dt, $cat , $PrintPatientOnly = 0, $summary_report = false, $print_mode = ''){
    $data = $this->data_set();
    $data['id'] = $dt->Encounter_ID;
    $data['Encounter_Id'] = $dt->Encounter_ID;
    $data['Encounter_dt'] = $dt;

    $data['print_mode'] = $print_mode;
    $data['PrintPatientOnly'] = $PrintPatientOnly;
    $data['Field'] = $cat;
    if($summary_report){
      $data['summary_report'] = $summary_report;
    }

    $html = $this->load->view($data['partial'], $data, TRUE);

    $q = array(
      'ID' => $dt->Encounter_ID,
      'ReportCategory' => $cat,
      'TableName' => 'EncounterHistory'
    );
	
	$html =  utf8_encode($html);
    $data_ins = $q+array(
      'Report' => json_encode(array('html' => $html)),
      'Org_ID' => $this->current_user->Org_Id,
      'User_PK' => $this->current_user->ID,
      'DateUpdated' => date('Y-m-d H:i:s')
    );  
	 
    $check = $this->ReportLogModel->select_db()->where($q)->get()->row();
    if(!empty($check)){
	  $this->ReportLogModel->update($check->ReportLog_ID, $data_ins);
    }else{
      $data_ins['DateCreated'] = date('Y-m-d H:i:s');
      $this->ReportLogModel->insert($data_ins);
    }

    return $html;

  }

  private function get_db_encounter_row($encounterKey = null, $is_ajax = FALSE){
    $dt = $this->EncounterHistoryModel->select_db()
    ->where('Org_ID', $this->current_user->Org_Id)
    ->where($this->EncounterHistoryModel->key, (int)$encounterKey)
    ->get()->row();
    if(empty($dt)){
      if($is_ajax){
        $this->json_generate_error(array());
      }else{
        $this->encounter_not_found();
      }
    }
    return $dt;
  }

  private function json_generate_error($arr){
    echo json_encode(array(
        'status' => "fail",
        'msg' => $this->mylib->get_client()." Report failed to generated"
    ));
    exit();
  }


  private function data_set(){
    $data['data_db'] = $this->load->database('data', TRUE)->database;
    $data['template_db'] = $this->load->database('template', TRUE)->database;
    $data['user_db'] = $this->load->database('user', TRUE)->database;
    $data['audit_db'] = $this->load->database('audit', TRUE)->database;
    $data['image_db'] = $this->load->database('image', TRUE)->database;
    $data['master_db'] = $this->load->database('master', TRUE)->database;
    $data['current_user'] = $this->current_user;
    $data['partial'] = $this->self . "/encounter_generatereport";
    $data['HeaderNeeded'] = FALSE;
    $data['OutputMasterKey'] = 0;
    $data['NeedTemplateHeader'] = TRUE;
    return $data;
  }


  private function save_encounter_ajax($dt) {
    $dt_post = array(
        'ChiefComplaint' => $this->input->post('ChiefComplaint'),
        'Dept_ID' => $this->input->post('Dept_ID'),
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
    if(!empty($_POST['Provider_ID'])){
      $this->EncounterHistoryModel->update($dt->Encounter_ID, $dt_post);
      $this->AppointmentModel->update($dt->Appointments_ID, $dt_update);
    }
  }

  private function unlock($dt) {
    if ($this->mylib->only_supper_admin()) {
      $update = array(
          'EncounterSignedOff' => 0,
          'SignedOffSupervising' => 0,
          'RenderingSignedOffDate' => NULL,
          'SupervisingSignedOffDate' => NULL,
      );
      $this->EncounterHistoryModel->update($dt->Encounter_ID, $update);
      redirect('encounter/start/' . $dt->Appointments_ID);
    }
  }

  private function save_encounter($dt_en, $appt){
    $org = $this->OrgProfileModel->get_by_id($this->current_user->Org_Id)->row();
    if(!empty($dt_en->Encounter_ID) && $dt_en->EncounterSignedOff != 1){
      $this->form_validation->set_rules($this->EncounterHistoryModel->validation());
      $this->form_validation->set_error_delimiters(ERRORS_STYLE_OPEN, ERRORS_STYLE_END);
      if ($this->form_validation->run() == TRUE) {
        $dt_post= $this->data_post();
        $this->EncounterHistoryModel->update($dt_en->Encounter_ID, $dt_post);

        //if sign off
        if($dt_post['EncounterSignedOff'] == 1){ 
          $list = $this->ReportLogModel->select_db()
                    ->where('ID', (int) $dt_en->Encounter_ID)
                    ->where('TableName', 'EncounterHistory')
                    ->get()->result();
          foreach ($list as $key => $v) {
            $this->ReportLogModel->update($v->ReportLog_ID, array('DateUpdated' => null));
          }
          $this->generate_report($dt_en->Encounter_ID, false);
          //auto generate
          if($org->AutoGenerateReport == 1){
            $this->save_report('provider', $dt_en->Encounter_ID , 'autoproviderreport', 1);
          }
        }
        //log
        $ApplicationSpecificText = "Update Encounter";
        $this->mylib->action_audit_log($ApplicationSpecificText, "E", "U", $dt_en->Encounter_ID, $dt_en->Patient_ID);

        //set default calendar
        $this->session->set_userdata('CURRENT_CALENDAR', date('Y-m-d', strtotime($appt->ApptStart)));
        redirect('encounter/start/' . $appt->Appointments_ID);
      }
    }
  }

  private function data_post(){
    $RenderingSignedOffDate = !empty($_POST['params']['Rendering_id']) ? date('Y-m-d H:i:s') : NULL;
    $SupervisingSignedOffDate = !empty($_POST['SignedOffSupervising']) ? date('Y-m-d H:i:s') : NULL;
    $SignedOffSupervising = !empty($_POST['SignedOffSupervising']) ? 1 : 0;
    $EncounterSignedOff = !empty($_POST['SignedOffSupervising']) && !empty($_POST['params']['Rendering_id']) ? 1 : 0;
    return  array(
      'EncounterDate' => $this->input->post('EncounterDate'),
      'ChiefComplaint' => $this->input->post('ChiefComplaint'),
      'EncounterDescription_ID' => $this->input->post('EncounterDescription_ID'),
      'Provider_ID' => $this->input->post('Provider_ID'),
      'SupProvider_ID' => $this->input->post('SupProvider_ID'),
      'Dept_ID' => $this->input->post('Dept_ID'),
      'Facility_ID' => $this->input->post('Facility_ID'),
      'Org_ID' => $this->current_user->Org_Id,
      'Users_PK' => $this->current_user->ID,
      'RenderingSignedOffDate' => $RenderingSignedOffDate,
      'SignedOffSupervising' => $SignedOffSupervising,
      'SupervisingSignedOffDate' => $SupervisingSignedOffDate,
      'EncounterSignedOff' => $EncounterSignedOff
    );
  }

  private function patient_save($dt) {
    if($dt->EncounterSignedOff != 1){
      $this->load->helper('email');
      $data_update['PhoneCell'] = $this->input->post('PhoneHome');
      $patient = $this->PatientProfileModel->get_by_id($dt->Patient_ID)->row();
      if($patient && empty($patient->PhoneHome)){
        $data_update['PhoneHome'] = $this->input->post('PhoneHome');
      }
      if (valid_email($this->input->post('Email'))) {
        $data_update['Email'] = $this->input->post('Email');
      }
      $this->PatientProfileModel->update($dt->Patient_ID, $data_update);
      redirect('template_v2/start/' . $dt->Encounter_ID );
    }
  }

  private function get_encounter_by_appt($appt_id = null){
    $appt = $this->AppointmentModel->select_db()
    ->where('Appointments_ID', (int)$appt_id)
    ->where('Org_ID', $this->current_user->Org_Id)
    ->where('(Hidden is null or Hidden <> 1)')->get()->row();
    $get_err = TRUE;
    if(!empty($appt->Appointments_ID)){
      $dt = $this->encounter_by_appt($appt);
      if(!empty($dt)){
        $get_err = FALSE;
        return array('appt' => $appt, 'dt' => $dt);
      }
    }
    if($get_err){
      $this->encounter_not_found();
    }
  }

  private function get_encounter($appt){
    $dt = $this->EncounterHistoryModel->select_db()
    ->select('EncounterHistory.*, org.UseProviderType')
    ->where('Appointments_ID', (int)$appt->Appointments_ID)
    ->where('EncounterHistory.Org_ID', $this->current_user->Org_Id)
    ->join('OrgProfile as org', 'org.Org_ID = EncounterHistory.Org_ID')
    ->order_by($this->EncounterHistoryModel->key, 'desc')
    ->get()->row();
    return $dt;
  }

  private function encounter_by_appt($appt){
    $dt = $this->get_encounter($appt);
    if(empty($dt)){
      $this->insert_encounter($appt);
      $dt = $this->get_encounter($appt);
    }
    return $dt;
  }

  private function encounter_not_found(){
    $data['partial'] = $this->self . "/encounter_not_found";
    echo $this->load->view('layout', $data, TRUE);
    exit();
  }

  private function insert_encounter($dt_app){
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
  }


  private function encounter_get_data($dt) {
    $con = '(Hidden = 0 OR Hidden IS NULL)';
    $org = $this->OrgProfileModel->get_by_id($this->current_user->Org_Id)->row();
    $data['sm'] = (!empty($org->SummarySheet) && $org->SummarySheet == 1) ? TRUE : FALSE;
    $provider_id = set_value('Provider_ID', $dt->Provider_ID);
    $p = (!empty($provider_id)) ? ' AND Provider_ID = '. $provider_id : '';
    $data['encounter'] = $this->EncounterDescriptionListModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con.$p);
    $con2 = $dt->UseProviderType == 1 ? $con.'and RenderingProvider = 1' : $con;
    $data['provider'] = $this->ProviderProfileModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con2);
    $con2 = $dt->UseProviderType == 1 ? $con.'and (RenderingProvider is null or RenderingProvider <> 1)' : $con;
    $data['sprovider'] = $this->ProviderProfileModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con2);
    $data['facility'] = $this->FacilityListModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con);
    $data['department'] = $this->Deptprofilemodel->get_by_field('Org_Id', $this->current_user->Org_Id, $con);
    $data['encounter_history'] = $this->EncounterHistoryModel->patient_history("EncounterHistory.Patient_ID", $dt->Patient_ID)->result();
    $data['patient'] = $this->PatientProfileModel->get_by_id($dt->Patient_ID)->row();
    if(empty($data['patient'])){
      $this->encounter_not_found();
    }
    return $data;
  }

}
