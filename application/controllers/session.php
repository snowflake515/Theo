<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Session extends CI_Controller {

  function __construct() {
    parent::__construct();
  }

  function index() {
    $data['partial'] = "public/login";

    if ($this->session->userdata('wrong_login') >= 5) {
      $data['partial'] = "public/security";
    }

    $this->load->view('login_layout', $data);
  }

  function reset_security() {
    $this->session->set_userdata('wrong_login', "0");
    redirect('/');
  }

  function login() {
    $this->form_validation->set_rules($this->validate_login());
    $this->form_validation->set_error_delimiters(ERRORS_STYLE_OPEN, ERRORS_STYLE_END);
    if ($this->form_validation->run() == FALSE) {
      $this->index();
    } else {
      $this->session->set_userdata('wrong_login', "0");

      $user_id = $this->input->post('userid');
      $password = $this->input->post('password');
      $client_number = (int)$this->input->post('client_number');
      $get_current_login = $this->UserModel->check_login($user_id, $password, $client_number);

      $newdata = array('USER_ID' => $get_current_login->ID);
      $this->session->set_userdata($newdata);
      redirect('patient');
    }
  }

  function userid($str, $params) {
    $user_id = $this->input->post('userid');
    $password = $this->input->post('password');
    $client_number = (int)$this->input->post('client_number');
    $check_login = $this->UserModel->check_login($user_id, $password, $client_number);
    if ($check_login) {
      $get_current_login = $check_login;
      $exp = array('ORG1', 'AWACS1');
      $org_id = (int) $get_current_login->Org_Id;
      if (in_array($get_current_login->User_Id, $exp)) {
        return TRUE;
      } elseif (($org_id == $this->input->post('client_number'))) {
        $org = $this->OrgProfileModel->get_by_id($get_current_login->Org_Id)->row();
        $ip = $this->input->ip_address();
        if ($org &&  !empty($org->IP_Inclusion) && $this->OrgProfileModel->check_ip($ip, $get_current_login->Org_Id)->num_rows() == 0) {
          $this->wrong_login('userid', 'Sorry, your machine IP address is not in the allowed range of IP addresses permitted by
              your system administrator for proper authentication. If this error continues to appear,
              we recommend you contact your system administrator and tell them that your IP
              address is: ' . $ip);
          return FALSE;
        }
        return TRUE;
      } else {
        $this->wrong_login('Login is invalid.');
        return FALSE;
      }
    } else {
      $this->wrong_login('The username or password you entered is incorrect.');
      return FALSE;
    }
  }

  private function wrong_login($msg) {
    $wrong_login = (int) $this->session->userdata('wrong_login');
    $wrong_login = $wrong_login + 1;
    $this->session->set_userdata('wrong_login', "$wrong_login");
    $this->form_validation->set_message('userid', $msg);
  }

  function logout() {
    $c = $this->input->get('c');
    $c_access = !empty($c) ? "?c=$c" : NULL;
    $this->session->userdata = array();
    $this->session->sess_destroy();
    redirect('/'.$c_access);
  }

  function validate_login() {
    return $config = array(
        array('field' => 'userid', 'label' => 'userid', 'rules' => 'callback_userid[userid, password]')
    );
  }

}
