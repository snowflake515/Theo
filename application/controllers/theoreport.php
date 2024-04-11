<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Theoreport extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->current_user = $this->sessionlib->current_user();
    $this->self = $this->router->fetch_class();

    $this->load->model('TheoVitalsModel');
    $this->load->model('SessionCarePlanModel');
    $this->load->model('TheoConnectModel');
  }

  function index(){
    $patient_id = (int) $this->input->get('pid');
    $encounter_id = (int) $this->input->get('eid');
    $encounter_date = $this->input->get('edate');
    $encounter_date =  date('Y-m-d', strtotime($encounter_date));
    $url = URL_AWS_S3; 
    $name_file  = '';
    $session_id = 0;

    $get_session = $this->TheoConnectModel->get_where(
      array('Encounter_ID' => $encounter_id, 'Patient_ID' => $patient_id), 
      '(Encounter_ID is not null and TheoSession_ID is not null )'
    )->row();
    
    $db =  $this->load->database('data', true);
              
    $get_session2 =  $db->select('TOP 1 *')
    ->where(array(' convert(date, EncounterDate) =' => $encounter_date, 'Patient_ID' => $patient_id))
    ->from('TheoResults')->get()->row();                   
    
    if(!empty($get_session->TheoSession_ID)){
      $session_id = (int) $get_session->TheoSession_ID; 
    }elseif(!empty($get_session2->Session_ID)){
      $session_id = (int) $get_session2->Session_ID; 
    }  

    $get_name_file = $this->SessionCarePlanModel->select_db()->where('session_id', $session_id)->get()->row();
    $name_file = !empty($get_name_file->care_plan_filename) ? $get_name_file->care_plan_filename : '';

    if(!empty($name_file)){
      redirect($url.$name_file);
    }else{
      echo 'THEO Care Plan Report Not Found';
    }

    // $get_session = $this->TheoVitalsModel->select_db()
    //   ->select('TOP 1 Session_ID')
    //   ->where('Status', '9')
    //   ->where('(Hidden IS NULL OR Hidden = 0)')
    //   ->where('Patient_ID', $patient_id)
    //   ->where('Org_ID', $this->current_user->Org_Id)
    //   ->order_by('EncounterDate', 'DESC')->get()->row();

    //   if(!empty($get_session->Session_ID)){
    //     try {
    //       $session_id = (int) $get_session->Session_ID;
    //       $get_name_file = $this->SessionCarePlanModel->select_db()->where('session_id', $session_id)->get()->row();
    //     } catch (Exception $e) {
    //     }
    //   }

      

  }
}
