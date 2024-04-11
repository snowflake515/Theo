<?php

class FreeModel extends CI_Model
{

  //  var $table = "Appointments";
  //  var $key = "Appointments_ID";

  function __construct()
  {
    parent::__construct();
  }

  function db($group = 'data')
  {
    return  $this->load->database($group, TRUE);
  }

  function exec_sql($sql, $group = 'data')
  {
    $db  =  $this->load->database($group, TRUE);
    $db->trans_begin();
    $db->query($sql);
    $db->trans_commit();
  }

  function data_db()
  {
    return $this->data_db = $this->load->database('data', TRUE);
  }

  function template_db()
  {
    return $this->template_db = $this->load->database('template', TRUE);
  }
}
