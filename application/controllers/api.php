<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Api extends CI_Controller {

  function __construct() {
    parent::__construct();
    $not_required_login = array();
    $this->current_user = $this->sessionlib->current_user('json');
    $this->self = $this->router->fetch_class();
  }
  
  function check_login(){
    return false;
  }

  function get_appointments() {
    if ($this->input->post('view') == "agendaDay") {
      $newdata = array('CURRENT_CALENDAR' => $this->input->post('start'));
      $this->session->set_userdata($newdata);
    }
    if ($this->input->post('view') == "month") {
      $this->get_month();
    } else {

      $dt = array();
      $data_api = $this->AppointmentModel->get_appointments_by_org_id(array(
                  'Org_ID' => $this->current_user->Org_Id,
                  'ApptStart' => $this->input->post('start'),
                  'ApptStop' => $this->input->post('end'),
                  'Provider_ID' => $this->input->post('provider')
              ))->result();
      foreach ($data_api as $val) {
        $get_last_check = $this->CheckInLogModel->get_last_check($val->Appointments_ID)->row();
        $provider = $this->ProviderProfileModel->get_by_id($val->Provider_ID)->row();
        $checkIn_codes = $this->CheckInCodeModel->get_by_field('CodeOrder', ($get_last_check) ? $get_last_check->CodeOrder : 0, array('Org_Id' => $this->current_user->Org_Id))->row();
        $color = ($checkIn_codes) ? $checkIn_codes->Color : NULL;
        $width = ($checkIn_codes) ? '40%' : 0;
        $phone = (!empty($val->PhoneHome)) ? $val->PhoneHome : $val->PhoneWork;
        $phone = iconv('UTF-8', 'UTF-8//IGNORE', sprintf('%s', $phone));
        $acc_num = sprintf('%s', $val->AccountNumber);
        
        $checkin_desc = (!empty($checkIn_codes->Description)) ? $checkIn_codes->Description : "";
        
        if ($this->input->post('view') == "agendaDay") {
          
          $title = '<table>'
                  . '<tr>'
                  . '<td width=150>&nbsp; <i class="icon icon-time"></i>&nbsp; &nbsp; ' . anchor('patient/demographics/'.$val->Patient_ID, mb_convert_encoding($val->LastName . ', ' . $val->FirstName, 'UTF-8', 'UTF-8')) . '</td>'
                  . '<td width=150><strong>Provider </strong>' . $provider->ProviderLastName . ', ' . $provider->ProviderFirstName . '</td>'
                  . '<td width=150><strong>Status </strong>' . $checkin_desc . '</td>'
                  . '<td width=250>' . "<span  style='background: #$color; width: $width;margin-left:10px; border-bottom:1px dashed #000;height:20px; display: inline-block;'>&nbsp;</span>" . '&nbsp;&nbsp;&nbsp;&nbsp;<strong>DOB</strong> '.date("m/d/Y", strtotime($val->DOB)).'</td>'
                  . '<td width=150><strong>Phone </strong>' . $phone . '</td>'
                  . '<td width=150><strong>ACCT </strong>' . $acc_num . '</td>'
                  . '</tr>'
                  . '</table>';
        } else {
          $title = '&nbsp; <i class="icon icon-time"></i>&nbsp; &nbsp; ' . anchor('patient/demographics/'.$val->Patient_ID, mb_convert_encoding($val->LastName . ', ' . $val->FirstName, 'UTF-8', 'UTF-8')) . '<br/> <strong>Provider :</strong> &nbsp;   ' . $provider->ProviderLastName . ', ' . $provider->ProviderFirstName
                  . '<br/><strong>Status :</strong> &nbsp;  ' . $checkin_desc
                  . "<br/><span  style='background: #$color; width: $width; border-bottom:1px dashed #000;height:20px; display: inline-block;'>&nbsp;</span>"
                  . '<br/><strong>DOB :</strong> &nbsp;  ' . date("m/d/Y", strtotime($val->DOB))
                  . '<br/><strong>Phone :</strong> &nbsp;  ' . $phone 
                  . '<br/><strong>ACCT :</strong> &nbsp;  ' . $acc_num;
        }
        $end_date = date("Y-m-d\TH:i:s", strtotime($val->ApptStop));
        $start_date = date("Y-m-d\TH:i:s", strtotime($val->ApptStart));
        if ($end_date == $start_date || $val->ApptStop <= $val->ApptStart) {
          $temp_date = strtotime($val->ApptStart);
          $end_date = date("Y-m-d\TH:i:s", strtotime('+30 minutes', $temp_date));
        }
        $dt[] = array(
            'id' => $val->Appointments_ID,
            'title' => $title,
            'start' => $start_date,
            'end' => $end_date,
            'allDay' => FALSE
        );
      }

      echo json_encode($dt);
    }
  }

  function get_month() {
    $dt = array();
    //  $num = cal_days_in_month(CAL_GREGORIAN, 11, 2013); // 31
    $start = $this->input->post('start');
    $end = $this->input->post('end');
    $i = 0;
    while (strtotime($start) <= strtotime($end)) {
      $date = date("Y-m-d", strtotime($start));
      $jumlah = $this->AppointmentModel->get_appt_month(array(
                  'Org_ID' => $this->current_user->Org_Id,
                  'Provider_ID' => $this->input->post('provider'),
                  'date' => $date
              ))->num_rows();
      if ($jumlah != 0) {
        $jumlah = ($jumlah > 1) ? "$jumlah Appts <br/> <i class='icon icon-book'></i>" : "$jumlah Appt <br/> <i class='icon icon-book'></i>";
        $dt[] = array(
            'id' => $i,
            'title' => "$jumlah",
            'start' => date("Y-m-d\TH:i:s", strtotime($date)),
            'end' => date("Y-m-d\TH:i:s", strtotime($date))
        );
      }
      $start = date("Y-m-d", strtotime("+1 day", strtotime($date)));
      $i++;
    }

    echo json_encode($dt);
  }

  function check_log() {
    $CodeOrder = (int) $this->input->post('CodeOrder'); 
    if($CodeOrder){
      $checkIn_codes = $this->CheckInCodeModel->get_by_field('CodeOrder', ($this->input->post('CodeOrder')) ? $this->input->post('CodeOrder') : 0, array('Org_Id' => $this->current_user->Org_Id))->row();
      $insert = array(
          'Appointments_Id' => $this->input->post('Appointments_Id'),
          'CodeOrder' => $this->input->post('CodeOrder'),
          'CheckIn_DateTime' => date('Y-m-d H:i:s'),
          'Users_PK' => $this->current_user->ID,
          'Description' => $checkIn_codes->Description,
          'Color' => $checkIn_codes->Color
      ); 
      $this->CheckInLogModel->insert($insert);
      echo ($checkIn_codes) ? $checkIn_codes->Color : NULL;
    } 
  }

  function get_encounter_type() {
    $con = 'Provider_ID = ' . $this->input->post('Provider_ID') . 'and (Hidden = 0 OR Hidden IS NULL)';
    $data['dt'] = null;
    $data['encounter'] = $this->EncounterDescriptionListModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con);
    $this->load->view($this->self . '/get_encounter_type', $data);
  }

  function get_patient() {
    $data['patients'] = $this->PatientProfileModel->get_simple_limit(array('Org_Id' => $this->current_user->Org_Id), 5)->result();
    $this->load->view($this->self . '/get_patient', $data);
  }

}
