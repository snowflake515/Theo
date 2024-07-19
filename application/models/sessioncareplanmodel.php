<?php

class SessionCarePlanModel extends CI_Model {

  var $table = "noblemd20.session_careplan";
  var $key = "session_id";

  function __construct() {
    parent::__construct();
    $this->db = $this->load->database('theo', TRUE);
  }

  function select_db() {
     return $this->db->from($this->table);
  }

  function insert($data) {
    $this->db->insert($this->table, $data);
  }

  function update($id, $data) {
    $this->db->where($this->key, $id);
    $this->db->update($this->table, $data);
  }

  function delete($id) {
    $this->db->where($this->key, $id);
    $this->db->delete($this->table);
  }

  function get_last_insert() {
    return $this->db->insert_id();
  }

  function free_sql(){
    return $this->db;
  }


}
