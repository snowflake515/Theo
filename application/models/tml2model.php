<?php

class Tml2Model extends CI_Model {

  var $table = "TML2";
  var $key = "TML2_ID";

  function __construct() {
    parent::__construct();
    $this->template_db = $this->load->database('template', TRUE);
  }

  function insert($data) {
    $this->template_db->trans_begin();
    $this->template_db->insert($this->table, $data);
    $this->template_db->trans_commit();
  }

  function update($id, $data) {
    $this->template_db->trans_begin();
    $this->template_db->where($this->key, $id);
    $this->template_db->update($this->table, $data);
    $this->template_db->trans_commit();
  }

  function delete($id) {
    $this->template_db->trans_begin();
    $this->template_db->where($this->key, $id);
    $this->template_db->delete($this->table);
    $this->template_db->trans_commit();
  }

  function get_all() {
    return $this->template_db->get($this->table);
  }

  function get_by_id($id) {
    $this->template_db->where($this->key, $id);
    $this->template_db->from($this->table);
    return $this->template_db->get();
  }

  function get_by_field($field, $val, $other_condition = null) {
    $this->template_db->where($field, $val);
    $this->template_db->order_by('Sequence', 'ASC');
    if($other_condition != null){
      $this->template_db->where($other_condition);
    }
    $this->template_db->from($this->table);
    return $this->template_db->get();
  }
  
  function get_last_insert() {
    //return $this->data_db->insert_id();
    $res = $this->template_db->query("SELECT @@IDENTITY as insert_id")->row_array();
    return intval($res['insert_id']);  
  }

}
