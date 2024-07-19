<?php

class EMRAuditLogModel extends CI_Model {

  var $table = "EMRAuditLog";
  var $key = "AuditLog_Id";

  function __construct() {
    parent::__construct();
    $this->audit_db = $this->load->database('audit', TRUE);
  }

  function insert($data) {
    $this->audit_db->trans_begin();
    $this->audit_db->insert($this->table, $data);
    $this->audit_db->trans_commit();
  }

  function update($id, $data) {
    $this->audit_db->trans_begin();
    $this->audit_db->where($this->key, $id);
    $this->audit_db->update($this->table, $data);
    $this->audit_db->trans_commit();
  }

  function delete($id) {
    $this->audit_db->trans_begin();
    $this->audit_db->where($this->key, $id);
    $this->audit_db->delete($this->table);
    $this->audit_db->trans_commit();
  }

  function get_all() {
    return $this->audit_db->get($this->table);
  }

  function get_by_id($id) {
    $this->audit_db->where($this->key, $id);
    $this->audit_db->from($this->table);
    return $this->template_db->get();
  }

  function get_by_field($field, $val, $other_condition = null) {
    $this->audit_db->where($field, $val);
    if($other_condition != null){
      $this->audit_db->where($other_condition);
    }
    $this->audit_db->from($this->table);
    return $this->data_db->get();
  }
  
  function get_last_insert() {
    //return $this->data_db->insert_id();
    $res = $this->audit_db->query("SELECT @@IDENTITY as insert_id")->row_array();
    return intval($res['insert_id']);
  }

}
