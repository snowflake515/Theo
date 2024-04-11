<?php
class ClinicalTriggerModel extends CI_Model
{
  function __construct()
  {
    parent::__construct();
    $this->db = $this->load->database('data', true); 
  }

  function process_clinical_trigger($ecounter){
     
    //get trigger master
    $trigger_master = $this->TabletTriggersModel->get_by_data($ecounter->Org_ID, $ecounter->Provider_ID);
    if ($trigger_master->num_rows() > 0) {
      $trigger_master_result = $trigger_master->result();
    } else {
      $trigger_master = $this->TabletTriggersModel->get_by_data($ecounter->Org_ID, 0);
      if ($trigger_master->num_rows() > 0) {
        $trigger_master_result = $trigger_master->result();
      } else {
        $trigger_master_result = $this->TabletTriggersModel->get_by_data(0, 0)->result();
      }
    }

    if ($ecounter && $ecounter->Encounter_ID) {
      $trigger_arr = array();
      foreach ($trigger_master_result as $trigger) {
        $query = $trigger->TTCriteria;
        if (!empty($query)) {
          $trigger_arr[] = $trigger->TabletTriggers_ID;
        }
      }

      $this->TabletTriggersDataModel->delete_trigger_data($trigger_arr, $ecounter->Encounter_ID, $ecounter->Patient_ID);
      $this->prep_insert_trgger_data($ecounter, $trigger_arr); 
    }
    
  }


  private function prep_insert_trgger_data($ecounter, $trigger) {
    foreach ($trigger as $id) {
      $trigger = $this->TabletTriggersModel->get_by_id($id)->row();
      $check_trigger = $this->TabletTriggersModel->data_tirger($ecounter->Encounter_ID, $trigger->TTCriteria);
      if ($check_trigger == TRUE) {
        $this->insert_trgger_data($ecounter, $trigger);
      }
    }
  }


  private function insert_trgger_data($ecounter, $trigger) {
    $data_inst = array(
        'Org_ID' => $ecounter->Org_ID,
        'Provider_ID' => $ecounter->Provider_ID,
        'Encounter_ID' => $ecounter->Encounter_ID,
        'Patient_ID' => $ecounter->Patient_ID,
        'TabletTriggers_ID' => $trigger->TabletTriggers_ID,
        'DateAdded' => date('Y-m-d H:i:s'),
        'Hidden' => 0
    );

    $con = 'TabletTriggers_ID = ' . $trigger->TabletTriggers_ID . ' AND Encounter_ID =' . $ecounter->Encounter_ID;
    $check = $this->TabletTriggersDataModel->get_by_field('Patient_ID', (int) $ecounter->Patient_ID, $con);
    if ($check->num_rows() > 0) {
      //$check = $check->row();
      // $this->TabletTriggersDataModel->update($check->TabletTriggersData_ID, $data_inst);
    } else {
      $this->TabletTriggersDataModel->insert($data_inst);

      //log
      //   $id = $this->TabletTriggersDataModel->get_last_insert();
      //   $patient_id = ($ecounter) ? $ecounter->Patient_ID : 0;
      //   $ApplicationSpecificText = "Insert CTs";
      //   $this->mylib->action_audit_log($ApplicationSpecificText, "CTS", "A", $id, $patient_id);
    }
  }


}