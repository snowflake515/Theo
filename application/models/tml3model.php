<?php

class Tml3Model extends CI_Model {

  var $table = "TML3";
  var $key = "TML3_ID";

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

  function get_by_id($id) {
    $this->template_db->where($this->key, $id);
    $this->template_db->from($this->table);
    return $this->template_db->get();
  }

  function get_by_field($field, $val, $other_condition = null) {
    $this->template_db->where($field, $val);
    if($other_condition != null){
      $this->template_db->where($other_condition);
    }
    $this->template_db->order_by('Sequence', 'ASC');
    $this->template_db->from($this->table);
    return $this->template_db->get();
  }
  
  function get_data_save($con) {
    $this->template_db->select('TML3.*, TML1.TML1_ID');
    $this->template_db->where($con);
    $this->template_db->where('(TML3.Hidden = 0 OR TML3.Hidden IS NULL)');
    $this->template_db->from($this->table);
    $this->template_db->join('TML2', 'TML2.TML2_ID = TML3.TML2_ID'); 
    $this->template_db->join('TML1', 'TML1.TML1_ID = TML2.TML1_ID'); 
    return $this->template_db->get();
  }
  
  
  function get_data_join($con) {
    $this->template_db->select('TML3.*, TML1.TML1_ID');
    $this->template_db->where($con); 
    $this->template_db->from($this->table);
    $this->template_db->join('TML2', 'TML2.TML2_ID = TML3.TML2_ID'); 
    $this->template_db->join('TML1', 'TML1.TML1_ID = TML2.TML1_ID'); 
    return $this->template_db->get();
  }
  
  

}
