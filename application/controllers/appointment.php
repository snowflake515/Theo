<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Appointment extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->current_user = $this->sessionlib->current_user();
    $this->self = $this->router->fetch_class();
  }

  function get_day(){
    $data['date'] = ($this->input->get('current_select') != "") ? $this->input->get('current_select') : $this->input->get('change_date');
    $data['date'] = $this->convert_ymd($str = $data['date']);
    $newdata = array('CURRENT_CALENDAR' => $data['date']);
    $this->session->set_userdata($newdata);
      
    $data['checkin_code'] = $this->CheckInCodeModel->get_by_field('Org_Id', $this->current_user->Org_Id)->result();
    $data['appts'] = $this->AppointmentModel->get_appt_month(array(
                'Org_ID' => $this->current_user->Org_Id,
                'Provider_ID' => $this->input->get('Provider_ID'),
                'date' => $data['date']
            ))->result();
    $data['partial'] = $this->self . "/list_get_day";
    $this->load->view('layout', $data);
  }
  
  private function convert_ymd($str = 0) {
    $result = NULL;
    $s = explode('-', $str);
    if ($str && $s && count($s) == 3) {
      $result = $s[2] . '-' . $s[0] . '-' . $s[1];
    }
    return $result;
  }
  
  function destroy($id){
    
    $id = (int) $id;
    $con = '(Hidden = 0 OR Hidden IS NULL) and Org_Id = ' . $this->current_user->Org_Id;
    $dt = $this->AppointmentModel->get_by_field('Appointments_ID', $id, $con)->row();
    if ($dt) {
      $this->AppointmentModel->update($id, array('Hidden' => 1)); 
      $patient_id = (int) $dt->Patient_ID;
      $ApplicationSpecificText = "Deleted Appointment";
      $this->mylib->action_audit_log($ApplicationSpecificText, "AP", "D", $id, $patient_id);
    }  
    
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }

}
