<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Encounter extends CI_Controller
{

  function __construct()
  {
    parent::__construct();

    header('Content-type: application/json');

    $not_required_login = array();
    $this->current_user = $this->sessionlib->current_user('json');
    $this->self = $this->router->fetch_class();
    $this->load->model('FreeModel');
  }

  private function convertYMD($str = '')
  {
    //ex 02-29-2020
    $result = NULL;
    $s = explode('-', $str);
    if ($str && $s && count($s) == 3) {
      $result = $s[2] . '-' . $s[0] . '-' . $s[1];
    }
    return $result;
  }

  private function getTimeOnly($str = '')
  {
    $result = NULL;
    $s = explode(' ', $str);
    if ($str && $s && count($s) > 0) {
      $result = $s[1];
    }
    return $result;
  }

  private function joinSelectedDate($selected_date, $fixed_time)
  {
    return $selected_date . ' ' . $this->getTimeOnly($fixed_time);
  }


  private function save_params($Encounter_ID, $Appointments_ID)
  {

    $dt_post = array(
      'ChiefComplaint' => $this->input->post('ChiefComplaint'),
      'Dept_ID' => $this->input->post('Dept_ID'),
      // 'EncounterDate' => $this->input->post('EncounterDate'),
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

    if ($Encounter_ID) {
      $this->EncounterHistoryModel->update($Encounter_ID, $dt_post);
    }

    if ($Appointments_ID) {
      $this->AppointmentModel->update($Appointments_ID, $dt_update);
    }
  }

  function save_encounter()
  {
    $Encounter_ID = (int) $this->input->post('encounter_id');
    $Appointments_ID = (int) $this->input->post('Appointments_ID');
    $this->save_params($Encounter_ID, $Appointments_ID);
  }

  function changedate()
  {
    $enct_new_id = 0;
    $appt_new_id = 0;

    $encounter_id = (int) $this->input->post('encounter_id');
    $encounter_date = $this->input->post('encounter_date');
    $selected_date = $this->convertYMD($encounter_date);

    $encounter = $this->EncounterHistoryModel->get_by_id($encounter_id)->row_array();
    $temp_appt_id = !empty($encounter['Appointments_ID']) ? $encounter['Appointments_ID'] : 0;
    $appt = $this->AppointmentModel->get_by_id($temp_appt_id)->row_array();
    $etl  = $this->ETLModel->get_by_field('Encounter_ID', $encounter_id)->row_array();

    if (!empty($encounter['Encounter_ID']) && !empty($appt['Appointments_ID']) && !empty($selected_date)) {

      unset($appt['Appointments_ID']);
      $appt['ApptStart'] = $this->joinSelectedDate($selected_date,  $appt['ApptStart']);
      $appt['ApptStop'] =  $this->joinSelectedDate($selected_date,  $appt['ApptStop']);
      $appt['DateEntered'] = date('Y-m-d H:i:s');
      $appt['ApptStart_UTC'] =  $this->joinSelectedDate($selected_date,  $appt['ApptStart_UTC']);
      $appt['ApptStop_UTC'] =  $this->joinSelectedDate($selected_date,  $appt['ApptStop_UTC']);

      $this->AppointmentModel->insert($appt);
      $appt_new_id = $this->AppointmentModel->get_last_insert();

      unset($encounter['Encounter_ID']);
      $encounter['Appointments_ID'] = $appt_new_id;
      $encounter['EncounterDate'] =  $this->joinSelectedDate($selected_date,  $encounter['EncounterDate']);
      $this->EncounterHistoryModel->insert($encounter);
      $enct_new_id = $this->EncounterHistoryModel->get_last_insert();

      if ($enct_new_id && $appt_new_id) {
        $this->save_params($enct_new_id, $appt_new_id);
      }


      $cdate = date('Y-m-d H:i:s');


      if (!empty($etl['ETL_Id'])) {
        unset($etl['ETL_Id']);
        $etl['Encounter_Id'] = $enct_new_id;
        $this->ETLModel->insert($etl);

        $sql = "INSERT INTO ETL1 (Encounter_Id, TML1_Id) 
        SELECT $enct_new_id, TML1_Id FROM ETL1 WHERE Encounter_Id = $encounter_id";
        $this->FreeModel->exec_sql($sql);

        $sql = "INSERT INTO ETL2 (Encounter_Id, TML2_Id) 
        SELECT $enct_new_id, TML2_Id FROM ETL2 WHERE Encounter_Id = $encounter_id";
        $this->FreeModel->exec_sql($sql);


        $sql = "INSERT INTO ETL3 (Encounter_Id, TML3_Id, DateCreated) 
        SELECT $enct_new_id, TML3_Id, '$cdate' FROM ETL3 
        WHERE Encounter_Id = $encounter_id";
        $this->FreeModel->exec_sql($sql);

        $sql = "INSERT INTO ETL3Input
        (Encounter_Id, TML3_Id, ETL3Input, Redacted, DateRedacted)
        SELECT $enct_new_id, TML3_Id, ETL3Input, Redacted, DateRedacted FROM ETL3Input WHERE
        Encounter_Id = $encounter_id";
        $this->FreeModel->exec_sql($sql);

        $sql = "INSERT INTO TabletInput
        (Encounter_ID, TML1_ID, TML2_ID, TML3_ID, TML3_Value, Hidden, Status)
        SELECT $enct_new_id, TML1_ID, TML2_ID, TML3_ID, TML3_Value, Hidden, Status FROM
        TabletInput WHERE Encounter_ID = $encounter_id";
        $this->FreeModel->exec_sql($sql);
      }
      echo json_encode(['url' => site_url('encounter/start/' . $appt_new_id)]);
    } else {
      echo json_encode(['error' => 'Ups something wrong, Please try agaian!']);
    }
  }
}
