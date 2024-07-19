<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Dashboard extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->current_user = $this->sessionlib->current_user();
    $this->self = $this->router->fetch_class();
  }

  function index() {
    $data['partial'] = $this->self."/list";
    $this->load->view('layout', $data);
  }
  
  


}
