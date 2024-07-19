<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class MyLib {

  function __construct() {
    $this->ci_load = & get_instance();
  }

  function dt_current_user() {
    $id = $this->ci_load->session->userdata('USER_ID');
    return $this->ci_load->UserModel->get_by_id($id)->row();
  }

  function action_audit_log($ApplicationSpecificText = NULL, $AuditTrail = "SEC", $AuditMode = "A", $AuditRecord = 0,  $patient_id = 0) {
    $this->current_user = $this->ci_load->sessionlib->current_user();
    $OriginalMode = $AuditMode;

    $arr_MRAuditLog = array(
        "Org_Id" => (int) $this->current_user->Org_Id,
        "User_Id" => (int) $this->current_user->ID,
        "Patient_Id" => $patient_id,
        "Application" => $AuditTrail,
        "Mode" => $AuditMode,
        "DateAccessed" => date("Y-m-d H:i:s"),
        "TimeAccessed" => date("H:i:s"),
        "RecordNumber" => $AuditRecord,
        "Success" => 1,
        "ApplicationSpecificText" => $ApplicationSpecificText,
    );
    $this->ci_load->EMRAuditLogModel->insert($arr_MRAuditLog);

    $AuditLog_Id = $this->ci_load->EMRAuditLogModel->get_last_insert();

    /*
    if ($AuditMode != $OriginalMode) {
      $arr_EMRAuditaAnomaly = array(
          "AuditLog_Id" => $AuditLog_Id,
          "OriginalMode" => $OriginalMode,
      );
      $this->ci_load->EMRAuditaAnomalyModel->insert($arr_EMRAuditaAnomaly);
    }
    */

  }

  function only_supper_admin() {
    $exp = array('ORG1', 'AWACS1');
    $curren_user = $this->dt_current_user();
    if ($curren_user && in_array(strtoupper($curren_user->User_Id), $exp)) {
      return TRUE;
    }
    return FALSE;
  }


  function get_client(){
    $user = $this->dt_current_user();
    $title_client = 'WellTrackONE';
    if($user){
      $db = $this->ci_load->data_db = $this->ci_load->load->database('image', TRUE);
      $client = $db->from($this->ci_load->ClientImagesModel->table)->select('ClientName, ClientAccess')
      ->where(array( 'Hidden' => 0, 'Org_ID' => $user->Org_Id )) ->get()->row();
      if($client){
        $title_client = $client->ClientName;
      }
    }
    return $title_client;
  }

}
