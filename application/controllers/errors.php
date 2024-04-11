<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Errors extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->self = $this->router->fetch_class();
  }

  function not_found() {
    $data['partial'] = $this->self . "/404";
    $this->load->view('layout', $data);
  }

}
