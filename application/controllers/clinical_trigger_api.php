<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Clinical_Trigger_API extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->current_user = $this->sessionlib->current_user();
		$this->load->model('PatientProfileModel');
	}

	function revup_referral($encounter_id = 0) {
		$response['status'] = false;
		if ($encounter_id) {
			$encounter_id = $this->EncounterHistoryModel->deccrypt($encounter_id);
			
			$ecounter_obj = $this->EncounterHistoryModel->get_by_id((int) $encounter_id);
			if($ecounter_obj){
				$ecounter_obj = $ecounter_obj->row();
				$patient_obj  = $this->PatientProfileModel->get_by_id($ecounter_obj->Patient_ID);
				if($patient_obj){
					$patient_obj = $patient_obj->row();
					$response['status'] = true;
					$response['patient'] = $patient_obj;
				}
			}
		}
		
		header('Content-type: application/json');
        echo json_encode($response);
	}
}