<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Password extends CI_Controller {
	
	public $SPECIAL_USERS;
	
	//-------------------------
	function __construct() {
		parent::__construct();
		$not_required_login = array();
		$this->current_user = $this->sessionlib->current_user();
		$this->load->model('usermodel');
		$this->load->model('orgprofilemodel');
		//$this->load->helper('vayes_helper');
		
		//If ever addtl users are needed, insert all caps user_id
		$this->SPECIAL_USERS = array("ORG1", "AWACS1"); //array("ORG1", "AWACS1", "PAB");
    }
	
	//-------------------------
	function test(){
		/**
		*
		* vdebug($this->usermodel->get_by_id($this->current_user->ID)->result(), false, false, false);
		*/
		//vdebug($this->current_user, false, false, false);
		//vdebug($this->session->userdata, false, false, false);
	}
	
	//-------------------------
	function password_reset(){
		$this->usermodel->update($this->current_user->ID, array('resetPassword' => 1));
		//vdebug($this->usermodel->get_by_id($this->current_user->ID)->result(), false, false, false);
	}
	
	//-------------------------
	function checkResetPasswordBit(){
		header('Content-type: application/json');
		echo $this->current_user->ResetPassword;
	}
	
	//-------------------------
	function changePassword(){
		$response['status'] = false;
		$response['message'] = '';
		
		if($this->input->post('new-password') && $this->input->post('confirm-password')){
			$new_password = $this->input->post('new-password');
			if($this->validateNewPassword($new_password)){
				$this->usermodel->update($this->current_user->ID, array('Password' => $this->usermodel->encript($new_password), 'resetPassword' => 0));
				$response['status']  = true;
				$response['message'] = 'Successfully updated password.';
			}else{
				$response['message'] = 'New password must not be the same as the current password.';
			}
		}else{
			$response['message'] = 'All fields are required.';
		}
		
		header('Content-type: application/json');
        echo json_encode($response);
	}
	
	//-------------------------
	private function validateNewPassword($pNewPassword){
		$isValid = false;
		if($this->usermodel->encript($pNewPassword) != $this->current_user->Password){
				$isValid = true;
		}
		return $isValid;
	}
	
	//-------------------------
	function setOrgID(){
		$response['status'] = false;
		$response['message'] = "";
		
		if($this->input->get('orgid')){
			$orgid = $this->input->get('orgid');
			if($orgid && !empty($orgid)){
				$this->session->set_userdata('ORG_ID', $orgid);
				$this->usermodel->update($this->current_user->ID, array('Org_ID' => $this->session->userdata['ORG_ID']));
				$response['status']  = true;
				$response['org_id']  = $this->session->userdata['ORG_ID'];
				$response['message'] = "Organization successfully selected.";
			}
		}else{
			$response['message'] = "Invalid Org ID. Please select organization.";
		}
		
		header('Content-type: application/json');
        echo json_encode($response);
	}
	
	//-------------------------
	function checkUser(){
		$response['status'] = false;
		$response['message'] = "";
		
		$special_user_array = $this->SPECIAL_USERS;
		if($this->current_user->User_Id){
			$user['id'] 	 = $this->current_user->ID;
			$user['user_id'] = strtoupper($this->current_user->User_Id);
			if(!$this->session->userdata('ORG_ID')){
				if(in_array($user['user_id'], $special_user_array)){
					$response['status'] = true;
					$response['user'] = $user;
					$response['org_list_html'] = $this->getOrgList();
				}
			}else{
				$response['message'] = "Org ID already set.";
			}
		}else{
			$response['message'] = "No user found";
		}
		
		header('Content-type: application/json');
        echo json_encode($response);
	}
	
	//-------------------------
	private function getOrgList(){
		$org_list_html = '<option selected="true" disabled="disabled">Please select your organization</option>';
		$this->data_db = $this->load->database('data', TRUE);
		$query = "SELECT Org_ID, OrgName, OrgAddr1, OrgAddr2, OrgCity, OrgState 
				 FROM OrgProfile 
				 WHERE (Hidden IS NULL OR Hidden = 0) 
				 ORDER BY OrgName";
		$result = $this->data_db->query($query);
		
		if($result && count($result->result()) > 0){
			$org_list = $result->result();
			foreach($org_list as $org_obj){
				$org_list_html .= '<option value="'. $org_obj->Org_ID .'">'. $org_obj->OrgName .'</option>';
			}
		}
		
		return $org_list_html;
	}
	
	//-------------------------
	function changeOrg(){
		$response['status'] = true;
		
		if($this->session->userdata('ORG_ID')){
			$this->session->unset_userdata('ORG_ID');
		}
		
		header('Content-type: application/json');
        echo json_encode($response);
	}
}