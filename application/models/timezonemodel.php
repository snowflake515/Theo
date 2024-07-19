<?php

class TimeZoneModel extends CI_Model {

  var $table = "TimeZone";
  var $key = "TimeZone_ID";

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

  function get_last_insert() {
    //return $this->data_db->insert_id();
    $res = $this->data_db->query("SELECT @@IDENTITY as insert_id")->row_array();
    return intval($res['insert_id']);
  }

  function get_sort($arr) {
    $this->data_db->where_in($arr);
    $this->data_db->where('(Hidden = 0 OR Hidden IS NULL)');

    $this->data_db->from($this->table);
    $this->data_db->order_by('SortOrder', 'ASC');
    return $this->data_db->get();
  }

}
