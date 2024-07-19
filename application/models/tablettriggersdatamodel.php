<?php

class TabletTriggersDataModel extends CI_Model {

  var $table = "TabletTriggersData";
  var $key = "TabletTriggersData_ID";

  function __construct() {
    parent::__construct();
    $this->data_db = $this->load->database('data', TRUE);
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
    if($other_condition != null){
      $this->data_db->where($other_condition);
    }
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }
  
  function show_trigger_data($patient, $ecounter){
    $this->data_db->select('t1.*, t2.*');
    $this->data_db->from($this->table.' as t1');
    $this->data_db->join('TabletTriggers as t2', 't1.TabletTriggers_ID = t2.TabletTriggers_ID');
   
    $this->data_db->where('t1.Patient_ID', (int)$patient->Patient_ID);
    $this->data_db->where('t1.Encounter_ID', (int)$ecounter->Encounter_ID);
    $this->data_db->where('(t1.Hidden = 0 OR t1.Hidden IS NULL)');
    $this->data_db->order_by('t2.TTDescription', 'ASC');
    return $this->data_db->get();
  }
  
  function delete_trigger_data($arr = array(), $id_encounter = 0, $id_patient = 0){
    $this->data_db->trans_begin();
    $this->data_db->where('Encounter_ID', $id_encounter);
    $this->data_db->where('Patient_ID', $id_patient);
    //$this->data_db->where_not_in('TabletTriggers_ID', $arr);
    $this->data_db->delete($this->table);
    $this->data_db->trans_commit();
  }
  
  function get_last_insert() {
    //return $this->data_db->insert_id();
    $res = $this->data_db->query("SELECT @@IDENTITY as insert_id")->row_array();
    return intval($res['insert_id']);
  }


}
