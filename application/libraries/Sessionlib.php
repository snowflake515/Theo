<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class SessionLib {

  function __construct() {
    $this->ci_load = & get_instance();
  }

  function check_login() {
    return ($this->ci_load->session->userdata('USER_ID') == NULL) ? FALSE : TRUE;
  }

  function current_user($ress = NULL) {
    $id = $this->ci_load->session->userdata('USER_ID');
    if ($id == NULL || $this->ci_load->UserModel->get_by_id($id)->num_rows() == 0) {
      if ($ress == 'json') {
        $json = array('error' => TRUE, 'msg' => 'Your Session has time out, Please Login Again');
        header('Content-type: application/json');
        echo json_encode($json);
        exit();
      } else {
        redirect('/');
      }
    } else {
      $org_select = $this->ci_load->session->userdata('CURRENT_ORG');
      $check_select = $this->ci_load->session->userdata('CHECK_ORG');
      $user = $this->ci_load->UserModel->get_by_id($id)->row();
      if ($check_select == TRUE) {
        $user->Org_Id = $org_select;
      }
      return $user;
    }
  }

  function current_user_ajax() {
    $id = $this->ci_load->session->userdata('USER_ID');
    if ($id == NULL || $this->ci_load->UserModel->get_by_id($id)->num_rows() == 0) {
      return FALSE;
    } else {
      $org_select = $this->ci_load->session->userdata('CURRENT_ORG');
      $check_select = $this->ci_load->session->userdata('CHECK_ORG');
      $user = $this->ci_load->UserModel->get_by_id($id)->row();
      if ($check_select == TRUE) {
        $user->Org_Id = $org_select;
      }
      return $user;
    }
  }

}
