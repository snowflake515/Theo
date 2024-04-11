<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Service extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->check_user();
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
                  . '<td width=150>&nbsp; <i class="icon icon-time"></i>&nbsp; &nbsp; ' . anchor('patient/demographics/' . $val->Patient_ID, mb_convert_encoding($val->LastName . ', ' . $val->FirstName, 'UTF-8', 'UTF-8')) . '</td>'
                  . '<td width=150><strong>Provider </strong>' . $provider->ProviderLastName . ', ' . $provider->ProviderFirstName . '</td>'
                  . '<td width=150><strong>Status </strong>' . $checkin_desc . '</td>'
                  . '<td width=250>' . "<span  style='background: #$color; width: $width;margin-left:10px; border-bottom:1px dashed #000;height:20px; display: inline-block;'>&nbsp;</span>" . '&nbsp;&nbsp;&nbsp;&nbsp;<strong>DOB</strong> ' . date("m/d/Y", strtotime($val->DOB)) . '</td>'
                  . '<td width=150><strong>Phone </strong>' . $phone . '</td>'
                  . '<td width=150><strong>ACCT </strong>' . $acc_num . '</td>'
                  . '</tr>'
                  . '</table>';
        } else {
          $title = '&nbsp; <i class="icon icon-time"></i>&nbsp; &nbsp; ' . anchor('patient/demographics/' . $val->Patient_ID, mb_convert_encoding($val->LastName . ', ' . $val->FirstName, 'UTF-8', 'UTF-8')) . '<br/> <strong>Provider :</strong> &nbsp;   ' . $provider->ProviderLastName . ', ' . $provider->ProviderFirstName
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

      $add_css = $this->call_add_css();
      $return_dt_mounth = array('dt' => $dt, 'add_css' => $add_css);
      $this->json_output($return_dt_mounth);
    }
  }

  private function call_add_css() {
    $str = "";
    $bg_color = '#FFFFFF';
    $date_start = $this->input->post('start');
    $date_end = $this->input->post('end');
    $days = array(
        'Sunday' => 1,
        'Monday' => 2,
        'Tuesday' => 3,
        'Wednesday' => 4,
        'Thursday' => 5,
        'Friday' => 6,
        'Saturday' => 7,
    );
    $datename = date('l', strtotime($date_start));

    $option = array(
        99 => 'fc-day',
        1 => 'fc-sun',
        2 => 'fc-mon',
        3 => 'fc-tue',
        4 => 'fc-wed',
        5 => 'fc-thu',
        6 => 'fc-fri',
        7 => 'fc-sat',
    );
    $con = "(Hidden is NULL or Hidden = 0)
              AND (
                (CaseDate is not NULL) OR
                (DateBlock BETWEEN '$date_start' AND '$date_end')
              )";
    $list = $this->DateBlockModel->get_by_field('Org_ID', $this->current_user->Org_Id, $con);
    if ($list) {
      foreach ($list->result() as $ls) {
        if (!empty($option[$ls->CaseDate])) {
          $cls = $option[$ls->CaseDate];
          $str .= "td.$cls{background: $bg_color}";
        } else {
          $cls = 'fc-day[data-date="' . $ls->DateBlock . '"]';
          $str .= "td.$cls{background: $bg_color}";
        }
      }
    }



    $con = "(Hidden is NULL or Hidden = 0)
              AND (
                (CaseDate = 99 AND CaseTime = 1) OR
                (CaseDate = $days[$datename] AND CaseTime = 1) OR
                (DateBlock = '$date_start' AND CaseTime = 1)
              )";
    $list = $this->DateBlockModel->get_by_field('Org_ID', $this->current_user->Org_Id, $con);
    if ($list->num_rows() > 0) {
      $str .= "table.fc-agenda-slots{background: $bg_color}";
    } else {
      $s_time = strtotime('00:00');
      for ($x = 0; $x <= 47; $x++) {
        $cls = 'fc-slot' . $x;
        $temp_time = date("H:i", $s_time);
        $con = "(Hidden is NULL or Hidden = 0)
              AND (
                (CaseDate = 99 AND '$temp_time' BETWEEN TimeStart AND TimeEnd) OR
                (CaseDate = $days[$datename]  AND '$temp_time' BETWEEN TimeStart AND TimeEnd) OR
                (DateBlock = '$date_start' AND '$temp_time' BETWEEN TimeStart AND TimeEnd)
              )";
        $list = $this->DateBlockModel->get_by_field('Org_ID', $this->current_user->Org_Id, $con);
        if ($list->num_rows() > 0) {
          $str .= "tr.$cls td.fc-widget-content{background: $bg_color}";
        }
        $s_time = strtotime('+30 minutes', $s_time);
      }
    }


    return $str;
  }

  private function get_month() {
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
    $add_css = $this->call_add_css();
    $return_dt_mounth = array('dt' => $dt, 'add_css' => $add_css);
    $this->json_output($return_dt_mounth);
  }

  function appointment_detail($id = 0) {
    $data['dt'] = $this->AppointmentModel->get_by_id($id)->row();
    $data['patient'] = $this->PatientProfileModel->get_by_id($data['dt']->Patient_ID)->row();
    $data['get_last_check'] = $this->CheckInLogModel->get_last_check($id)->row();
    $data['checkin_code'] = $this->CheckInCodeModel->get_by_field('Org_Id', $this->current_user->Org_Id)->result();
    $html = $this->load->view('schedule/appointment_detail', $data, TRUE);
    $this->json_output(array('html' => $html));
  }

  function eligibility() {
    $patient_id   = (int) $this->input->post('patient_id');
    $patient      = $this->PatientProfileModel->get_by_id($patient_id)->row();
    $provider     = $this->ProviderProfileModel->get_by_id($patient->Provider_ID)->row();


    $result       = NULL;
    $error        = FALSE;
    $html         = NULL;
    $LimitService = 0;

    if ($patient && $provider && ($this->current_user->Org_Id == $patient->Org_ID)) {
      $lastcheck = (!empty($patient->LastCheckService)) ? $patient->LastCheckService : date('Y-m-d');
      $LimitService = (date('Y-m-d', strtotime($lastcheck)) != date('Y-m-d')) ? 1 : $patient->LimitService + 1;
      $this->PatientProfileModel->update($patient_id, array(
        'LastCheckService' => date('Y-m-d'),
        'LimitService' =>   $LimitService
      ));


      if($LimitService == 1){
        $client = new SoapClient('https://services.meddatahealth.com/submissionportal/submissionportal.asmx?WSDL', array('soap_version' => SOAP_1_2, 'trace' => 1));


        $headerbody = array(
            'UserName' => 2224958,
            'Password' => 'xGt@Ji=5o9A',
        );


        $header = new SOAPHeader('http://services.medconnect.net/submissionportal', 'SecurityHeader', $headerbody);
        $client->__setSoapHeaders($header);

        $req = '<?xml version="1.0" encoding="UTF-8"?>
              <requests>
              <request requestType="Eligibility">
              <payerId>10001</payerId>
              <providerId>' . $provider->ProviderNPI . '</providerId>
              <subscriberId>' . $patient->MedicalRecordNumber . '</subscriberId>
              <subscriberFirstName>' . $patient->FirstName . '</subscriberFirstName>
              <subscriberLastName>' . $patient->LastName . '</subscriberLastName>
              <subscriberDOB>' . date('Ymd', strtotime($patient->DOB)) . '</subscriberDOB>
         <subscriberGender>'.$patient->Sex.'</subscriberGender>
              </request>
              </requests>';

        try {
          $res = $client->SubmitSync(array(
            'request' => $req,
            'requestFormat' => 'FlatXml',
            'responseFormat' => 'VerboseXml',
            'synchronousTimeout' => '0.00:05:00'));

          $result = $res->SubmitSyncResult;

          //print_r(htmlentities($res->SubmitSyncResult));
        } catch (SoapFault $fault) {
          $html = $fault->faultstring;
          $error = TRUE;
        }
      }else{
        $html  = "Sorry, you have exceeded the number of allowable request attempts";
        $error = TRUE;
      }

    }
    if (!empty($result)) {
      $xml = simplexml_load_string($result);
	    $xml = json_encode($xml);
	    $xml = json_decode($xml,TRUE);

      if(!empty($xml['SubscriberLevelLoop']['SubscriberNameLoop']['SubscriberEligibilityOrBenefitInformationLoop']) ){

        foreach ($xml['SubscriberLevelLoop']['SubscriberNameLoop']['SubscriberEligibilityOrBenefitInformationLoop'] as $s){
          if( (!empty($s['SubscriberEligibilityOrBenefitInformation']['CompMedProcedID'])
          && !empty($s['SubscriberEligibilityBenefitDate']['DateTimePeriod']))
          && ($s['SubscriberEligibilityOrBenefitInformation']['CompMedProcedID'] == 'G0438'
          || $s['SubscriberEligibilityOrBenefitInformation']['CompMedProcedID'] == 'G0439')
          ){
            $DateTimePeriod = array();
            foreach (explode("-", $s['SubscriberEligibilityBenefitDate']['DateTimePeriod']) as $dt){
              $DateTimePeriod[] = date('m/d/Y', strtotime($dt));
            }
            $html = $s['SubscriberEligibilityOrBenefitInformation']['CompMedProcedID']
                    .' benefit begin '.implode(' - ', $DateTimePeriod);
            break;
          }
        }

      }
      if(!empty($xml['SubscriberLevelLoop']['SubscriberNameLoop']['SubscriberRequestValidation']['RejectReasonCode'])){
        $html = $xml['SubscriberLevelLoop']['SubscriberNameLoop']['SubscriberRequestValidation']['RejectReasonCode'];
      }
      if(empty($html)){
        $html = "Not Eligible";
      }
      $this->PatientProfileModel->update($patient_id, array('Notes' => $html));

    } else {
      $error = TRUE;
    }
    $this->json_output(array('html' => $html, 'error' => $error));
  }

  function appointment_history() {
    $patient_id = (int) $this->input->post('patient_id');
    $patient = $this->PatientProfileModel->get_by_id($patient_id)->row();

    $html = "";
    if ($patient && ($this->current_user->Org_Id == $patient->Org_ID)) {
      $data['patient'] = $patient;
      $data['dt'] = $this->AppointmentModel->get_appointments_by_patient($patient_id)->result();
      $html = $this->load->view('patient/service_appointment_history', $data, TRUE);
    }
    $this->json_output(array('html' => $html));
  }

  private function check_user() {
    $this->current_user = $this->sessionlib->current_user_ajax();
    if ($this->current_user == FALSE) {
      $this->output->set_status_header(401, 'Session TimeOut!');
      exit();
    }
  }

  private function json_output($ress) {
    $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($ress));
  }

}
