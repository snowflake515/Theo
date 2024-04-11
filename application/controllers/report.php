<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Report extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->self = "report_cfm";
  }

  function test() {
    $this->load->view($this->self . '/comp_screeningschedule', NULL);
  }

}
