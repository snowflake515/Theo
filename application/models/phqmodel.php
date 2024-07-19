<?php

class PhqModel extends CI_Model
{
  var $phq2_ids = '358,359';
  var $phq2_id = '1147';

  var $phq9_ids = '360,385,386,387,388,389,390';
  var $phq9_id = '431';

  function __construct()
  {
    parent::__construct();
    $this->data_db = $this->load->database('data', TRUE);
  }

  function check_phq($tml1_id, $encounter_ID)
  {
    $this->phq2($tml1_id, $encounter_ID);
    $this->phq9($tml1_id, $encounter_ID);
  }

  function check_all_phq($encounter_ID)
  { 
    $tml1 = $this->get_tml1_input($encounter_ID)->result();
    foreach ($tml1 as  $t1) {
      $tml1_id = $t1->TML1_ID;
      $this->check_phq($tml1_id, $encounter_ID);
    }
  }

  function phq2($tml1_id, $encounter_ID, $get = false)
  {
    $config = array(
      'phq_type' => 'phq2',
      'phq2_total' => 0,
      'tml1_id' => (int)$tml1_id,
      'encounter_ID' => (int)$encounter_ID,
      'TML3_TBotMaster_IDs' => $this->phq2_ids,
      'TML3_TBotMaster_ID' => $this->phq2_id,
    );
    if ($get) {
      return $this->get_phq($config);
    } else {
      $this->phq($config);
    }
  }

  function phq9($tml1_id, $encounter_ID, $get = false)
  {
    $config = array(
      'phq_type' => 'phq9',
      'phq2_total' => $this->phq2($tml1_id, $encounter_ID, true),
      'tml1_id' => $tml1_id,
      'encounter_ID' => $encounter_ID,
      'TML3_TBotMaster_IDs' => $this->phq9_ids,
      'TML3_TBotMaster_ID' => $this->phq9_id,
    );
    if ($get) {
      return $this->get_phq($config);
    } else {
      $this->phq($config);
    }
  }



  function phq($config)
  {
    $phq_type = $config['phq_type'];
    $phq2_total = $config['phq2_total'];
    $tml1_id = $config['tml1_id'];
    $encounter_ID = $config['encounter_ID'];
    $TML3_TBotMaster_IDs = $config['TML3_TBotMaster_IDs'];
    $TML3_TBotMaster_ID = $config['TML3_TBotMaster_ID'];

    $phq_total = 0;

    $sql = "TML3.TML3_TBotMaster_ID IN ($TML3_TBotMaster_IDs) and TML1.TML1_ID = $tml1_id and TML3.Preselected = 0";
    $phq_bot_ids = $this->Tml3Model->get_data_save($sql);
    $bt_num_rows = $phq_bot_ids->num_rows();
    foreach ($phq_bot_ids->result() as $bt) {
      $tb = $this->TabletInputModel->get_data($bt->TML3_ID, $encounter_ID, "(Status <> 'X' OR Status is NULL)")->row();
      if ($tb) {
        $phq_total = $phq_total + $bt->TML3_TBotData;
      }
    }
    if ($phq_type == 'phq9') {
      $phq_total = ($phq2_total >= 3) ? $phq2_total + $phq_total : 0;
    }

    $sql = "TML3.TML3_TBotMaster_ID IN ($TML3_TBotMaster_ID) and TML1.TML1_ID = $tml1_id";
    $input_phqs = $this->Tml3Model->get_data_join($sql)->result();
    if ($input_phqs) {
		foreach($input_phqs as $input_phq){
			
			  $dt_post = array(
				'Encounter_ID' => $encounter_ID,
				'TML1_ID' => $input_phq->TML1_ID,
				'TML2_ID' => $input_phq->TML2_ID,
				'TML3_ID' => $input_phq->TML3_ID,
				'TML3_Value' => $phq_total,
				'Status' => null
			  );
			  $t_phq = $this->TabletInputModel->get_data($input_phq->TML3_ID, $encounter_ID)->row();
			  if ($t_phq) {
				$this->TabletInputModel->update($t_phq->TabletInput_ID, $dt_post);
			  } else {
				$this->TabletInputModel->insert($dt_post);
			  }

			  $cek_tml3_input = $this->ETL3InputModel->get_by_field(
				'Encounter_Id',
				$encounter_ID,
				'TML3_Id = ' . $input_phq->TML3_ID
			  );
			  $dt_insert = array(
				'Encounter_Id' => $encounter_ID,
				'TML3_Id' => $input_phq->TML3_ID,
				'ETL3Input' => $phq_total
			  );

			  if ($cek_tml3_input->num_rows() == 0) {
				$this->ETL3InputModel->insert($dt_insert);
			  } else {
				$this->ETL3InputModel->update_where(
				  array(
					'Encounter_Id' => $encounter_ID,
					'TML3_Id' => $input_phq->TML3_ID
				  ),
				  array('ETL3Input' => $phq_total)
				);
			  }
		}
    }
  }



  function get_phq($config)
  {
    $tml1_id = $config['tml1_id'];
    $encounter_ID = $config['encounter_ID'];
    $TML3_TBotMaster_ID = $config['TML3_TBotMaster_ID'];

    $phq_total = 0;
    $sql = "TML3.TML3_TBotMaster_ID IN ($TML3_TBotMaster_ID) and TML1.TML1_ID = $tml1_id";
    $input_phq = $this->Tml3Model->get_data_save($sql)->row();
    if ($input_phq) {
      $t_phq = $this->TabletInputModel->get_data($input_phq->TML3_ID, $encounter_ID)->row();
      if ($t_phq) {
        $phq_total = (int)$t_phq->TML3_Value;
      }
    }
    return $phq_total;
  }

  function get_tml1_input($encounter_ID){ 
    $this->data_db->select('TML1_ID');
    $this->data_db->from('TabletInput');
    $this->data_db->group_by('TML1_ID');
    $this->data_db->where('Encounter_ID', $encounter_ID);
    return $this->data_db->get();
  }



}
