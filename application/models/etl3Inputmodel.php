<?php

class ETL3InputModel extends CI_Model {

  var $table = "ETL3Input";
  var $key = "ETL3Input_Id";

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
  
  function update_where($con, $data) {
    $this->data_db->trans_begin();
    $this->data_db->where($con);
    $this->data_db->update($this->table, $data);
    $this->data_db->trans_commit();
  }

  function delete($id) {
    $this->data_db->trans_begin();
    $this->data_db->where($this->key, $id);
    $this->data_db->delete($this->table);
    $this->data_db->trans_commit();
  }
  
  function delete_where($con) {
    $this->data_db->trans_begin();
    $this->data_db->where($con);
    $this->data_db->delete($this->table);
    $this->data_db->trans_commit();
  }

  function delete_by_field($Encounter_ID, $TML3_ID) {
    $this->data_db->trans_begin();
    $this->data_db->where('Encounter_ID', $Encounter_ID);
    $this->data_db->where('TML3_ID', $TML3_ID);
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
    $this->data_db->order_by($this->key, 'desc');
    return $this->data_db->get();
  }
  
  function get_data($id, $id_encpunter){
    $this->data_db->where('Encounter_ID', $id_encpunter);
    $this->data_db->where('TML3_ID', $id);
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }
  
  function get_last_insert() {
    //return $this->data_db->insert_id();
    $res = $this->data_db->query("SELECT @@IDENTITY as insert_id")->row_array();
    return intval($res['insert_id']);
  }

}
