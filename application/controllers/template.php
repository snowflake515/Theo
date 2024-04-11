<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Template extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->self = $this->router->fetch_class();
    $ress = ($this->router->fetch_method() == 'save') ? 'json' : NULL;
    $this->current_user = $this->sessionlib->current_user($ress);
    my_show_404();
  }

  function tml1($id_encounter = null, $id_appt = null) {
    $id_org = $this->current_user->Org_Id;
    $con = "(TML1_Org_ID = $id_org)" . ' and (Hidden = 0 OR Hidden IS NULL)';
    $data['dt'] = null;
    $data['id_appt'] = $id_appt;
    $data['id_encounter'] = $id_encounter;
    $data['template1'] = $this->Tml1Model->get_where(FALSE, $con)->result();
    $data['partial'] = $this->self . "/template1";
    $this->load->view('layout', $data);
  }

  function tml2() {
    $id = $this->input->post('TML1_ID');
    $con = '(Hidden = 0 OR Hidden IS NULL)';
    $data['id_encounter'] = $this->input->post('id_encounter');
    $data['dt'] = $this->Tml1Model->get_by_id($id)->row();
    $data['template2'] = $this->Tml2Model->get_by_field('TML1_ID', $id, $con)->result();
    $data['partial'] = $this->self . "/template2";
    $this->load->view('layout', $data);
  }

  function tml3() {
    $data['tml1'] = $this->input->post('id_tml1');
    $data['dt'] = $this->Tml1Model->get_by_id($this->input->post('id_tml1'))->row();
    $id_encounter = (int) $this->input->post('id_encounter');
    $data['id_encounter'] = $id_encounter;
    $dt_appt = $this->EncounterHistoryModel->get_by_id($id_encounter)->row();
    $data['id_appt'] = ($dt_appt && $dt_appt->Appointments_ID) ? $dt_appt->Appointments_ID : 0;
    $data['post'] = $this->input->post('template');
    $data['partial'] = $this->self . "/template3";
    $this->load->view('layout', $data);
  }

  function save() {
    $check = $this->TabletInputModel->get_by_field(
            'Encounter_ID', $this->input->post('Encounter_ID'), 
            "(Status IS NULL OR Status <> 'X') AND TML3_ID = " . $this->input->post('TML3_ID'));
    $dt_post = $this->input->post();
    $status_check = $dt_post['status'];
    $dt_post['Status'] = NULL;
    $dt_post['Hidden'] = NULL;

    unset($dt_post['status']);
    if ($check->num_rows() == 0) {
      $this->TabletInputModel->insert($dt_post);
    } else {
      $check = $check->row();

      if ($status_check == 0) {
        $dt_post['Status'] = 'X';
        $dt_post['TML3_Value'] = NULL;
      }
      $this->TabletInputModel->update($check->TabletInput_ID, $dt_post);
    }

    //etl
    $cek_tml = $this->ETLModel->get_by_field('Encounter_Id', $dt_post['Encounter_ID'])->num_rows();
    if ($cek_tml == 0) {
      $dt_insert = array(
          'Encounter_Id' => $dt_post['Encounter_ID'],
          'ETLSaved' => 1
      );
      $this->ETLModel->insert($dt_insert);
    }

    //etl2
    $cek_tml2 = $this->ETL2Model->get_by_field('Encounter_Id', $dt_post['Encounter_ID'], 'TML2_Id = ' . $dt_post['TML2_ID'])->num_rows();
    if ($cek_tml2 == 0) {
      $dt_insert = array(
          'Encounter_Id' => $dt_post['Encounter_ID'],
          'TML2_Id' => $dt_post['TML2_ID']
      );
      $this->ETL2Model->insert($dt_insert);
    }

    //etl3
    $cek_tml3 = $this->ETL3Model->get_by_field('Encounter_Id', $dt_post['Encounter_ID'], 'TML3_Id = ' . $dt_post['TML3_ID'])->num_rows();
    if ($cek_tml3 == 0) {
      $dt_insert = array(
          'Encounter_Id' => $dt_post['Encounter_ID'],
          'TML3_Id' => $dt_post['TML3_ID']
      );
      $this->ETL3Model->insert($dt_insert);
    }else{
      $dt_con = array(
		  'Encounter_Id' => $dt_post['Encounter_ID'],
          'TML3_Id' => $dt_post['TML3_ID'],
		);
      if ($status_check == 0) {
          $this->ETL3Model->delete_where($dt_con);
      }
    }


    //etl3_input
    $cek_tml3_input = $this->ETL3InputModel->get_by_field('Encounter_Id', $dt_post['Encounter_ID'], 'TML3_Id = ' . $dt_post['TML3_ID']);
    $dt_insert = array(
        'Encounter_Id' => $dt_post['Encounter_ID'],
        'TML3_Id' => $dt_post['TML3_ID'],
        'ETL3Input' => $dt_post['TML3_Value']
    );
    if ($cek_tml3_input->num_rows() == 0) {
      $this->ETL3InputModel->insert($dt_insert);
	  
      //log
      $id = $this->ETL3InputModel->get_last_insert();
      $dt_en = $this->EncounterHistoryModel->get_by_id($dt_post['Encounter_ID'])->row();
      $patient_id = ($dt_en) ? $dt_en->Patient_ID : 0;
      $ApplicationSpecificText = "Insert Template value";
      $this->mylib->action_audit_log($ApplicationSpecificText, "TML", "A", $id, $patient_id);
    } else {
      $row = $cek_tml3_input->row();
	  $dt_con = array(
		  'Encounter_Id' => $dt_post['Encounter_ID'],
          'TML3_Id' => $dt_post['TML3_ID'],
		);
      $this->ETL3InputModel->update_where($dt_con, $dt_insert);
	  
	  if ($status_check == 0) {
		
        $this->ETL3InputModel->delete_where($dt_con);
      }

      //log
      $dt_en = $this->EncounterHistoryModel->get_by_id($dt_post['Encounter_ID'])->row();
      $patient_id = ($dt_en) ? $dt_en->Patient_ID : 0;
      $ApplicationSpecificText = "Updated Template value";
      $this->mylib->action_audit_log($ApplicationSpecificText, "TML", "U", $row->ETL3Input_Id, $patient_id);
    }
  }

}
