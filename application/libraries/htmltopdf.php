<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

require_once APPPATH . "/third_party/html2pdf/html2pdf.class.php";

class Htmltopdf extends HTML2PDF {

  public function __construct() {
    parent::__construct();
  }

}
