<?php

class TabletTriggersModel extends CI_Model {

  var $table = "TabletTriggers";
  var $key = "TabletTriggers_ID";

  function __construct() {
    parent::__construct();
    $this->data_db = $this->load->database('data', TRUE);
    $this->template_db = $this->load->database('template', TRUE);
  }

  function insert($data) {
    $this->data_db->trans_begin();
    $this->data_db->insert($this->table, $data);
    $this->data_db->trans_commit();
  }

  function update($id, $data) {
    $this->data_db->trans_begin();
    $this->data_db->where($this->key, $id);
    $this->data_db->update($this->table, $data);
    $this->data_db->trans_commit();
  }

  function delete($id) {
    $this->data_db->trans_begin();
    $this->data_db->where($this->key, $id);
    $this->data_db->delete($this->table);
    $this->data_db->trans_commit();
  }

  function get_all() {
    return $this->data_db->get($this->table);
  }

  function get_by_id($id) {
    $this->data_db->where($this->key, $id);
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }

  function get_by_field($field, $val, $other_condition = null) {
    $this->data_db->where($field, $val);
    if ($other_condition != null) {
      $this->data_db->where($other_condition);
    }
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }

  function pack_years($encounter_id = 0) {
    $cros_db_data = $this->data_db->database;
    $cros_db_template = $this->template_db->database;

    $sql = " SELECT TOP 1 ti.TML3_Value
      FROM $cros_db_data.dbo.TabletInput ti
      JOIN $cros_db_template.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
      WHERE ti.Encounter_ID = $encounter_id AND tm3.TML3_TBotMaster_ID IS NOT
      NULL
      AND (ti.Status IS NULL OR ti.Status <> 'X')
      AND tm3.TML3_TBotMaster_ID = 147;";
    $query = $this->data_db->query($sql);
    $per_y = ($query->num_rows() != 0) ? (int) $query->row()->TML3_Value : 0;
    //echo $query->row()->TML3_Value;

    $sql = " SELECT TOP 1  ti.TML3_Value
      FROM $cros_db_data.dbo.TabletInput ti
      JOIN $cros_db_template.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
      WHERE ti.Encounter_ID = $encounter_id AND tm3.TML3_TBotMaster_ID IS NOT
      NULL
      AND (ti.Status IS NULL OR ti.Status <> 'X')
      AND tm3.TML3_TBotMaster_ID = 146;";
    $query = $this->data_db->query($sql);
    $per_d = ($query->num_rows() != 0) ? (int) $query->row()->TML3_Value : 0;
    //echo $query->row()->TML3_Value;

    return ($per_d * $per_y);
  }

  function data_tirger($encounter_id = 0, $condition = NULL) {

    $check_sql = array();
    $encounter_id = (int) $encounter_id;
    $conditions = explode(DELIMITER_SQL, $condition);
    $master_data_ids = $this->get_master_data_ids($encounter_id);
    foreach ($conditions as $condition) {
      if (!empty($condition)) {
        //echo $condition;
        $check_sql[] = $this->get_data_trigger($encounter_id, $condition, $master_data_ids);
      }
    }
    //var_dump($check_sql);
    $return = ($check_sql && !in_array(FALSE, $check_sql));
    //echo '======>';
    //var_dump($return);
    //echo '<hr/>';
    return $return;
  }

  private function tbot_count_check($condition, $encounter_id){
    $string = $condition;
    preg_match_all("/@SumValue+(.*)\@end/", $string, $matches);
    if(!empty($matches[1])){
    	foreach($matches[1] as $key => $s ){
    		$s = explode(')', $s);
        if(!empty($s[0]) && !empty($s[1])){
          $ids = str_replace(array('(', ')'), '', $s[0]);
		 
		  $ids_ex = explode(',', $ids);
          $idx = array();
          foreach ($ids_ex as  $value) { 
			  if($value){
				  $idx[] = (int)$value;
			  } 
          }
          $ids = (count($idx) > 0) ? implode(',', $idx) : 0; 
	 
          $sql = "SELECT
                  distinct tm3.TML3_TBotMaster_ID AS TBotMaster
                  FROM Wellness_eCastEMR_Data.dbo.TabletInput ti
                  JOIN Wellness_eCastEMR_Template.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
                  WHERE (ti.Encounter_ID = $encounter_id)
                    AND (tm3.TML3_TBotMaster_ID IS NOT NULL)
                    AND (ti.Status IS NULL OR ti.Status <> 'X')
                    AND tm3.TML3_TBotMaster_ID IN ($ids)
          ";

          $exec = $this->data_db->query($sql);
          $result_num = 0;
          if($exec && $exec->num_rows()){
            $result_num = $exec->num_rows();
          }
      		$int = (int) filter_var($s[1], FILTER_SANITIZE_NUMBER_INT);
      		$op = $this->get_op_tbot_count($s[1],  $result_num, $int);
      		$q = NULL;
      		if($op === TRUE){
      			$q = ' (1 = 1) ';
      		}elseif($op === FALSE){
      			$q = ' (1 != 1) ';
      		}
      		if($q !== NULL){
				if(!empty($matches[0]) && !empty($matches[0][$key])){
					$string  = str_replace($matches[0][$key], $q, $string);
				}  
      		}
			
        }
    	}
    }

    return $string;

  }


  private function get_op_tbot_count($str, $a, $b){
    $arr_op = array('>=', '<=', '<>', '!=', '!<', '!>','=', '>', '<');
  	$g_op  = NULL;
  	$res = NULL;
  	foreach ($arr_op as $op){
  		$pos = strpos($str, $op);
  		if($pos){
  		    $g_op =  $op;
  			break;
  		}
  	}

  	if($g_op){
  		switch($g_op) {
  			case '>=': $res = ($a >= $b);break;
  			case '<=': $res = ($a <= $b);break;
  			case '<>': $res = ($a != $b);break;
  			case '!=': $res = ($a != $b);break;
  			case '!<': $res = ($a <= $b);break;
  			case '!>': $res = ($a >= $b);break;
  			case '=' : $res = ($a == $b);break;
  			case '>' : $res = ($a > $b);break;
  			case '<' : $res = ($a < $b);break;
  		}
  	}
  	return $res;

  }


  function get_data_trigger($encounter_id = 0, $condition = NULL, $master_data_ids = '') {

    $condition = ($condition) ? 'WHERE ' . $condition . ' ' : '';
    $condition = $this->tbot_count_check($condition, $encounter_id);

    $cros_db_data = $this->data_db->database;
    $cros_db_template = $this->template_db->database;
    $add_q = "AND (ti.Status IS NULL OR ti.Status <> 'X')";
    $convert = "CASE ISNUMERIC(ti.TML3_Value) WHEN 1 THEN CAST(ti.TML3_Value AS float) ELSE null END
        AS TabletValue";

    $bmi_v = $this->get_bmi_val($encounter_id);
    $WtHR_v = $this->get_WtHR_val($encounter_id);
    $PulsePressure_v = $this->get_PulsePressure_val($encounter_id);

    $dt = $this->EncounterHistoryModel->get_by_id($encounter_id)->row();
    $patient_dt = $this->PatientProfileModel->get_by_id($dt->Patient_ID)->row();
    $get_gender = $patient_dt->Sex;
    $get_age = ($patient_dt->DOB) ? dob_to_age($patient_dt->DOB) : 0;
    $get_pack_years = 0;
    $get_pack_years = $this->pack_years($encounter_id);
    $addtional = "(select '$get_gender') as Gender,"
            . "(select $get_age) as Age,"
            . "(select $get_pack_years) as Pack_Years, "
            . "(select '$master_data_ids') as MasterData_IDs ";

    $query = $this->data_db->query('
        SELECT * FROM(
        SELECT ti.TabletInput_ID,ti.Encounter_ID,ti.TML3_ID,
        ' . $convert . ',
        ti.TML3_Value AS TabletValueString,
        tm3.TML3_TBotMaster_ID AS TBotMaster,
        tm3.TML3_TBotData AS TBotData,
        tm3.TML3_Description,
        ( select  ' . $bmi_v . ') as BMI,
        ( select  ' . $WtHR_v . ') as WtHR,
        ( select  ' . $PulsePressure_v . ') as PulsePressure,
        ' . $addtional . '
        FROM ' . $cros_db_data . '.dbo.TabletInput ti
        JOIN ' . $cros_db_template . '.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
        WHERE (ti.Encounter_ID = ' . $encounter_id . ')
          AND (tm3.TML3_TBotMaster_ID IS NOT NULL)
          ' . $add_q . '
        ) TEMP_TRIGGER_TABLE
        ' . $condition);
    $return = ($query && $query->num_rows() > 0) ? TRUE : FALSE;
    return $return;
  }

  function get_PulsePressure_val($encounter_id = 0) {
    $id_Systolic = 425;
    $id_Diastolic  = 426;
    $cros_db_data = $this->data_db->database;
    $cros_db_template = $this->template_db->database;
    $PulsePressure = 0;
    $sql = "SELECT TOP 1 ti.TML3_Value
      FROM $cros_db_data.dbo.TabletInput ti
      JOIN $cros_db_template.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
      WHERE ti.Encounter_ID = $encounter_id AND tm3.TML3_TBotMaster_ID IS NOT
      NULL
      AND (ti.Status IS NULL OR ti.Status <> 'X')
      AND tm3.TML3_TBotMaster_ID = $id_Diastolic";

    $query = $this->data_db->query($sql);
    $query = ($query && $query->num_rows() > 0) ? $query->row() : FALSE;
    $Diastolic = ($query) ? $query->TML3_Value : 0;


    $sql = "SELECT TOP 1 ti.TML3_Value
      FROM $cros_db_data.dbo.TabletInput ti
      JOIN $cros_db_template.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
      WHERE ti.Encounter_ID = $encounter_id AND tm3.TML3_TBotMaster_ID IS NOT
      NULL
      AND (ti.Status IS NULL OR ti.Status <> 'X')
      AND tm3.TML3_TBotMaster_ID = $id_Systolic";
    $query = $this->data_db->query($sql);
    $query = ($query && $query->num_rows() > 0) ? $query->row() : FALSE;
    $Systolic = ($query) ? $query->TML3_Value : 0;

    if ($Diastolic > 0 && $Systolic > 0) {
      $PulsePressure = ($Systolic - $Diastolic);
      $PulsePressure = ($PulsePressure < 0) ? 0 : $PulsePressure;
    }
    return $PulsePressure;
  }

  function get_WtHR_val($encounter_id = 0) {
    $id_height = 423;
    $id_waist = 430;
    $cros_db_data = $this->data_db->database;
    $cros_db_template = $this->template_db->database;
    $wthr = 0;
    $sql = "SELECT TOP 1 ti.TML3_Value
      FROM $cros_db_data.dbo.TabletInput ti
      JOIN $cros_db_template.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
      WHERE ti.Encounter_ID = $encounter_id AND tm3.TML3_TBotMaster_ID IS NOT
      NULL
      AND (ti.Status IS NULL OR ti.Status <> 'X')
      AND tm3.TML3_TBotMaster_ID = $id_height";

    $query = $this->data_db->query($sql);
    $query = ($query && $query->num_rows() > 0) ? $query->row() : FALSE;
    $height = ($query) ? $query->TML3_Value : 0;


    $sql = "SELECT TOP 1 ti.TML3_Value
      FROM $cros_db_data.dbo.TabletInput ti
      JOIN $cros_db_template.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
      WHERE ti.Encounter_ID = $encounter_id AND tm3.TML3_TBotMaster_ID IS NOT
      NULL
      AND (ti.Status IS NULL OR ti.Status <> 'X')
      AND tm3.TML3_TBotMaster_ID = $id_waist";
    $query = $this->data_db->query($sql);
    $query = ($query && $query->num_rows() > 0) ? $query->row() : FALSE;
    $waist = ($query) ? $query->TML3_Value : 0;

    if ($waist > 0 && $height > 0) {
      $wthr = ($waist/$height);
    }
    return $wthr;
  }

  function get_bmi_val($encounter_id = 0, $ids = NULL) {
    $id_height = 423;
    $id_weight = 424;
    $cros_db_data = $this->data_db->database;
    $cros_db_template = $this->template_db->database;
    $bmi = 0;
    $sql = "SELECT TOP 1 ti.TML3_Value
      FROM $cros_db_data.dbo.TabletInput ti
      JOIN $cros_db_template.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
      WHERE ti.Encounter_ID = $encounter_id AND tm3.TML3_TBotMaster_ID IS NOT
      NULL
      AND (ti.Status IS NULL OR ti.Status <> 'X')
      AND tm3.TML3_TBotMaster_ID = $id_height";

    $query = $this->data_db->query($sql);
    $query = ($query && $query->num_rows() > 0) ? $query->row() : FALSE;
    $height = ($query) ? $query->TML3_Value : 0;


    $sql = "SELECT TOP 1 ti.TML3_Value
      FROM $cros_db_data.dbo.TabletInput ti
      JOIN $cros_db_template.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
      WHERE ti.Encounter_ID = $encounter_id AND tm3.TML3_TBotMaster_ID IS NOT
      NULL
      AND (ti.Status IS NULL OR ti.Status <> 'X')
      AND tm3.TML3_TBotMaster_ID = $id_weight";
    $query = $this->data_db->query($sql);
    $query = ($query && $query->num_rows() > 0) ? $query->row() : FALSE;
    $weight = ($query) ? $query->TML3_Value : 0;

    if ($weight > 0 && $height > 0) {
      $bmi = (($weight) / ($height * $height)) * 703;
      $bmi = floatval(number_format($bmi, 2));
    }
    return $bmi;
  }

  function g_last_query() {
    return $this->data_db->last_query();
  }

  function create_db_function($type = 'CREATE') {
    $cros_db_data = $this->data_db->database;
    $cros_db_template = $this->template_db->database;
    $alter = "$type FUNCTION dbo.GET_BMI(@IdEncounter int)
    RETURNS REAL
    AS
    BEGIN
      DECLARE @BMI REAL, @HT REAL, @WT REAL
      SELECT TOP 1 @HT = ti.TML3_Value
      FROM $cros_db_data.dbo.TabletInput ti
      JOIN $cros_db_template.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
      WHERE ti.Encounter_ID = @IdEncounter AND tm3.TML3_TBotMaster_ID IS NOT
      NULL
      AND (ti.Status IS NULL OR ti.Status <> 'X')
      AND tm3.TML3_TBotMaster_ID = 136;
      SELECT TOP 1 @WT = ti.TML3_Value
      FROM $cros_db_data.dbo.TabletInput ti
      JOIN $cros_db_template.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
      WHERE ti.Encounter_ID = @IdEncounter AND tm3.TML3_TBotMaster_ID IS NOT
      NULL
      AND (ti.Status IS NULL OR ti.Status <> 'X')
      AND tm3.TML3_TBotMaster_ID = 137;
      SELECT @BMI = ((@WT)/(@HT * @HT))* 703;
      RETURN ( @BMI )
    END;
    ";
    $this->data_db->trans_begin();
    $query = $this->data_db->query($alter);
    $this->data_db->trans_commit();
  }

  function get_by_data($org_id = 0, $provider_id = 0) {
    $this->data_db->where('Org_ID', $org_id);
    $this->data_db->where('Provider_ID', $provider_id);
    $this->data_db->where('(Hidden = 0 OR Hidden IS NULL)');
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }

  function get_master_data_ids($encounter_id = 0) {
    $cros_db_data = $this->data_db->database;
    $cros_db_template = $this->template_db->database;

    $add_q = "AND (ti.Status IS NULL OR ti.Status <> 'X') AND (tm3.Hidden IS NULL OR ti.Hidden <> 1)";
    $sql = "SELECT
        tm3.TML3_TBotMaster_ID AS TBotMaster,
        tm3.TML3_TBotData AS TBotData
        FROM $cros_db_data.dbo.TabletInput ti
        JOIN $cros_db_template.dbo.TML3 tm3 ON ti.TML3_ID = tm3.TML3_ID
        WHERE (ti.Encounter_ID = $encounter_id)
          AND (tm3.TML3_TBotMaster_ID IS NOT NULL)
          $add_q
        ";
    $ids = $this->data_db->query($sql);
    $ret = array();
    foreach ($ids->result() as $v) {
      $ret[] = 'M' . $v->TBotMaster . '_D' . $v->TBotData . '_ID';
    }
    return implode(',', $ret);
  }

}
