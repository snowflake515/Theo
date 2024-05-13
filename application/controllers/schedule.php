<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Schedule extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->current_user = $this->sessionlib->current_user();
    $this->self = $this->router->fetch_class();
  }

  function index($date = null) {
    $con = '(Hidden = 0 OR Hidden IS NULL)';
    $data['provider'] = $this->ProviderProfileModel->get_by_field('Org_Id', (int) $this->current_user->Org_Id, $con)->result();
    $data['partial'] = $this->self . "/calendar";
    $this->load->view('layout', $data);
  }

  function appointment_detail($id) {
    $data['dt'] = $this->AppointmentModel->get_by_id($id)->row();
    $data['patient'] = $this->PatientProfileModel->get_by_id($data['dt']->Patient_ID)->row();
    $data['get_last_check'] = $this->CheckInLogModel->get_last_check($id)->row();
    $data['checkin_code'] = $this->CheckInCodeModel->get_by_field('Org_Id', $this->current_user->Org_Id)->result();
    $this->load->view($this->self . '/appointment_detail', $data);
  }

  function appointment_new($id = null) {
    $data['patient'] = $this->PatientProfileModel->get_by_field('Patient_ID', $id, array('Org_Id' => $this->current_user->Org_Id))->row();
    $data['dt'] = null;
    $data['partial'] = $this->self . "/appointment_new";
    $this->load->view('layout', $data);
  }

  private function params_appt() {
    $patient_dt = $this->PatientProfileModel->get_by_field('Patient_ID', $this->input->post('Patient_ID'), array('Org_Id' => $this->current_user->Org_Id))->row();
    $provider_dt = $this->ProviderProfileModel->get_by_field('Provider_ID', $this->input->post('Provider_ID'), array('Org_Id' => $this->current_user->Org_Id))->row();

    $post = array(
        'ApptStart' => $this->convert($this->input->post('ApptStart'), $this->input->post('ApptStartTime')),
        'ApptStop' => $this->convert($this->input->post('ApptStart'), $this->input->post('ApptStopTime')),
        'Org_ID' => $this->current_user->Org_Id,
        'Facility_ID' => $this->input->post('Facility_ID'),
        'Notes' => $this->input->post('Notes'),
        'PMSReason' => $this->input->post('Notes'),
        'EncounterDescription_ID' => $this->input->post('EncounterDescription_ID'),
        'Hidden' => 0,
        'Dept_ID' => $this->current_user->Dept_Id,
        'User_ID' => $this->current_user->User_Id,
        'TOA' => 'WellTrackONE Visit',
        'Users_PK' => $this->current_user->ID,
    );
    log_message('error', json_encode($post, JSON_PRETTY_PRINT));
    if (!empty($patient_dt->Patient_ID)) {
      $patient_post = array(
          'Patient_ID' => $patient_dt->Patient_ID,
          'Patient_FName' => $patient_dt->FirstName,
          'Patient_LName' => $patient_dt->LastName,
          'Patient_DOB' => $patient_dt->DOB,
          'Patient_SSN' => $patient_dt->SSN,
          'Patient_MRN' => $patient_dt->MedicalRecordNumber,
      );
      $post = $post + $patient_post;
    }

    if (!empty($provider_dt->Provider_ID)) {
      $provider_post = array(
          'Provider_ID' => $provider_dt->Provider_ID,
          'Provider_FName' => $provider_dt->ProviderFirstName,
          'Provider_LName' => $provider_dt->ProviderLastName,
          'Provider_UPIN' => $provider_dt->ProviderUPIN,
          'Provider_Number' => $provider_dt->PMS_Pkey,
      );
      $post = $post + $provider_post;
    }

    return $post;
  }

  private function process_encounter($id_appt = 0) {
    $appt = $this->AppointmentModel->get_by_id($id_appt)->row();
    if ($appt) {
      $dt_ecounter = array(
          'EncounterDate' => date('Y-m-d', strtotime($appt->ApptStart)),
          'Provider_ID' => $appt->Provider_ID,
          'Patient_ID' => $appt->Patient_ID,
          'EncounterDescription' => $appt->Notes,
          'ChiefComplaint' => $appt->Notes,
          'EncounterDescription_ID' => $appt->EncounterDescription_ID,
          'EncounterSignedOff' => 0,
          'EncounterNotes' => $appt->Notes,
          'EncounterPrinted' => 0,
          'Facility_ID' => $appt->Facility_ID,
          'User_ID' => $this->current_user->User_Id,
          'Org_ID' => $this->current_user->Org_Id,
          'Dept_ID' => $this->current_user->Dept_Id,
          'Hidden' => 0,
          'Appointments_ID' => $id_appt,
          'Users_PK' => $this->current_user->ID,
          'ClinicalTriggerView' => NULL,
      );

      $cek = $this->EncounterHistoryModel->get_last_history($id_appt);
      if ($cek->num_rows() == 0) {
        $this->EncounterHistoryModel->insert($dt_ecounter);
      } else {
        $cek = $cek->row();
        $this->EncounterHistoryModel->update($cek->Encounter_ID, $dt_ecounter);
      }

      $this->session->set_userdata('CURRENT_CALENDAR', $dt_ecounter['EncounterDate']);
    }
  }

  function appointment_create() {     
    $this->form_validation->set_rules($this->AppointmentModel->validation_create());
    $this->form_validation->set_error_delimiters(ERRORS_STYLE_OPEN, ERRORS_STYLE_END);
    if ($this->form_validation->run() == FALSE) {
      $this->appointment_new($this->input->post('Patient_ID'));
    } else {
      $dt_inset = $this->params_appt();
      $this->AppointmentModel->insert($dt_inset);
      $id_Appointment = $this->AppointmentModel->get_last_insert();
      $this->update_local_time($id_Appointment);
      $this->create_status($id_Appointment);
      $this->process_encounter($id_Appointment);

      $ApplicationSpecificText = "Insert Appointment";
      $this->mylib->action_audit_log($ApplicationSpecificText, "AP", "A", $id_Appointment, $this->input->post('Patient_ID')); 
      redirect('encounter/start/' . $id_Appointment . '/1');
    }
  }

  function update_local_time($id = 0) {
    $appt = $this->AppointmentModel->get_by_id($id)->row();
    $data_db = $this->load->database('data', TRUE)->database;
    $org_id = $this->current_user->Org_Id;
    $org_details = $this->OrgProfileModel->get_by_field('Org_ID', $org_id)->row();
    $get_time_zone = $this->TimeZoneModel->get_by_id($org_details->TimeZone_ID)->row();

    $sql = "Update $data_db.dbo.Appointments
    SET ApptStart_UTC=$data_db.dbo.LocaltoUTC('$appt->ApptStart', $get_time_zone->TzOffsetStandard_num, $org_details->DST),
      ApptStop_UTC= $data_db.dbo.LocaltoUTC('$appt->ApptStop', $get_time_zone->TzOffsetStandard_num, $org_details->DST)
    where Appointments_Id = $id";
    $this->ReportModel->data_db->trans_begin();
    $this->ReportModel->data_db->query($sql);
    $this->ReportModel->data_db->trans_commit();
  }

  function create_status($id_Appointment) {
    if ($this->input->post('status') != NULL) {
      $s = (int) $this->input->post('status');
      $checkIn_codes = $this->CheckInCodeModel->get_by_field('CodeOrder', $s, array('Org_Id' => $this->current_user->Org_Id))->row();
      if ($checkIn_codes) {
        $insert = array(
          'Appointments_Id' => $id_Appointment,
          'CodeOrder' => $this->input->post('status'),
          'CheckIn_DateTime' => date('Y-m-d H:i:s'),
          'Users_PK' => $this->current_user->ID,
          'Description' => $checkIn_codes->Description,
          'Color' => $checkIn_codes->Color
        );
        $this->CheckInLogModel->insert($insert);
      }
    }
  }

  private function convert($date, $time) {
    $d_p = explode('-', $date);
    $date = $d_p[2] . '-' . $d_p[0] . '-' . $d_p[1];
    $date = new DateTime("$time $date");
    return $date->format('Y-m-d H:i:s');
  }

  function appointment_edit($id) {
    $id = (int) $id;
    $con = '(Hidden = 0 OR Hidden IS NULL) and Org_Id = ' . $this->current_user->Org_Id;
    $data['dt'] = $this->AppointmentModel->get_by_field('Appointments_ID', $id, $con)->row();
    if ($data['dt']) {
      $data['patient'] = $this->PatientProfileModel->get_by_field('Patient_ID', $data['dt']->Patient_ID, array('Org_Id' => $this->current_user->Org_Id))->row();
      $data['partial'] = $this->self . "/appointment_edit";
      $this->load->view('layout', $data);
    } else {
      redirect($this->self . '/appointment_new');
    }
  }

  function appointment_update() {
    $id_Appointment = (int) $this->input->post('Appointments_ID');

    $this->form_validation->set_rules($this->AppointmentModel->validation_update());
    $this->form_validation->set_error_delimiters(ERRORS_STYLE_OPEN, ERRORS_STYLE_END);
    if ($this->form_validation->run() == FALSE) {
      $this->appointment_edit($id_Appointment);
    } else {
      $dt_update = $this->params_appt();
      $this->AppointmentModel->update($id_Appointment, $dt_update);

      $this->update_local_time($id_Appointment);
      $this->create_status($id_Appointment);
      $this->process_encounter($id_Appointment);

      $ApplicationSpecificText = "Update Appointment";
      $this->mylib->action_audit_log($ApplicationSpecificText, "AP", "U", $id_Appointment, $this->input->post('Patient_ID'));

      redirect('patient');
    }
  }

  function appointment_destroy($id) {
    $id = (int) $id;
    $con = '(Hidden = 0 OR Hidden IS NULL) and Org_Id = ' . $this->current_user->Org_Id;
    $dt = $this->AppointmentModel->get_by_field('Appointments_ID', $id, $con)->row();
    if ($dt) {
      $this->AppointmentModel->update($id, array('Hidden' => 1));

      $patient_id = (int) $dt->Patient_ID;
      $ApplicationSpecificText = "Deleted Appointment";
      $this->mylib->action_audit_log($ApplicationSpecificText, "AP", "D", $id, $patient_id);
    }
    redirect($this->self . '/appointment_new');
  }

  function print_cal() {
    $data_api = $this->AppointmentModel->get_appointments_by_org_id(array(
                'Org_ID' => $this->current_user->Org_Id,
                'ApptStart' => $this->input->post('start'),
                'ApptStop' => $this->input->post('end'),
                'Provider_ID' => $this->input->post('provider')
            ))->result();
    $data['listing'] = $data_api;
    $data['partial'] = $this->self . "/print_cal";
    $this->load->view('layout_print', $data);
  }

}
