<?php

class PatientProfileUDFModel extends CI_Model {

  var $table = "PatientProfileUDF";
  var $key = "PPUDF_Id";

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
    if ($other_condition != null) {
      $this->data_db->where($other_condition);
    }
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }

  function validation_create() {
    $dt_post = $this->input->post();
    $numerik = array(
        'User_Real1_Desc',
        'User_Real2_Desc',
        'User_Real3_Desc',
        'User_Real4_Desc',
        'User_Real5_Desc');
    $config = array();
    if ($dt_post) {
      foreach ($dt_post as $key => $value) {
        if (in_array($key, $numerik)) {
          $config[] = array('field' => $key, 'label' => 'This', 'rules' => 'numeric');
        }
      }
    }
    return $config;
  }

  function validation_update() {
    return $this->validation_create();
  }

}
