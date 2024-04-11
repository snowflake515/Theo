<?php

class PatientUDFValuesModel extends CI_Model {

  var $table = "PatientUDFValues";
  var $key = "UDFValues_Id";

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

  function get_where($con) {
    $this->data_db->where($con);
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }

  function get_by_field($field, $val, $other_condition = null) {
    $this->data_db->where($field, $val);
    if ($other_condition != null) {
      $this->data_db->where($other_condition);
    }
    $this->data_db->from($this->table);
    $this->data_db->order_by($this->key,'DESC');
    return $this->data_db->get();
  }

  function validation_create() {
    $dt_post = $this->input->post();
    $numerik = array(
        'User_Real1_Value',
        'User_Real2_Value',
        'User_Real3_Value',
        'User_Real4_Value',
        'User_Real5_Value');
    $config = array();
    if ($dt_post) {
      foreach ($dt_post as $key => $value) {
        if (in_array($key, $numerik)) {
          $config[] = array('field' => $key, 'label' => 'this', 'rules' => 'numeric');
        }
      }
    }
    return $config;
  }

  function get_last_insert() {
    //return $this->data_db->insert_id();
    $res = $this->data_db->query("SELECT @@IDENTITY as insert_id")->row_array();
    return intval($res['insert_id']);
  }

  function validation_update() {
    return $this->validation_create();
  }

  function get_params($params = NULL) {
    if ($params == 'date') {
      return array('User_Date1_Value', 'User_Date2_Value', 'User_Date3_Value', 'User_Date4_Value', 'User_Date5_Value');
    } else {
      return array('User_Text1_Value', 'User_Text2_Value', 'User_Text3_Value', 'User_Text4_Value', 'User_Text5_Value', 'User_Text6_Value', 'User_Text7_Value', 'User_Text8_Value', 'User_Text9_Value', 'User_Text10_Value', 'User_Date1_Value', 'User_Date2_Value', 'User_Date3_Value', 'User_Date4_Value', 'User_Date5_Value', 'User_Real1_Value', 'User_Real2_Value', 'User_Real3_Value', 'User_Real4_Value', 'User_Real5_Value');
    }
  }

}
