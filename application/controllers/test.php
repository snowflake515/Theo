<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Test extends CI_Controller {

  function __construct() {
    parent::__construct();
    //$this->current_user = $this->sessionlib->current_user();
    //$this->self = $this->router->fetch_class();
	
  }

  function index() {
     $str = '186|203|184|179|192|197|188|121|127|180|';
     $str = '178|190|166|132|132|138|205|';
     echo $this->UserModel->deccrypt($str);
  }
  
  function db2(){
	  return $this->load->database('template2', TRUE);
  }
  
  private function import_tml(){
	  $db = $this->db2();
	  $list = $db->from('tml1')->where('tml1_id', 4220)->get()->row_array();
	  unset($list['TML1_ID']);
	  $list['TML1_Org_ID'] = 0;
	  $list['TML1_Provider_ID'] = 0;
	  echo '<hr>TML1 <hr>';
	  var_dump($list);
	  $this->Tml1Model->insert($list);
	  $tml1_id =  $this->Tml1Model->get_last_insert(); 
	  
	  $db = $this->db2();
	  $list2 = $db->from('tml2')->where('tml1_id', 4220)->get()->result_array();
	  
	  foreach($list2 as $tml2){
		$t2_id = $tml2['TML2_ID'];
		
		unset($tml2['TML2_ID']);
		$tml2['TML1_ID'] = $tml1_id;
		$this->Tml2Model->insert($tml2);
		$tml2_id =  $this->Tml2Model->get_last_insert(); 
		
		echo '<hr>TML2 <hr>';
		var_dump($tml2);
		 	
		$db = $this->db2();
		$list3 = $db->from('tml3')->where('tml2_id', $t2_id)->get()->result_array();
		 foreach($list3 as $tml3){
			 echo '<hr>TML3 <hr>';
			 
			 unset($tml3['TML3_ID']);
			 
			 $tml3['TML2_ID'] = $tml2_id;
			 $this->Tml3Model->insert($tml3);
			 var_dump($tml3);	
			
			 
		 }
			 
	 }
	    
	  
  }
  
  

}
