<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class User extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->current_user = $this->sessionlib->current_user();
    $this->self = $this->router->fetch_class();
  }

  function change_org() {
    $exp = array('ORG1', 'AWACS1');
    if (!$this->session->userdata('CHECK_ORG') && in_array($this->current_user->User_Id, $exp)) {
      $this->form_validation->set_rules($this->validate_change_org());
      $this->form_validation->set_error_delimiters(ERRORS_STYLE_OPEN, ERRORS_STYLE_END);
      if ($this->form_validation->run() == FALSE) {
        $data['partial'] = "modal/blank";
        $this->load->view('layout', $data);
      } else {
        $dt_update = array('Org_ID' => $this->input->post('Org_ID'));
        $this->UserModel->update($this->current_user->ID, $dt_update);
        $newdata = array('CHECK_ORG' => TRUE, 'CURRENT_ORG' => $this->input->post('Org_ID'));
        $this->session->set_userdata($newdata);
        redirect('schedule');
      }
    } else {
      redirect('schedule');
    }
  }

  function rm_org() {
    $exp = array('ORG1', 'AWACS1');
    if (in_array($this->current_user->User_Id, $exp)) {
      $newdata = array('CHECK_ORG' => FALSE);
      $this->session->set_userdata($newdata);
      redirect('schedule');
    } else {
      redirect('schedule');
    }
  }

  private function validate_change_org() {
    return $config = array(
        array('field' => 'Org_ID', 'label' => 'org', 'rules' => 'required')
    );
  }

  function change_pass() {
    if ($this->current_user->ResetPassword != 0) {
      $this->form_validation->set_rules($this->validate_change_pass());
      $this->form_validation->set_error_delimiters(ERRORS_STYLE_OPEN, ERRORS_STYLE_END);
      if ($this->form_validation->run() == FALSE) {
        $data['partial'] = "modal/blank";
        $this->load->view('layout', $data);
      } else {
        $this->UserModel->update($this->current_user->ID, array(
            'Password' => $this->UserModel->encript($this->input->post('new-password')),
            'ResetPassword' => 0));

        $msg = 'Password successfully updated.';
        $this->session->set_flashdata('pass_success_msg', $msg);
        //log
        $ApplicationSpecificText = "Change Password";
        $this->mylib->action_audit_log($ApplicationSpecificText, "SEC", "U", $this->current_user->ID, 0);
        redirect('schedule');
      }
    }else{
      redirect('schedule');
    }
  }

  function pass() {
    if ($this->UserModel->encript($this->input->post('new-password')) == $this->current_user->Password) {
      $this->form_validation->set_message('pass', 'New password must not be the same as the current password.');
      return FALSE;
    }
  }

  private function validate_change_pass() {
    return $config = array(
        array('field' => 'new-password', 'label' => 'new password', 'rules' => 'required|min_length[6]|callback_pass'),
        array('field' => 'confirm-password', 'label' => 'confirm password', 'rules' => 'required|matches[new-password]')
    );
  }

}
