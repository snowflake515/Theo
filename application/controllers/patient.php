<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Patient extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->current_user = $this->sessionlib->current_user();
    $this->self = $this->router->fetch_class();
  }

  function index() {
    $this->page();
  }

  function search() {
    $current_time = $this->input->post('current_time');
    $current_select = $this->input->post('current_select');
    
    $data_search = array(
        'text_field' => $this->input->post('text_field'),
        'by_field' => $this->input->post('by_field'),
        'dob' => $this->input->post('dob'),
        'Provider_ID' => $this->input->post('Provider_ID'),
        'Hidden' => $this->input->post('Hidden'),
        'SSN' => $this->input->post('SSN'),
        'MedicalRecordNumber' => $this->input->post('MedicalRecordNumber'),
        'AccountNumber' => $this->input->post('AccountNumber'),
        'phoneNumber' => $this->input->post('phoneNumber'),
        'lastName' => $this->input->post('lastName'),
        'firstName' => $this->input->post('firstName'),
        'PhoneHome' => $this->input->post('PhoneHome')
    );

    $data = array('patient_search' => $data_search);
    $this->session->set_userdata($data);
    $params = "";
    if ($current_time != "") {
      $params = 'current_select=' . $current_select . '&current_time=' . $current_time;
    }
    redirect("patient/page?" . $params);
  }

  function reset_search() {
    $data = array(
        'patient_search' => NULL
    );
    $this->session->set_userdata($data);
    redirect("patient/page");
  }


  function page() {
    $data_serach = $this->session->userdata('patient_search');
    
    $this->load->library('pagination');
    $config = default_config_pagging();
    $config['base_url'] = base_url() . 'index.php/patient/page/';
    $count_patients = $this->PatientProfileModel->get_count($data_serach, $this->current_user->Org_Id);
    $config['total_rows'] = ($count_patients) ? intval($count_patients->count_data) : 0;
    $config['per_page'] = 10;
    $config['uri_segment'] = 3;
    $config['num_links'] = 4;
    $this->pagination->initialize($config);
    $data['link_pagging'] = $this->pagination->create_links();
    
    $pages = (int) $this->uri->segment(3, 0);
    $data['pages'] = $pages;
    $data['list'] = $this->PatientProfileModel->get_patients($pages, $data_serach, $this->current_user->Org_Id);
    $data['form_search'] = $data_serach;
    $data['partial'] = $this->self . "/list";
    $this->load->view('layout', $data);
  }

  private function get_dt_pateint($patient_id = 0) {
    $patient_id = (int) $patient_id;
    $con = 'Org_ID = ' . $this->current_user->Org_Id;
    $dt = $this->PatientProfileModel->get_by_field('Patient_ID', $patient_id, $con)->row();
    return $dt;
  }

  function demographics($patient_id = NULL) {
    $patient_id = (int) $patient_id;
    $data['dt'] = $this->get_dt_pateint($patient_id);
    if ($data['dt'] || $patient_id == 0) {
      $data['partial'] = $this->self . "/demographics_form";
    } else {
      $data['partial'] = $this->self . "/patient_not_found";
    }
    $this->load->view('layout', $data);
  }

  function save_demographics() {
    $next_form = $this->input->post('next_form');
    $dt_post = $this->PatientProfileModel->get_params_demographics();
    $patient_id = (int) $this->input->post('Patient_ID');
    $btn_back = $this->input->post('back');
    $dt_patient = $this->get_dt_pateint($patient_id);
    $this->form_validation->set_rules($this->PatientProfileModel->rules_valid_demographics());
    $this->form_validation->set_error_delimiters(ERRORS_STYLE_OPEN, ERRORS_STYLE_END);
    if ($this->form_validation->run() == FALSE) {
      $this->demographics($patient_id);
      if ($btn_back) {
        redirect('patient/');
      }
    } else {
      if (!empty($dt_patient->Patient_ID)) {
        $id = $dt_patient->Patient_ID;
        $this->PatientProfileModel->update($dt_patient->Patient_ID, $dt_post);
        //log
        $ApplicationSpecificText = "Updated Patient Record ";
        $this->mylib->action_audit_log($ApplicationSpecificText, "PT", "U", $id, $id);
      } else {
        $dt_post['DateEntered'] = date('Y-m-d H:i:s');
        $dt_post['User_ID'] = $this->current_user->User_Id;
        $dt_post['Hidden'] = 0;
        $dt_post['Org_ID'] = $this->current_user->Org_Id;
        $this->PatientProfileModel->insert($dt_post);
        $id = $this->PatientProfileModel->get_last_insert();
        $this->insert_udf($id);
        //log
        $ApplicationSpecificText = "Created Patient Record ";
        $this->mylib->action_audit_log($ApplicationSpecificText, "PT", "A", $id, $id);
      }
      if ($btn_back) {
        redirect('patient/');
      } elseif (!empty($next_form)) {
        redirect('patient/' . $next_form . '/' . $id);
      } else {
        redirect('patient/demographics/' . $id);
      }
    }
  }

  function office_information($patient_id = 0) {
    $data['dt'] = $this->get_dt_pateint($patient_id);
    if ($data['dt']) {
      $data['partial'] = $this->self . "/office_information_form";
    } else {
      $data['partial'] = $this->self . "/patient_not_found";
    }
    $this->load->view('layout', $data);
  }

  function save_office_information() {
    $next_form = $this->input->post('next_form');
    $dt_post = $this->PatientProfileModel->get_params_office();
    $patient_id = (int) $this->input->post('Patient_ID');
    $dt_patient = $this->get_dt_pateint($patient_id);
    if (!empty($dt_patient->Patient_ID)) {
      $this->form_validation->set_rules($this->PatientProfileModel->rules_valid_office());
      $this->form_validation->set_error_delimiters(ERRORS_STYLE_OPEN, ERRORS_STYLE_END);
      if ($this->form_validation->run() == FALSE) {
        $this->office_information($patient_id);
      } else {
        $id = $dt_patient->Patient_ID;
        $this->PatientProfileModel->update($dt_patient->Patient_ID, $dt_post);
        if (!empty($next_form)) {
          redirect('patient/' . $next_form . '/' . $id);
        } else {
          redirect('patient/office_information/' . $id);
        }
      }
    } else {
      $data['partial'] = $this->self . "/patient_not_found";
      $this->load->view('layout', $data);
    }
  }

  function responsible_party($patient_id = 0) {
    $data['dt'] = $this->get_dt_pateint($patient_id);
    if ($data['dt']) {
      $data['partial'] = $this->self . "/responsible_party_form";
    } else {
      $data['partial'] = $this->self . "/patient_not_found";
    }
    $this->load->view('layout', $data);
  }

  function save_responsible_party() {
    $next_form = $this->input->post('next_form');
    $dt_post = $this->PatientProfileModel->get_params_responsible_party();
    $patient_id = (int) $this->input->post('Patient_ID');
    $dt_patient = $this->get_dt_pateint($patient_id);
    if (!empty($dt_patient->Patient_ID)) {
      $this->form_validation->set_rules($this->PatientProfileModel->rules_valid_responsible_party());
      $this->form_validation->set_error_delimiters(ERRORS_STYLE_OPEN, ERRORS_STYLE_END);
      if ($this->form_validation->run() == FALSE) {
        $this->responsible_party($patient_id);
      } else {
        $id = $dt_patient->Patient_ID;
        $this->PatientProfileModel->update($dt_patient->Patient_ID, $dt_post);
        if (!empty($next_form)) {
          redirect('patient/' . $next_form . '/' . $id);
        } else {
          redirect('patient/responsible_party/' . $id);
        }
      }
    } else {
      $data['partial'] = $this->self . "/patient_not_found";
      $this->load->view('layout', $data);
    }
  }

  function user_defined_fields($patient_id = NULL) {
    $dt_patient = $this->get_dt_pateint($patient_id);
    if ($dt_patient) {
      $data['patient_id'] = $dt_patient->Patient_ID;
      $data['dt'] = $this->PatientProfileUDFModel->get_by_field("Org_ID", $this->current_user->Org_Id)->row();
      //var_dump($data['dt']);
      $con = "Org_Id = " . $this->current_user->Org_Id;
      $dt_udf_v = $this->PatientUDFValuesModel->get_by_field("Patient_Id", $dt_patient->Patient_ID, $con);
      if ($dt_udf_v->num_rows() == 0) {
        $this->insert_udf($dt_patient->Patient_ID);
        $id_udf = $this->PatientUDFValuesModel->get_last_insert();
        $data['dt_udf_v'] = $this->PatientUDFValuesModel->get_by_field("Patient_Id", $dt_patient->Patient_ID, $con)->row();
      } else {
        $data['dt_udf_v'] = $dt_udf_v->row();
      }
      $data['partial'] = $this->self . "/user_defined_fields_form";
    } else {
      $data['partial'] = $this->self . "/patient_not_found";
    }
    $this->load->view('layout', $data);
  }

  function save_user_defined_field() {

    $UDFValues_Id = (int) $this->input->post('UDFValues_Id');
    $id = (int) $this->input->post('Patient_ID');

    $next_form = $this->input->post('next_form');
    $btn = $this->input->post('back');

    $this->form_validation->set_rules($this->PatientUDFValuesModel->validation_create());
    $this->form_validation->set_error_delimiters(ERRORS_STYLE_OPEN, ERRORS_STYLE_END);
    if ($this->form_validation->run() == FALSE) {
      $this->user_defined_fields($id);
    } else {
      $dt_update = $this->get_params_udf();
      $this->PatientUDFValuesModel->update($UDFValues_Id, $dt_update);
      //log
      $ApplicationSpecificText = "Updated Patient UDF Record ";
      $this->mylib->action_audit_log($ApplicationSpecificText, "PT", "U", $UDFValues_Id, $id);
      if (!empty($next_form)) {
        redirect("patient/$next_form/$id");
      } else {
        redirect("patient/user_defined_fields/$id");
      }
    }
  }

  function patient_appointment_history($patient_id = NULL) {
    $patient_id = (int) $patient_id;
    $data['patient_id'] = $patient_id;
    $dt_patient = $this->get_dt_pateint($patient_id);
    if ($dt_patient) {
      $data['patient'] = $dt_patient;
      $data['dt'] = $this->AppointmentModel->get_appointments_by_patient($patient_id)->result();
      $data['partial'] = $this->self . "/appointment_history";
    } else {
      $data['partial'] = $this->self . "/patient_not_found";
    }
    $this->load->view('layout', $data);
  }

  function patient_encounter_history($patient_id = NULL) {
    $dt_patient = $this->get_dt_pateint($patient_id);
    if ($dt_patient) {
      $data['partial'] = $this->self . "/encounter_history";
      $data['encounter_history'] = $this->EncounterHistoryModel->patient_history("EncounterHistory.Patient_ID", $patient_id)->result();
    } else {
      $data['partial'] = $this->self . "/patient_not_found";
    }
    $this->load->view('layout', $data);
  }

  private function insert_udf($id) {
    $dt_insert = array(
        'Patient_Id' => $id,
        'Org_Id' => $this->current_user->Org_Id
    );
    $check = $this->PatientUDFValuesModel->get_where($dt_insert)->num_rows();
    if ($check == 0) {
      $this->PatientUDFValuesModel->insert($dt_insert);
    }
  }

  private function get_params_udf() {
    $dt_post = $this->input->post();
    $all_params = $this->PatientUDFValuesModel->get_params();
    $date_fields = $this->PatientUDFValuesModel->get_params('date');

    foreach ($dt_post as $key => $value) {
      if (in_array($key, $all_params)) {
        if ($value == "") {
          $dt_update[$key] = NULL;
        } else {
          if (in_array($key, $date_fields)) {
            $dt_update[$key] = date('Y-m-d', (strtotime($value)));
          } else {
            $dt_update[$key] = $value;
          }
        }
      }
    }
    $dt_update['Org_ID'] = $this->current_user->Org_Id;
    return $dt_update;
  }

}
