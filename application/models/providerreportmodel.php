<?php
class ProviderReportModel extends CI_Model
{
  function __construct()
  {
    parent::__construct();
    $this->db = $this->load->database('data', true);
    $this->db_theo = $this->load->database('theo', true);
    $this->template_db = $this->load->database('template', true);
  }

  function get_session($session_id)
  {
    $session = $this->db->select('TOP 1 *')
      ->where(array('session_id' => $session_id))
      ->from('TheoResults')->get()->row();
    return $session;
  }

  function prepare_encounter($session, $tml1, $current_user)
  {

    $encounter = false;

    $theo_connect_data = array(
      'TML1_ID' => $tml1->TML1_ID,
      'TheoSession_ID' => $session->Session_ID,
      'Org_ID' => $session->Org_ID,
      'Patient_ID' => $session->Patient_ID
    );

    $theo_connect_data2 = array( 
      'TheoSession_ID' => $session->Session_ID,
      'Org_ID' => $session->Org_ID,
      'Patient_ID' => $session->Patient_ID
    );

    $check_theo_connect  = $this->TheoConnectModel->get_where($theo_connect_data, 'Encounter_ID is not null')->row();
    $check_theo_connect2  = $this->TheoConnectModel->get_where($theo_connect_data2, 'Encounter_ID is not null')->row();

    if ($check_theo_connect) {

      $encounter = $this->EncounterHistoryModel->get_by_id($check_theo_connect->Encounter_ID)->row();
      // echo '<hr>';
      // var_dump('get from theo connect');
      // var_dump($check_theo_connect);
      // var_dump($encounter);

    } elseif($check_theo_connect2){
      $encounter = $this->EncounterHistoryModel->get_by_id($check_theo_connect2->Encounter_ID)->row();
      $this->create_theo_connect($encounter, $session, $tml1, $current_user);
    } else {

      $get_encounter =  $this->create_appt($session, $current_user);
      $encounter = $this->EncounterHistoryModel->get_by_id($get_encounter)->row();
      if ($encounter) {
        $this->create_theo_connect($encounter, $session, $tml1, $current_user);
      }
      // echo '<hr>';
      // var_dump('get from encounter');
      // var_dump($encounter);
    }

    return $encounter;
  }



  function create_theo_connect($encounter, $session, $tml1, $current_user)
  {
    $en_date = $this->input->get('datestart');
    $input_theo_connect = array(
      'TML1_ID' => $tml1->TML1_ID,
      'Encounter_ID' => $encounter->Encounter_ID,
      'Patient_ID' => $encounter->Patient_ID,
      'User_ID' => $current_user->User_Id,
      'Org_ID' => $current_user->Org_Id,
      'TheoVideoPlay_ID' => null,
      'TheoSession_ID' => $session->Session_ID,
      'TheoAccount_ID' => $session->ID,
      'DateCreated' => date('Y-m-d H:i:s'), 
    );

    if(!empty($en_date)){
      $input_theo_connect['EncounterDate'] = $en_date; 
    }

    $this->TheoConnectModel->insert($input_theo_connect);
  }



  function create_appt($session, $current_user)
  {
    $params = $this->params_appt($session, $current_user);

    $this->AppointmentModel->insert($params);
    $id_Appointment = $this->AppointmentModel->get_last_insert();
    $this->update_local_time($id_Appointment);
    $id_encounter =  $this->process_encounter($id_Appointment);


    // echo '<hr>';
    // var_dump($params);
    // var_dump($id_encounter);
    return $id_encounter;
  }

  function params_appt($session, $current_user)
  {
    $patient_dt = $this->PatientProfileModel->get_by_field(
      'Patient_ID',
      $session->Patient_ID
    )->row();

    $provider_dt = $this->ProviderProfileModel->get_by_field(
      'Provider_ID',
      $session->Provider_ID
    )->row();

    $con = '(Hidden = 0 OR Hidden IS NULL) and Provider_ID = ' . $session->Provider_ID;
    $encounter = $this->EncounterDescriptionListModel->get_by_field('Org_Id', $current_user->Org_Id, $con)->row();

    $EncounterDescription_ID = !empty($encounter->EncounterDescription_ID) ? $encounter->EncounterDescription_ID : 0;
    
    $en_date = $this->input->get('datestart');
    $en_date = !empty($en_date) ? $en_date : $session->EncounterDate;
    
    $post = array(
      'ApptStart' =>  date("Y-m-d H:i:s", strtotime($en_date)),
      'ApptStop' => date("Y-m-d H:i:s", strtotime("+30 minutes", strtotime($en_date))),
      'Facility_ID' => null,
      'Notes' => 'Theo Visit',
      'PMSReason' => 'Theo Visit',
      'EncounterDescription_ID' => $EncounterDescription_ID,
      'Hidden' => 0,
      'Org_ID' => $current_user->Org_Id,
      'Dept_ID' => $current_user->Dept_Id,
      'User_ID' => $current_user->User_Id,
      'TOA' => 'WellTrackONE Visit',
      'Users_PK' => $current_user->ID,
    );

    if (!empty($patient_dt->Patient_ID)) {
      $patient_post = array(
        'Patient_ID' => $patient_dt->Patient_ID,
        'Patient_FName' => $patient_dt->FirstName,
        'Patient_LName' => $patient_dt->LastName,
        'Patient_DOB' => $patient_dt->DOB,
        'Patient_SSN' => $patient_dt->SSN,
        'Patient_MRN' => $patient_dt->MedicalRecordNumber,
      );
      $post = $post + $patient_post;
    }

    if (!empty($provider_dt->Provider_ID)) {
      $provider_post = array(
        'Provider_ID' => $provider_dt->Provider_ID,
        'Provider_FName' => $provider_dt->ProviderFirstName,
        'Provider_LName' => $provider_dt->ProviderLastName,
        'Provider_UPIN' => $provider_dt->ProviderUPIN,
        'Provider_Number' => $provider_dt->PMS_Pkey,
      );
      $post = $post + $provider_post;
    }

    return $post;
  }


  function update_local_time($id)
  {
    $appt = $this->AppointmentModel->get_by_id($id)->row();
    $data_db = $this->db->database;
    $org_id = $appt->Org_ID;
    $org_details = $this->OrgProfileModel->get_by_field('Org_ID', $org_id)->row();
    $get_time_zone = $this->TimeZoneModel->get_by_id($org_details->TimeZone_ID)->row();

    $sql = "Update $data_db.dbo.Appointments
    SET ApptStart_UTC=$data_db.dbo.LocaltoUTC('$appt->ApptStart', $get_time_zone->TzOffsetStandard_num, $org_details->DST),
      ApptStop_UTC= $data_db.dbo.LocaltoUTC('$appt->ApptStop', $get_time_zone->TzOffsetStandard_num, $org_details->DST)
    where Appointments_Id = $id";
    $this->db->trans_begin();
    $this->db->query($sql);
    $this->db->trans_commit();
  }


  function process_encounter($id_appt)
  {
    $id_encounter = 0;
    $appt = $this->AppointmentModel->get_by_id($id_appt)->row();
    if ($appt) {
      $dt_ecounter = array(
        'EncounterDate' => date('Y-m-d', strtotime($appt->ApptStart)),
        'Provider_ID' => $appt->Provider_ID,
        'Patient_ID' => $appt->Patient_ID,
        'EncounterDescription' => $appt->Notes,
        'ChiefComplaint' => $appt->Notes,
        'EncounterDescription_ID' => $appt->EncounterDescription_ID,
        'EncounterSignedOff' => 0,
        'EncounterNotes' => $appt->Notes,
        'EncounterPrinted' => 0,
        'Facility_ID' => $appt->Facility_ID,
        'Org_ID' => $appt->Org_ID,
        'Dept_ID' => $appt->Dept_ID,
        'User_ID' => $appt->User_ID,
        'Hidden' => 0,
        'Appointments_ID' => $id_appt,
        'Users_PK' =>  $appt->Users_PK,
        'ClinicalTriggerView' => null,
      );

      $this->EncounterHistoryModel->insert($dt_ecounter);
      $id_encounter = $this->EncounterHistoryModel->get_last_insert();
    }
    return $id_encounter;
  }




  function bulk_template($tml1_id, $encounter_id, $session_id)
  {

    //etl
    $cek_tml = $this->ETLModel->get_by_field('Encounter_Id', $encounter_id)->num_rows();
    if ($cek_tml == 0) {
      $dt_insert = array(
        'Encounter_Id' => $encounter_id,
        'ETLSaved' => 1
      );
      $this->ETLModel->insert($dt_insert);
    }

    //preselect template ETL3
    $sql1 = "INSERT INTO Wellness_eCastEMR_Data.dbo.ETL3 (Encounter_Id, TML3_Id)
    select $encounter_id as Encounter_Id, t3.TML3_ID 
    from TML3 as t3
    join TML2 as t2 on t2.TML2_ID = t3.TML2_ID
    join TML1 as t1 on t1.TML1_ID = t2.TML1_ID
    where t1.TML1_ID = $tml1_id and t3.PreSelected = 1 
    and TypeInput <> 'radio_btn' 
    and (t3.Hidden = 0 OR t3.Hidden IS NULL)
    and t3.TML3_ID not in (select et3.TML3_Id from Wellness_eCastEMR_Data.dbo.ETL3 as et3 where et3.Encounter_ID = $encounter_id)";
    $this->commit($sql1);

    //preselect template ETL3Input
    $sql2 = "INSERT INTO Wellness_eCastEMR_Data.dbo.ETL3Input (Encounter_Id, TML3_Id)
    select $encounter_id as Encounter_Id, t3.TML3_ID 
    from TML3 as t3
    join TML2 as t2 on t2.TML2_ID = t3.TML2_ID
    join TML1 as t1 on t1.TML1_ID = t2.TML1_ID
    where t1.TML1_ID = $tml1_id and t3.PreSelected = 1 
    and TypeInput <> 'radio_btn' 
    and (t3.Hidden = 0 OR t3.Hidden IS NULL)
    and t3.TML3_ID not in (select et3.TML3_Id from Wellness_eCastEMR_Data.dbo.ETL3Input as et3 where et3.Encounter_ID = $encounter_id)";
    $this->commit($sql2);

    //preselect template TabletInput
    $sql3 = "INSERT INTO Wellness_eCastEMR_Data.dbo.TabletInput(Encounter_Id, TML1_ID, TML2_ID, TML3_ID)
    select $encounter_id as Encounter_Id,  t2.TML1_ID, t3.TML2_ID, t3.TML3_ID 
    from TML3 as t3
    join TML2 as t2 on t2.TML2_ID = t3.TML2_ID
    join TML1 as t1 on t1.TML1_ID = t2.TML1_ID
    where t1.TML1_ID = $tml1_id and t3.PreSelected = 1 
    and TypeInput <> 'radio_btn' 
    and (t3.Hidden = 0 OR t3.Hidden IS NULL)
    and t3.TML3_ID not in (select et3.TML3_ID from Wellness_eCastEMR_Data.dbo.TabletInput as et3 where et3.Encounter_ID = $encounter_id)";
    $this->commit($sql3);


    //theoresult template TabletInput
    $sql4 = "INSERT INTO Wellness_eCastEMR_Data.dbo.TabletInput(Encounter_Id, TML1_ID, TML2_ID, TML3_ID) 
    select $encounter_id as Encounter_ID, t2.TML1_ID, t3.TML2_ID, t3.TML3_ID
    from Wellness_eCastEMR_Data.dbo.TheoResults as th
    join TML3 as t3 on t3.TheoQuestion_ID = th.Question_ID and t3.TheoAnswer_ID = th.Answer_ID
    join TML2 as t2 on t2.TML2_ID = t3.TML2_ID
    where t2.TML1_ID = $tml1_id 
    and th.Session_ID = $session_id 
    and  t3.TheoAnswer_ID <> 0
    and t3.TML3_ID not in (select et3.TML3_ID from Wellness_eCastEMR_Data.dbo.TabletInput as et3 where et3.Encounter_ID = $encounter_id)
    order by th.Question_ID asc ";
    $this->commit($sql4);


    //theoresult template etl2
    $sql5 = " INSERT INTO Wellness_eCastEMR_Data.dbo.ETL2(Encounter_Id, TML2_ID) 
    select $encounter_id as Encounter_ID,  t3.TML2_ID
    from Wellness_eCastEMR_Data.dbo.TheoResults as th
    join TML3 as t3 on t3.TheoQuestion_ID = th.Question_ID and t3.TheoAnswer_ID = th.Answer_ID
    join TML2 as t2 on t2.TML2_ID = t3.TML2_ID
    where t2.TML1_ID = $tml1_id 
    and th.Session_ID = $session_id 
    and  t3.TheoAnswer_ID <> 0
    and t3.TML2_ID not in (select et2.TML2_ID from Wellness_eCastEMR_Data.dbo.ETL2 as et2 where et2.Encounter_ID = $encounter_id)
    order by th.Question_ID asc ";
    $this->commit($sql5);

    //theoresult template etl3 
    $sql6 = " INSERT INTO Wellness_eCastEMR_Data.dbo.ETL3(Encounter_Id, TML3_ID) 
    select $encounter_id as Encounter_ID,  t3.TML3_ID
    from Wellness_eCastEMR_Data.dbo.TheoResults as th
    join TML3 as t3 on t3.TheoQuestion_ID = th.Question_ID and t3.TheoAnswer_ID = th.Answer_ID
    join TML2 as t2 on t2.TML2_ID = t3.TML2_ID
    where t2.TML1_ID = $tml1_id 
    and th.Session_ID = $session_id 
    and  t3.TheoAnswer_ID <> 0
    and t3.TML3_ID not in (select et3.TML3_ID from Wellness_eCastEMR_Data.dbo.ETL3 as et3 where et3.Encounter_ID = $encounter_id)
    order by th.Question_ID asc ";
    $this->commit($sql6);



    //theoresult template etl3_input 
    $sql7 = "INSERT INTO Wellness_eCastEMR_Data.dbo.ETL3Input(Encounter_Id, TML3_ID) 
    select $encounter_id as Encounter_ID,  t3.TML3_ID
    from Wellness_eCastEMR_Data.dbo.TheoResults as th
    join TML3 as t3 on t3.TheoQuestion_ID = th.Question_ID and t3.TheoAnswer_ID = th.Answer_ID
    join TML2 as t2 on t2.TML2_ID = t3.TML2_ID
    where t2.TML1_ID = $tml1_id 
    and th.Session_ID = $session_id 
    and  t3.TheoAnswer_ID <> 0
    and t3.TML3_ID not in (select et3.TML3_ID from Wellness_eCastEMR_Data.dbo.ETL3Input as et3 where et3.Encounter_ID = $encounter_id)
    order by th.Question_ID asc ";
    $this->commit($sql7);




    //vital
    $vital = $this->TheoVitalsModel->select_db()->where('Session_ID', $session_id)->get()->row();
    // echo '<hr>';
    // var_dump($vital);
    if ($vital) {

      $p_vitals = array(
        423 => $vital->Height,
        424 => $vital->Weight,
        425 => $vital->Systolic,
        426 => $vital->Diastolic,
      );

      $get_vitals = $this->Tml3Model->get_data_save(
        "TML3_TBotMaster_ID IN (423 , 424 , 425 , 426) 
          and TML2.TML1_ID = $tml1_id "
      )->result();
      foreach ($get_vitals as $v) {
        // echo '<hr>';
        $dt_post = array(
          'Encounter_ID' => $encounter_id,
          'TML1_ID' => $v->TML1_ID,
          'TML2_ID' => $v->TML2_ID,
          'TML3_ID' => $v->TML3_ID,
          'TML3_Value' => $p_vitals[$v->TML3_TBotMaster_ID],
          'Status' => null
        );
        $TabletInput = $this->TabletInputModel->get_data($v->TML3_ID, $encounter_id)->row();
        if ($TabletInput) {
          $this->TabletInputModel->update($TabletInput->TabletInput_ID, $dt_post);
        } else {
          $this->TabletInputModel->insert($dt_post);
        } 
        $cek_tml3 = $this->ETL3Model->get_by_field('Encounter_Id', $encounter_id, 'TML3_Id = ' . $v->TML3_ID)->num_rows();
        $dt_insert = array(
          'Encounter_Id' => $encounter_id,
          'TML3_Id' => $v->TML3_ID
        );
        if ($cek_tml3 == 0) {
          $this->ETL3Model->insert($dt_insert);
        } 

        //etl3_input
        $cek_tml3_input = $this->ETL3InputModel->get_by_field('Encounter_Id', $encounter_id, 'TML3_Id = ' . $v->TML3_ID);
        $dt_insert = array(
          'Encounter_Id' => $encounter_id,
          'TML3_Id' => $v->TML3_ID,
          'ETL3Input' => $this->input->post('input')
        );
        if ($cek_tml3_input->num_rows() == 0) {
          $this->ETL3InputModel->insert($dt_insert);
        } else {
          $row = $cek_tml3_input->row();
          $this->ETL3InputModel->update_where(array('Encounter_Id' => $encounter_id, 'TML3_Id' => $v->TML3_ID), array('ETL3Input' => $p_vitals[$v->TML3_TBotMaster_ID]));
          
        }


      }
    }
  }

  function commit($sql)
  {
    $this->template_db->trans_begin();
    $this->template_db->query($sql);
    $this->template_db->trans_commit();
  }


  function current_theo_count($tml1, $session, $Encounter_ID){
    $theo_connect_data = array(
      'TML1_ID' => $tml1->TML1_ID,
      'TheoSession_ID' => $session->Session_ID,
      'Org_ID' => $session->Org_ID,
      'Patient_ID' => $session->Patient_ID,
      'Encounter_ID' => $Encounter_ID
    );

    $check  = $this->TheoConnectModel->get_where($theo_connect_data)->row();
    return !empty($check->TheoResultCount) ? (int) $check->TheoResultCount : 0; 
  }

  function theo_count($session){
    return (int) $this->db
      ->where(array('session_id' => $session->Session_ID))
      ->from('TheoResults')->get()->num_rows();
  }

  function update_theo_count($tml1, $session, $Encounter_ID, $count){
    $theo_connect_data = array(
      'TML1_ID' => $tml1->TML1_ID,
      'TheoSession_ID' => $session->Session_ID,
      'Org_ID' => $session->Org_ID,
      'Patient_ID' => $session->Patient_ID,
      'Encounter_ID' => $Encounter_ID
    ); 
    $check  = $this->TheoConnectModel->get_where($theo_connect_data)->row(); 
    if($check){
      $this->TheoConnectModel->update($check->TheoConnect_ID, array('TheoResultCount' => $count ));
    }

  }
}
