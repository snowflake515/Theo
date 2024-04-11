<?php

class AppointmentModel extends CI_Model {

  var $table = "Appointments";
  var $key = "Appointments_ID";

  function __construct() {
    parent::__construct();
    $this->data_db = $this->load->database('data', TRUE);
  }

  function select_db() {
     return $this->data_db->from($this->table);
  }

  function insert($data) {
    $this->data_db->trans_begin();
    $this->data_db->insert($this->table, $data);
    $this->data_db->trans_commit();
  }

  function update($id, $data) {
    $this->data_db->trans_begin();
    $this->data_db->where($this->key, $id);
    $this->data_db->update($this->table, $data);
    $this->data_db->trans_commit();
  }

  function delete($id) {
    $this->data_db->trans_begin();
    $this->data_db->where($this->key, $id);
    $this->data_db->delete($this->table);
    $this->data_db->trans_commit();
  }

  function get_all() {
    return $this->data_db->get($this->table);
  }

  function get_by_id($id) {
    $this->data_db->where($this->key, $id);
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }

  function get_by_field($field, $val, $other_condition = null) {
    $this->data_db->where($field, $val);
    if ($other_condition != null) {
      $this->data_db->where($other_condition);
    }
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }

  function get_appointments_by_org_id($params) {
    foreach ($params as $key => $value) {
      if ($value != NULL || $value != "") {
        $operator = ($key == "ApptStop") ? "<=" : (($key == "ApptStart") ? ">=" : "");
        $this->data_db->where($this->table . '.' . $key . ' ' . $operator, $value);
      }
    }
    $this->data_db->where('(Appointments.Hidden = 0 OR Appointments.Hidden IS NULL)');
    $this->data_db->select('PatientProfile.AccountNumber,PatientProfile.MedicalRecordNumber, PatientProfile.PhoneHome, PatientProfile.PhoneWork,PatientProfile.DOB,PatientProfile.Patient_ID, PatientProfile.LastName, PatientProfile.FirstName, Appointments.Provider_ID, Appointments.Appointments_ID, Appointments.ApptStop, Appointments.ApptStart');
    $this->data_db->join('PatientProfile', "PatientProfile.Patient_ID = $this->table.Patient_ID");
    $this->data_db->from($this->table);
    $this->data_db->order_by('Appointments.ApptStart', 'ASC');
    return $this->data_db->get();
  }

  function get_appt_month($params) {
    foreach ($params as $key => $value) {
      if ($value != NULL || $value != "") {
        if ($key == 'date') {
          $this->data_db->where("CONVERT(VARCHAR(25), $this->table.ApptStart, 126) LIKE '$value%' ");
        } else {
          $this->data_db->where($this->table . '.' . $key, $value);
        }
      }
    }
    $this->data_db->where('(Appointments.Hidden = 0 OR Appointments.Hidden IS NULL)');
    $this->data_db->select("*, $this->table.Notes as appt_notes, $this->table.Provider_ID as provider_id_appt");
    $this->data_db->join('PatientProfile', "PatientProfile.Patient_ID = $this->table.Patient_ID");
    $this->data_db->from($this->table);
    $this->data_db->order_by('Appointments.ApptStart', 'ASC');
    return $this->data_db->get();
  }

  function validation_create() {
    $config = array(
        array('field' => 'Patient_ID', 'label' => 'Patient', 'rules' => 'required'),
        array('field' => 'Provider_ID', 'label' => 'Provider', 'rules' => 'required'),
        array('field' => 'status', 'label' => 'Status', 'rules' => 'required'),
        array('field' => 'ApptStart', 'label' => 'Appointment Date', 'rules' => 'required'),
        array('field' => 'ApptStartTime', 'label' => 'Start Time', 'rules' => 'required'),
        array('field' => 'ApptStopTime', 'label' => 'Stop Time', 'rules' => 'required'),
        array('field' => 'Facility_ID', 'label' => 'Facility_ID', 'rules' => 'required'),
        array('field' => 'EncounterDescription_ID', 'label' => 'Encounter Type', 'rules' => 'required'),
        array('field' => 'Notes', 'label' => 'Notes', 'rules' => 'max_length[50]'),

    );
    return $config;
  }

  function validation_update() {
    return $this->validation_create();
  }

  function get_last_insert() {
    //return $this->data_db->insert_id();
    $res = $this->data_db->query("SELECT @@IDENTITY as insert_id")->row_array();
    return intval($res['insert_id']);
  }

  function get_appointments_by_patient($patient_id) {
    $this->data_db->where($this->table . '.Patient_ID', $patient_id);
    $this->data_db->where('(Appointments.Hidden = 0 OR Appointments.Hidden IS NULL)');
    $this->data_db->join('ProviderProfile', "ProviderProfile.Provider_ID = $this->table.Provider_ID");
    $this->data_db->order_by('Appointments.Appointments_ID', 'DESC');
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }

}
