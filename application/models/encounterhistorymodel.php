<?php

class EncounterHistoryModel extends CI_Model {

  var $table = "EncounterHistory";
  var $key = "Encounter_ID";

  function __construct() {
    parent::__construct();
    $this->data_db = $this->load->database('data', TRUE);
    $this->template_db = $this->load->database('template', TRUE);
  }

  function select_db() {
     return $this->data_db->from($this->table);
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

  function update_where($data, $con) {
    $this->data_db->trans_begin();
    $this->data_db->where($con);
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

  function get_last_insert() {
    //return $this->data_db->insert_id();
    $res = $this->data_db->query("SELECT @@IDENTITY as insert_id")->row_array();
    return intval($res['insert_id']);
  }

  function get_last_history($appt_id) {
    $this->data_db->where('Appointments_ID', $appt_id);
    $this->data_db->limit(1, 0);
    $this->data_db->order_by($this->key, 'desc');
    $this->data_db->from($this->table);
    return $this->data_db->get();
  }

  function validation() {
    $config = array(
        array('field' => 'EncounterDate', 'label' => 'Encounter Date', 'rules' => 'required'),
        array('field' => 'ChiefComplaint', 'label' => 'Problem', 'rules' => 'max_length[1000]'),
        array('field' => 'EncounterDescription_ID', 'label' => 'Encounter Type', 'rules' => 'required'),
        array('field' => 'Provider_ID', 'label' => 'Rendering Provider', 'rules' => 'required'),
        array('field' => 'Dept_ID', 'label' => 'Department', 'rules' => 'required'),
        array('field' => 'Facility_ID', 'label' => 'Facility', 'rules' => 'required')
    );
    return $config;
  }

  function get_last_by_patient($id_patient) {
    $this->data_db->from($this->table);
    $this->data_db->where('Patient_ID', $id_patient);
    $this->data_db->order_by($this->key, 'DESC');
    $this->data_db->limit(1);
    return $this->data_db->get();
  }

  function encript($str = NULL) {
    $key = str_split("QWERTYUIOPASDFGHJKL;");
    $kpost = 0;
    $result = NULL;
    if ($str != NULL) {
      foreach (str_split($str) as $v) {
        $result.=ord($v) + ord($key[$kpost]);
        $result.=".";
        if ($kpost >= 19) {
          $kpost = 1;
        } else {
          $kpost++;
        }
      }
    }
    $c = strlen($result);
    $result = substr($result, 0, $c - 1);
    return $result;
  }

  function deccrypt($str = NULL) {
    $key = str_split("QWERTYUIOPASDFGHJKL;");
    $kpost = 0;
    $result = NULL;
    if ($str != NULL) {
      foreach (explode('.', $str) as $v) {
        if ($v) {
          $k = $v - ord($key[$kpost]);
          $result.=chr($k);
          if ($kpost >= 19) {
            $kpost = 1;
          } else {
            $kpost++;
          }
        }
      }
    }
    return $result;
  }

  function patient_history($field, $val, $other_condition = null) {
    $this->data_db->where($field, $val);
    if ($other_condition != null) {
      $this->data_db->where($other_condition);
    }
    $this->data_db->select($this->table . '.*, ProviderProfile.*, PatientProfile.LastName as p_LastName, PatientProfile.FirstName as p_FirstName');
    $this->data_db->from($this->table);
    $this->data_db->join('ProviderProfile', 'ProviderProfile.Provider_ID = EncounterHistory.Provider_ID');
    $this->data_db->join('Appointments', 'Appointments.Appointments_ID = EncounterHistory.Appointments_ID');
    $this->data_db->join('PatientProfile', 'PatientProfile.Patient_ID = EncounterHistory.Patient_ID');
    $this->data_db->where('(Appointments.Hidden = 0 OR Appointments.Hidden IS NULL)');
    $this->data_db->order_by("EncounterDate", "desc");
    return $this->data_db->get();
  }

  function query_commit($sql){
    $this->data_db->trans_begin();
    $this->data_db->query($sql);
    $this->data_db->trans_commit();
  }

  function query_select($sql){
    return $this->data_db->query($sql);
   }

  function process_hcc($encounter_id) {
    $cros_db_data = $this->data_db->database;
    $cros_db_template = $this->template_db->database;


    //SET Status == Y where TML3_TBotMaster_ID == 325
    $sql = "UPDATE $cros_db_data.dbo.TabletInput
        SET Hidden = 0, Status = 'Y'
          FROM $cros_db_template.dbo.TML3 t3
          JOIN $cros_db_data.dbo.TabletInput ti
          ON ti.TML3_ID = t3.TML3_ID
        WHERE t3.TML3_TBotMaster_ID = 325
          AND ti.Encounter_ID = $encounter_id
          AND (ti.Status IS NULL or ti.Status <> 'X')   ";

    $this->query_commit($sql);


    //=====================
    //Step 1
    //=====================

    //SET Status == 1
    $sql = "UPDATE $cros_db_data.dbo.TabletInput
        SET   Hidden = 0, Status = '1'
        WHERE Hidden IS NULL AND Status IS NULL
          AND Encounter_ID = $encounter_id";

    $this->query_commit($sql);

    //empty temp table
    //---$sql = "TRUNCATE TABLE wellness_eCastEMR_Data.dbo.tempHCC";

    //insert into temp table
    /*---$sql = "INSERT INTO Wellness_eCastEMR_Data.dbo.tempHCC
        SELECT DISTINCT Encounter_ID,0,NULL FROM Wellness_eCastEMR_Data.dbo.TabletInput
        WHERE (Hidden = 0 OR HIDDEN IS NULL) AND Status = '1'";
    */

    //insert into EncounterComponents if no exist
    /*$sql = "SET NOCOUNT ON
        DECLARE @Encounter_ID INT, @n INT, @max INT
        SELECT @n = 1
        SELECT @max = COUNT(*) FROM wellness_eCastEMR_Data.dbo.tempHCC
        WHILE @n <= @max
          BEGIN
            SELECT @Encounter_ID = Encounter_ID FROM wellness_eCastEMR_Data.dbo.tempHCC
            WHERE tempHCC_ID = @n
            --- See if this Encounter_ID exists already in EncounterComponents
            SELECT * FROM Wellness_eCastEMR_Data.dbo.EncounterComponents
            WHERE Encounter_ID = @Encounter_ID
            IF @@ROWCOUNT = 0
              BEGIN
                INSERT INTO Wellness_eCastEMR_Data.dbo.EncounterComponents
                SELECT
            Patient_ID,Encounter_ID,EncounterDate,38,NULL,ChiefComplaint,NULL,EncounterDate,0,NULL,NULL,NULL
                FROM Wellness_eCastEMR_Data.dbo.EncounterHistory eh WHERE eh.Encounter_ID = @Encounter_ID
              END
            SELECT @n = @n + 1
          END
        --- SELECT TOP 10 * FROM Wellness_eCastEMR_Data.dbo.EncounterComponents ORDER BY EncounterComponents_ID DESC";
     */


    $sql = "INSERT INTO $cros_db_data.dbo.EncounterComponents
              SELECT
                Patient_ID,Encounter_ID,EncounterDate,38,NULL,ChiefComplaint,NULL,EncounterDate,0,NULL,NULL,NULL
                FROM $cros_db_data.dbo.EncounterHistory eh
                WHERE eh.Encounter_ID = $encounter_id
                AND
                eh.Encounter_ID not in (
                  SELECT DISTINCT Encounter_ID
                  FROM $cros_db_data.dbo.EncounterComponents
                  WHERE Encounter_ID = $encounter_id
                  AND HeaderMaster_ID = 38)";
    $this->query_commit($sql);



    //=====================
    //Step 2
    //=====================

    // SET Status == 2
    $sql = "UPDATE $cros_db_data.dbo.TabletInput
        SET Status = '2'
        WHERE Status = '1' AND Hidden = 0
        AND Encounter_ID = $encounter_id";

    $this->query_commit($sql);


    $sql = "INSERT INTO $cros_db_data.dbo.ETL3Input
        SELECT Encounter_ID,TML3_ID,TML3_Value,0,NULL
        FROM $cros_db_data.dbo.TabletInput
        WHERE TML3_Value <> ''
        AND Status = '2'
        AND (Hidden = 0 OR Hidden IS NULL)
        AND TML3_ID NOT IN (SELECT DISTINCT TML3_Id FROM ETL3Input WHERE Encounter_Id = $encounter_id and [ETL3Input] <> '' )
        AND Encounter_ID = $encounter_id";
    $this->query_commit($sql);


    //=====================
    //Step 3
    //=====================
    $sql = "UPDATE $cros_db_data.dbo.TabletInput
        SET Status = '3'
        WHERE Status = '2' AND Hidden = 0 AND Encounter_ID = $encounter_id";
    $this->query_commit($sql);


    $sql = "INSERT INTO $cros_db_data.dbo.ETL3
        SELECT Encounter_ID,TML3_ID,GetDate()
        FROM $cros_db_data.dbo.TabletInput
        WHERE (Hidden = 0 OR Hidden IS NULL)
        AND Status = '3'
        AND Encounter_ID = $encounter_id
        AND TML3_ID NOT IN (SELECT DISTINCT TML3_ID FROM $cros_db_data.dbo.ETL3 WHERE Encounter_ID = $encounter_id)
        ";
    $this->query_commit($sql);


    //=====================
    //Step 4
    //=====================

    $sql = "UPDATE $cros_db_data.dbo.TabletInput
        SET Status = '4'
        WHERE Status = '3'
        AND Hidden = 0
        AND Encounter_ID = $encounter_id
        ";

    $this->query_commit($sql);

    //$sql = "TRUNCATE TABLE wellness_eCastEMR_Data.dbo.tempHCC";

    /*$sql = "INSERT INTO Wellness_eCastEMR_Data.dbo.tempHCC
        SELECT DISTINCT Encounter_ID,0,NULL FROM Wellness_eCastEMR_Data.dbo.TabletInput
        WHERE (Hidden = 0 OR HIDDEN IS NULL) AND Status = '4'";
    */



    /*$sql = "SET NOCOUNT ON
        DECLARE @Encounter_ID INT, @n INT, @max INT
        SELECT @n = 1
        SELECT @max = COUNT(*) FROM wellness_eCastEMR_Data.dbo.tempHCC
        WHILE @n <= @max
          BEGIN
            SELECT @Encounter_ID = Encounter_ID FROM wellness_eCastEMR_Data.dbo.tempHCC
            WHERE tempHCC_ID = @n
            --- See if this Encounter_ID exists already in ETL1
            SELECT * FROM Wellness_eCastEMR_Data.dbo.ETL1
            WHERE Encounter_ID = @Encounter_ID
            IF @@ROWCOUNT = 0
              BEGIN
                INSERT INTO Wellness_eCastEMR_Data.dbo.ETL1
                SELECT DISTINCT Encounter_ID,TML1_ID FROM Wellness_eCastEMR_Data.dbo.TabletInput
                WHERE Encounter_ID = @Encounter_ID
              END
            SELECT @n = @n + 1
          END";
     * *
     */

    $sql = "INSERT INTO $cros_db_data.dbo.ETL1
                SELECT DISTINCT Encounter_ID,TML1_ID FROM $cros_db_data.dbo.TabletInput
					WHERE (Hidden = 0 OR HIDDEN IS NULL)
                    AND Status = '4'
                    AND Encounter_ID = $encounter_id
					AND TML1_ID NOT IN (SELECT DISTINCT TML1_ID FROM $cros_db_data.dbo.ETL1 where Encounter_ID = $encounter_id)";
    $this->query_commit($sql);



    //=====================
    //Step 5
    //=====================


    $sql = "UPDATE $cros_db_data.dbo.TabletInput
        SET Status = '5'
        WHERE Status = '4'
        AND Hidden = 0
        AND Encounter_ID = $encounter_id";
    $this->query_commit($sql);

    $sql = "INSERT INTO $cros_db_data.dbo.ETL2
        SELECT DISTINCT Encounter_ID, TML2_ID
        FROM $cros_db_data.dbo.TabletInput
        WHERE (Hidden = 0 OR HIDDEN IS NULL)
        AND Status = '5'
        AND Encounter_ID = $encounter_id
        AND TML2_ID NOT IN (SELECT DISTINCT TML2_ID FROM $cros_db_data.dbo.ETL2 WHERE Encounter_ID = $encounter_id)";
    $this->query_commit($sql);

    //=====================
    //Step 6
    //=====================

    $sql = "UPDATE $cros_db_data.dbo.TabletInput
        SET Status = '6'
        WHERE Status = '5'
        AND Hidden = 0
        AND Encounter_ID = $encounter_id";
    $this->query_commit($sql);

    //$sql = "TRUNCATE TABLE wellness_eCastEMR_Data.dbo.tempHCC";

    /*$sql = "INSERT INTO Wellness_eCastEMR_Data.dbo.tempHCC
        SELECT DISTINCT Encounter_ID,0,NULL FROM Wellness_eCastEMR_Data.dbo.TabletInput
        WHERE (Hidden = 0 OR HIDDEN IS NULL) AND Status = '6'
        ";
     *
     */


    /*$sql = "SET NOCOUNT ON
        DECLARE @Encounter_ID INT, @n INT, @max INT
        SELECT @n = 1
        SELECT @max = COUNT(*) FROM wellness_eCastEMR_Data.dbo.tempHCC
        WHILE @n <= @max
          BEGIN
            SELECT @Encounter_ID = Encounter_ID FROM wellness_eCastEMR_Data.dbo.tempHCC
            WHERE tempHCC_ID = @n
            --- See if this Encounter_ID exists already in ETL
            SELECT * FROM Wellness_eCastEMR_Data.dbo.ETL
            WHERE Encounter_ID = @Encounter_ID
            IF @@ROWCOUNT = 0
              BEGIN
                INSERT INTO Wellness_eCastEMR_Data.dbo.ETL
                SELECT @Encounter_ID,1
              END
            SELECT @n = @n + 1
          END
        ";
     *
     */


    $sql = "INSERT INTO $cros_db_data.dbo.ETL
                SELECT DISTINCT Encounter_ID, 1 FROM $cros_db_data.dbo.TabletInput
                WHERE (Hidden = 0 OR HIDDEN IS NULL)
                  AND Status = '6'
                  AND Encounter_ID = $encounter_id
                  AND Encounter_ID NOT IN (SELECT DISTINCT Encounter_ID FROM $cros_db_data.dbo.ETL WHERE Encounter_ID = $encounter_id )";
    $this->query_commit($sql);

    //=====================
    //Step 7
    //=====================


    $sql = "UPDATE $cros_db_data.dbo.TabletInput
        SET Status = '7'
        WHERE Status = '6'
        AND Hidden = 0
        AND Encounter_ID = $encounter_id";
    $this->query_commit($sql);

    //=====================
    //Step 8
    //=====================

    $sql = "UPDATE $cros_db_data.dbo.TabletInput
        SET Status = '8'
        WHERE Status = '7'
        AND Hidden = 0
        AND Encounter_ID = $encounter_id";
     $this->query_commit($sql);



    //$sql = "TRUNCATE TABLE wellness_eCastEMR_Data.dbo.tempHCC";

    /*$sql = "INSERT INTO Wellness_eCastEMR_Data.dbo.tempHCC
        SELECT Encounter_ID,TML3_ID,'HCC RAF Values'
        FROM Wellness_eCastEMR_Data.dbo.TabletInput
        WHERE Status = 'Y'";
    */
    $sql = "SELECT Encounter_ID,TML3_ID,'HCC RAF Values' as HCC
        FROM $cros_db_data.dbo.TabletInput
        WHERE Status = 'Y'
        AND Encounter_ID = $encounter_id";
    $list_status_y = $this->query_select($sql);

    /*$sql = "SET NOCOUNT ON
        DECLARE @HCC NVARCHAR(4000), @Encounter_ID INT, @n INT, @max INT, @snippet INT
        SELECT @n = 1
        SELECT @max = COUNT(*) FROM wellness_eCastEMR_Data.dbo.tempHCC
        WHILE @n <= @max  --- You are looping through multiple Encounter_IDs in tempHCC
          BEGIN
            SELECT @Encounter_ID = Encounter_ID FROM wellness_eCastEMR_Data.dbo.tempHCC
            WHERE tempHCC_ID = @n


            /* Experimental Code to get rid of duplicates
            --- Create a random table using the Encounter_ID as a name and drop it later.  Fill it up
            --- with the HCC values from Clinical Triggers but use DISTINCT on it to get rid of DUPs.
            --- Then Coalesce it into the variable @HCC and you'll write an ETL3 with that later.
            CREATE TABLE wellness_eCastEMR_Data.dbo.[@Encounter_ID]
            (HCC varchar(200))
            INSERT INTO wellness_eCastEMR_Data.dbo.[@Encounter_ID]
              SELECT DISTINCT RTRIM(ttt.TTICDCodes)
              FROM wellness_eCastEMR_Data.dbo.TabletTriggersData ttd
              JOIN wellness_eCastEMR_Data.dbo.TabletTriggers ttt
              ON ttd.TabletTriggers_ID = ttt.TabletTriggers_ID
            WHERE ttd.Encounter_ID = @Encounter_ID AND ttt.TTICDCodes <> ''
            --- Now do the Coalesce function into the variable @HCC
            SELECT @HCC = COALESCE(@HCC + ', ', '') + HCC
            FROM wellness_eCastEMR_Data.dbo.[@Encounter_ID]
            DROP TABLE wellness_eCastEMR_Data.dbo.[@Encounter_ID]
            */

            /*
            --- Current Code
            SELECT @HCC = COALESCE(@HCC + ', ', '') + RTRIM(ttt.TTICDCodes)
            FROM wellness_eCastEMR_Data.dbo.TabletTriggersData ttd
            JOIN wellness_eCastEMR_Data.dbo.TabletTriggers ttt
            ON ttd.TabletTriggers_ID = ttt.TabletTriggers_ID
            WHERE ttd.Encounter_ID = @Encounter_ID AND ttt.TTICDCodes <> ''


            /*
            SELECT COUNT(*)
            FROM wellness_eCastEMR_Data.dbo.TabletTriggersData ttd
            JOIN wellness_eCastEMR_Data.dbo.TabletTriggers ttt
            ON ttd.TabletTriggers_ID = ttt.TabletTriggers_ID
            WHERE ttd.Encounter_ID = 522760 AND ttt.TTPhysician <> ''
            */
            /*---03/05/2016 Test first to see if zero rows returned - then don't write the snippets
            SELECT @snippet = COUNT(*)
            FROM wellness_eCastEMR_Data.dbo.TabletTriggersData ttd
            JOIN wellness_eCastEMR_Data.dbo.TabletTriggers ttt
            ON ttd.TabletTriggers_ID = ttt.TabletTriggers_ID
            WHERE ttd.Encounter_ID = @Encounter_ID AND ttt.TTPhysician <> ''

            IF @snippet > 0
                BEGIN
                    --- Right here is where you want to add the Coalescence of your TTPhysician snippets
                    SELECT @HCC = @HCC + '<br><b>Recommended Procedures, Treatments and Preventive Services</b>'
                    SELECT @HCC = COALESCE(@HCC + '; ', '') + '<br> - ' + ttt.TTPhysician
                    FROM wellness_eCastEMR_Data.dbo.TabletTriggersData ttd
                    JOIN wellness_eCastEMR_Data.dbo.TabletTriggers ttt
                    ON ttd.TabletTriggers_ID = ttt.TabletTriggers_ID
                    WHERE ttd.Encounter_ID = @Encounter_ID AND ttt.TTPhysician <> ''
                END

            UPDATE wellness_eCastEMR_Data.dbo.tempHCC
            SET HCC = @HCC WHERE Encounter_ID = @Encounter_ID

            IF @HCC IS NOT NULL
              BEGIN
                ---!!!-- Insert your BASE ETL3 rows for Status = 'Y' only
                INSERT INTO Wellness_eCastEMR_Data.dbo.ETL3
                SELECT Encounter_ID,TML3_ID,GetDate()
                FROM Wellness_eCastEMR_Data.dbo.TabletInput
                WHERE Status = 'Y' AND Encounter_ID = @Encounter_ID

                ---!!!-- Insert your BASE ETL2 rows for Status = 'Y' only
                INSERT INTO Wellness_eCastEMR_Data.dbo.ETL2
                SELECT DISTINCT Encounter_ID, TML2_ID FROM Wellness_eCastEMR_Data.dbo.TabletInput
                WHERE Status = 'Y' AND Encounter_ID = @Encounter_ID
              END
            ---!!!-- Finally you need to set the STATUS flags in TabletInput for this Encounter_ID
            UPDATE Wellness_eCastEMR_Data.dbo.TabletInput
            SET Hidden = 1, Status = '9'
            WHERE Status = 'Y' and Encounter_ID = @Encounter_ID

            ---Now bump the counter and go to the next Encounter_ID and do this all over again
            SELECT @n = @n + 1
          END
        ";
        */

    foreach ($list_status_y->result() as $v) {
        $sql = " SELECT DISTINCT RTRIM(ttt.TTICDCodes) as TTICDCodes
              FROM $cros_db_data.dbo.TabletTriggersData ttd
              JOIN $cros_db_data.dbo.TabletTriggers ttt
              ON ttd.TabletTriggers_ID = ttt.TabletTriggers_ID
              WHERE ttd.Encounter_ID = $v->Encounter_ID
              AND ttt.TTICDCodes <> ''";
        $list_TTICDCodes = $this->query_select($sql);

        $hcc_TTICDCodes = array_map(function($val) {
            return $val['TTICDCodes'];
          }, $list_TTICDCodes->result_array());

        $hcc = $v->HCC;
        $hcc .= implode(', ', $hcc_TTICDCodes);
        $hcc .= '<br><b>Recommended Procedures, Treatments and Preventive Services</b>';

        $sql = "SELECT  COALESCE('' + '; ', '') + '<br> - ' + CONVERT(VARCHAR(4000), ttt.TTPhysician) as TTPhysician
                FROM $cros_db_data.dbo.TabletTriggersData ttd
                JOIN $cros_db_data.dbo.TabletTriggers ttt
                ON ttd.TabletTriggers_ID = ttt.TabletTriggers_ID
                WHERE ttd.Encounter_ID = $v->Encounter_ID AND CONVERT(VARCHAR, ttt.TTPhysician) <> ''";

        $list_TTPhysician = $this->query_select($sql);
        $hcc_TTPhysician = array_map(function($val) {
            return $val['TTPhysician'];
          }, $list_TTPhysician->result_array());

        $hcc .= implode(' ', $hcc_TTPhysician);

        if(!empty($hcc_TTICDCodes) || !empty($hcc_TTPhysician)){
          $sql= "  INSERT INTO $cros_db_data.dbo.ETL3
              SELECT Encounter_ID,TML3_ID,GetDate()
              FROM $cros_db_data.dbo.TabletInput
              WHERE Status = 'Y'
              AND Encounter_ID = $v->Encounter_ID";
          $this->query_commit($sql);

          $sql = " INSERT INTO $cros_db_data.dbo.ETL2
              SELECT DISTINCT Encounter_ID, TML2_ID
              FROM $cros_db_data.dbo.TabletInput
              WHERE Status = 'Y'
              AND Encounter_ID = $v->Encounter_ID";
          $this->query_commit($sql);

          $insert_y_value = array(
              'Encounter_ID' => $v->Encounter_ID,
              'TML3_ID' => $v->TML3_ID,
              'ETL3Input' => $hcc,
              'Redacted' => 0,
              'DateRedacted' => NULL
          );
          $this->ETL3InputModel->insert($insert_y_value);

          $sql = "UPDATE $cros_db_data.dbo.TabletInput
              SET Hidden = 1, Status = '9'
              WHERE Status = 'Y'
              and Encounter_ID = $v->Encounter_ID";
          $this->query_commit($sql);
        }


    }
    $list_status_y->free_result();


    //=====================
    //Step 9
    //=====================

    $sql = "UPDATE $cros_db_data.dbo.TabletInput
        SET Hidden = 1, Status = '9'
        WHERE Status = '8'
        AND Hidden = 0
        AND Encounter_ID = $encounter_id";
    $this->query_commit($sql);

    $sql = "DELETE e3 FROM $cros_db_data.dbo.ETL3 e3
        JOIN $cros_db_data.dbo.TabletInput ti
        ON e3.TML3_ID = ti.TML3_ID
        WHERE e3.Encounter_ID = ti.Encounter_ID
        AND ti.Status = 'X'
        AND ti.Encounter_ID = $encounter_id";
    $this->query_commit($sql);


  }

}
