<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Test extends CI_Controller {

  function __construct() {
    parent::__construct();
    //  $this->current_user = $this->sessionlib->current_user();
    //$this->self = $this->router->fetch_class();
  }

  function get_app() {
    
  }

  function dec($str = NULL) {
    $str = '184|198|180|182|183|200|185|174|';
    $key = str_split("QWERTYUIOPASDFGHJKL;");
    $kpost = 0;
    $result = NULL;
    if ($str != NULL) {
      foreach (explode('|', $str) as $v) {
        if ($v) {
          $k = $v - ord($key[$kpost]);
          $result.=chr($k);
          if ($kpost >= 19) {
            $kpost = 1;
          } else {
            $kpost++;
          }
        }
      }
    }
    echo $result;
  }

  function delete_AWACSInput() {
    
//    $sql = "Update [Wellness_eCastEMR_Data].[dbo].[AWACSInput] "
//            . "set "
//            . "Hidden = 1, "
//            . "DataValue = -1 "
//            . "where "
//            . "IsNumeric(DataValue) = 0";
//    
//    $this->ReportModel->data_db->trans_begin();
//    $DeleteAWACSInput = $this->ReportModel->data_db->query($sql);
//    $this->ReportModel->data_db->trans_commit();
    
    
      $sql = "SELECT DataValue 
  FROM [Wellness_eCastEMR_Data].[dbo].[AWACSInput]
  where  IsNumeric(DataValue) = 0";


    $AWACSInput = $this->ReportModel->data_db->query($sql);
    $AWACSInput_num = $AWACSInput->num_rows();
    $AWACSInput_result = $AWACSInput->result();
    
    var_dump($AWACSInput_result);
  }

}
