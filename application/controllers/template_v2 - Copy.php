<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Template_v2 extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model('TheoConnectModel');
    $this->load->model('FreeModel');
    $this->load->model('PhqModel');
    $this->self = 'template2';
  }

  function start($id_encounter = NULL) {
    $this->current_user = $this->sessionlib->current_user();
    $encounter = $this->get_db_encounter_row($id_encounter);
    $data['encounter'] = $encounter;
    $data['Encounter_ID'] = $id_encounter;
    $data['id_org'] = $this->current_user->Org_Id;
    $data['partial'] = $this->self . "/template_index";
    $this->load->view('layout', $data);
  }

  private function get_db_encounter_row($encounterKey = null){
    $dt = $this->EncounterHistoryModel->patient_history('EncounterHistory.Encounter_ID', $encounterKey, array('EncounterHistory.Org_ID' => $this->current_user->Org_Id))->row();
    if(empty($dt)){
      $this->encounter_not_found('Encounter not found!');
    }elseif(!empty($dt->EncounterSignedOff) && $dt->EncounterSignedOff == 1 ){
      $this->encounter_not_found('Encounter is Locked!');
    }
    return $dt;
  }

  private function encounter_not_found($msg){
    $data['msg'] = $msg;
    $data['partial'] = "encounter/encounter_not_found";
    echo $this->load->view('layout', $data, TRUE);
    exit();
  }

  function generate_careplan($encounter_id = 0, $session_id = 0){

      $url = THEO_LINK."/api/session/generate-careplan-new/$session_id/en";

      try {
        $json = @file_get_contents($url);
        $obj = json_decode($json);
      } catch (\Exception $e) {
        echo 'operation failed';
      }

      if(!empty($obj->urlCarePlan)){
        redirect(URL_AWS_S3.$obj->urlCarePlan);
      }else{
        echo 'Something Wrong, PLease Try Again!';
      }

  }

  function change_tml1() {
    $this->check_user();
    $tml1 = (int) $this->input->post('tml1_ID');
    $encounter_id =  (int) $this->input->post('Encounter_ID');
    $encounter = $this->get_db_encounter_row($encounter_id);


    $check_tml1_theo = $this->check_theo_link($tml1, $encounter);
    $check_theo_connect = $check_tml1_theo['check_theo_connect'];
    $patient_vitals = $check_tml1_theo['patient_vitals'];


    $data['tml1'] = $tml1;
    $data['Encounter_ID'] = $encounter_id;
    $data['VideoPlay_ID'] = !empty($check_theo_connect->TheoVideoPlay_ID) ? $check_theo_connect->TheoVideoPlay_ID : 0;
    $data['Session_ID'] = !empty($check_theo_connect->TheoSession_ID) ? $check_theo_connect->TheoSession_ID : 0;
    $data['Account_ID'] = !empty($check_theo_connect->TheoAccount_ID) ? $check_theo_connect->TheoAccount_ID : 0;


    $tml2 = $this->load->view($this->self . "/tml2", $data, TRUE);
    $tml3 = $this->load->view($this->self . "/tml3", $data, TRUE);  

    $this->PhqModel->check_phq($tml1, $encounter_id);

    $ress = array(
      'tml2' => $tml2,
      'tml3' => $tml3, 
      'phq2_total' => $this->PhqModel->phq2($tml1, $encounter_id, TRUE),
      'theo_session_id' => $data['Session_ID'],
      'theo_patient_vitals' => $patient_vitals
    );

    $this->json_output($ress);
  }

  private function check_theo_link($tml1, $encounter){
    $tml1_dt = $this->Tml1Model->get_by_id($tml1)->row();
    $check_theo_connect = NULL;
    $patient_vitals = NULL;

    if(!empty($tml1_dt->TML1_Theo_Link) && $tml1_dt->TML1_Theo_Link == 1){
      $theo_connect_data = array(
        'TML1_ID' => $tml1,
        'Encounter_ID' => $encounter->Encounter_ID,
        'Org_ID' => $this->current_user->Org_Id
      );
      $check_theo_connect  = $this->TheoConnectModel->get_where($theo_connect_data)->row();

      if(
        !empty($check_theo_connect->TheoVideoPlay_ID)
        && !empty($check_theo_connect->TheoSession_ID)
        && !empty($check_theo_connect->TheoAccount_ID)
      ){
        //do nothing
      }else{
        if(!empty($tml1)){
          //input session theo
          $this->inputSessionTheo($encounter, $tml1);
          $check_theo_connect  = $this->TheoConnectModel->get_where($theo_connect_data)->row();
        }
      }
      $patient_get_vitals = $this->getTheoVitalsFirst($encounter, $tml1, $check_theo_connect);
      $patient_vitals = $this->inputTheoVitalsFirst($encounter, $tml1, $check_theo_connect);
    }


    return array(
      'check_theo_connect' => $check_theo_connect,
      'patient_vitals' => $patient_vitals
    );

  }

  private function getTheoVitalsFirst($encounter, $tml1, $check_theo_connect){

    if(!empty($check_theo_connect->TheoSession_ID)){

      $url = THEO_LINK.'/api/session/get-vitals/'.$check_theo_connect->TheoSession_ID;
      try {
        $json = @file_get_contents($url);
        $obj = json_decode($json);
      } catch (\Exception $e) {
      }

      $tmaster_vitals =  array(423 , 424 , 425 , 426 );

      $dt_vitals = array();
      $dt_vitals[] = !empty($obj->PatientVitals->height) ? $obj->PatientVitals->height : 0;
      $dt_vitals[] = !empty($obj->PatientVitals->weight) ? $obj->PatientVitals->weight : 0;
      $dt_vitals[] = !empty($obj->PatientVitals->systolic) ? $obj->PatientVitals->systolic : 0;
      $dt_vitals[] = !empty($obj->PatientVitals->diastolic) ? $obj->PatientVitals->diastolic : 0;

      $cros_db_data = $this->FreeModel->data_db()->database;
      $cros_db_template = $this->FreeModel->template_db()->database;

      foreach ($dt_vitals as $key => $v) {
        $key_vital =  $tmaster_vitals[$key];
        $theo_vital = $v;
        $this->FreeModel->data_db()->trans_begin();
        $sql = "UPDATE $cros_db_data.dbo.TabletInput set TML3_Value = '$theo_vital'
                where Encounter_ID = $encounter->Encounter_ID
                and TML1_ID = $tml1
                and TML3_ID in (
              			 select t3.TML3_ID
              			 from $cros_db_template.dbo.TML3 t3
              			 join $cros_db_template.dbo.TML2 t2 on  t2.TML2_ID = t3.TML2_ID
              			 where	t2.TML1_ID = $tml1
              					and t3.TML3_TBotMaster_ID = $key_vital
              					and t3.TypeInput = 'text_input'
              					and t3.SubTitle = 0
              					and (t3.Hidden = 0 OR t3.Hidden IS NULL) )";
        $this->FreeModel->data_db()->query($sql);
        $this->FreeModel->data_db()->trans_commit();

        
        $this->FreeModel->data_db()->trans_begin();
        $sql = "UPDATE $cros_db_data.dbo.ETL3Input set ETL3Input = '$theo_vital'
                where Encounter_ID = $encounter->Encounter_ID
                and TML3_ID in (
              			 select t3.TML3_ID
              			 from $cros_db_template.dbo.TML3 t3
              			 join $cros_db_template.dbo.TML2 t2 on  t2.TML2_ID = t3.TML2_ID
              			 where	t2.TML1_ID = $tml1
              					and t3.TML3_TBotMaster_ID = $key_vital
              					and t3.TypeInput = 'text_input'
              					and t3.SubTitle = 0
              					and (t3.Hidden = 0 OR t3.Hidden IS NULL) )";
        $this->FreeModel->data_db()->query($sql);
        $this->FreeModel->data_db()->trans_commit();


      }
    }
  }


  private function inputTheoVitalsFirst($encounter, $tml1, $check_theo_connect){
    $content = '';
    if(
      !empty($check_theo_connect->TheoVideoPlay_ID)
      && !empty($check_theo_connect->TheoSession_ID)
      && !empty($check_theo_connect->TheoAccount_ID)
    ){
      $session_id = $check_theo_connect->TheoSession_ID;
      $account_id = $check_theo_connect->TheoAccount_ID;
      $patient_id = !empty($encounter->Patient_ID) ? $encounter->Patient_ID : 0;
      $provider_id= !empty($encounter->Provider_ID) ? $encounter->Provider_ID : 0;

      $url = THEO_LINK."/api/session/vitals/manual/$session_id/$account_id/$patient_id/$provider_id";

      $template2 = $this->Tml2Model->get_by_field('TML1_ID', $tml1,  '(Hidden = 0 OR Hidden IS NULL)')->result();

      $tml2 = array(0);
      foreach($template2 as $v) {
        $tml2[] = $v->TML2_ID;
      }

      $tml2 = implode(',', $tml2);
      $tmaster_vitals = array(423 , 424 , 425 , 426 );

      $vitals_value = array();
      foreach ($tmaster_vitals as $tmaster_vital) {
        $con = "(Hidden = 0 OR Hidden IS NULL)
        AND TypeInput = 'text_input'
        AND SubTitle = 0
        AND TML2_ID in ($tml2) ";
        $tml_3_vitals = $this->Tml3Model->get_by_field('TML3_TBotMaster_ID', $tmaster_vital, $con)->row();
        $get_tml3 = !empty($tml_3_vitals->TML3_ID) ? $tml_3_vitals->TML3_ID : 0;
        $con = "(Status <> 'X' OR Status is null)
        AND TML3_ID = $get_tml3
        ";
        $tml_3_vital = $this->TabletInputModel->get_by_field('Encounter_ID', $encounter->Encounter_ID, $con)->row();
        $vitals_value[] =  !empty($tml_3_vital->TML3_Value) ? $tml_3_vital->TML3_Value : 0;
      }


      $height     = !empty($vitals_value[0]) ? $vitals_value[0]: 0;
      $weight     = !empty($vitals_value[1]) ? $vitals_value[1]: 0;
      $systolic   = !empty($vitals_value[2]) ? $vitals_value[2]: 0;
      $diastolic  = !empty($vitals_value[3]) ? $vitals_value[3]: 0;


      $patient_vitals = array(
        "password" =>  "",
        "languageIdentifier" => "en",
        "systolic" =>  (int)$systolic,
        "diastolic" =>  (int)$diastolic,
        "weight" => (int)$weight,
        "height" => (int)$height,
        "provider_id" => $provider_id,
      );

      $content    = json_encode($patient_vitals);

      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

      $json_response = curl_exec($curl);

      $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      curl_close($curl);

      $response = json_decode($json_response, true);

    }
    return $content;
  }

  private function inputSessionTheo($encounter, $tml1){
    $dt_patient = $this->PatientProfileModel->get_by_id($encounter->Patient_ID)->row();

    $url = THEO_LINK."/api/session/manual/";
    $content = json_encode(
        array(
          "patientDOBString" =>  date('Y-m-d', strtotime($dt_patient->DOB)),
          "patientIdentifier" => $dt_patient->MedicalRecordNumber,
          "latitude" =>  "-6.927040815359844",
          "longitude" =>  "107.5543816668052",
          "patientName" =>  $dt_patient->FirstName .' '. $dt_patient->LastName,
          "orgId" => $this->current_user->Org_Id,
          "userId" =>  $this->current_user->User_Id
        )
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    $response = json_decode($json_response, true);
    if(!empty($response['videoPlayId']) &&  !empty($response['sessionId']) && !empty($response['accountId'])){
      $video_play = $response['videoPlayId'];
      $session_id = $response['sessionId'];
      $account_id = $response['accountId'];

      $input_theo_connect = array(
        'TML1_ID' => $tml1,
        'Encounter_ID' => $encounter->Encounter_ID,
        'Patient_ID' => $encounter->Patient_ID,
        'User_ID' => $this->current_user->User_Id,
        'Org_ID' => $this->current_user->Org_Id,
        'TheoVideoPlay_ID' => $video_play,
        'TheoSession_ID' => $session_id,
        'TheoAccount_ID' => $account_id,
        'DateCreated' => date('Y-m-d H:i:s'),
      );

      $this->TheoConnectModel->insert($input_theo_connect);
    }
  }

  function change_tml2() {
    $this->check_user();
    $data['tml2_arr'] = $this->input->post('tml2_arr');
    $data['Encounter_ID'] = (int) $this->input->post('Encounter_ID');
    $data['tml1'] = (int) $this->input->post('tml1_ID');
    $tml3 = $this->load->view($this->self . "/tml3", $data, TRUE);
    $ress = array('tml3' => $tml3);
    $this->json_output($ress);
  }

  function save_tml3() {
    $this->check_user();
    $tml1_id = (int) $this->input->post('tml1_ID');
    $tml2_id = (int) $this->input->post('tml2_ID');
    $tml3_id = (int) $this->input->post('tml3_ID');
    $encounter_ID = (int) $this->input->post('Encounter_ID');

    $checked = (int) $this->input->post('checked');
    $tml3 = $this->Tml3Model->get_data_save(array('TML3_ID' => $tml3_id))->row();
    $phq9_total = 0;
    $phq2_total = 0;

    $theoanswer_ID = (int) $this->input->post('theoanswer_ID');
    $theoquestion_ID = (int) $this->input->post('theoquestion_ID');
    $theosession_ID = (int) $this->input->post('theosession_ID');
    $theovideoplay_ID = (int) $this->input->post('theovideoplay_ID');
    $theoaccount_ID = (int) $this->input->post('theoaccount_ID');

    $ress = array(
        'status' => 'true',
        'phq9_total' => 0,
        'phq2_total' => 0
    );
    if ($tml3 && $encounter_ID) {
      $dt_post = array(
          'Encounter_ID' => $encounter_ID,
          'TML1_ID' => $tml3->TML1_ID,
          'TML2_ID' => $tml3->TML2_ID,
          'TML3_ID' => $tml3->TML3_ID,
          'TML3_Value' => $this->input->post('input'),
          'Status' => ($checked == 1) ? NULL : 'X'
      );
      $TabletInput = $this->TabletInputModel->get_data($tml3->TML3_ID, $encounter_ID)->row();
      if ($TabletInput) {
        $this->TabletInputModel->update($TabletInput->TabletInput_ID, $dt_post);
      } else {
        $this->TabletInputModel->insert($dt_post);
      }

      if ($checked == 1 && $tml3->TypeInput == 'radio_btn' && !empty($tml3->RadioName)) {
        $sql = "TML3.TML3_ID NOT IN ($tml3->TML3_ID) AND TML3.TML2_ID = $tml3->TML2_ID AND TML3.RadioName = '$tml3->RadioName'";
        $tml3_radio_btn = $this->Tml3Model->get_data_save($sql)->result();
        foreach ($tml3_radio_btn as $rd) {
          $tb = $this->TabletInputModel->get_data($rd->TML3_ID, $encounter_ID)->row();
          if ($tb) {
            $this->TabletInputModel->update($tb->TabletInput_ID, array('Status' => 'X'));
            $this->ETL3Model->delete_where(array('Encounter_ID' => $encounter_ID, 'TML3_Id' => $tb->TML3_ID,));
            $this->ETL3InputModel->delete_where(array('Encounter_Id' => $encounter_ID, 'TML3_Id' => $tb->TML3_ID));
          }
        }
      }


      //etl
      $cek_tml = $this->ETLModel->get_by_field('Encounter_Id', $encounter_ID)->num_rows();
      if ($cek_tml == 0) {
        $dt_insert = array(
            'Encounter_Id' => $encounter_ID,
            'ETLSaved' => 1
        );
        $this->ETLModel->insert($dt_insert);
      }
	  
	  //etl1
      $cek_tml = $this->ETL1Model->get_by_field('Encounter_Id', $encounter_ID)->num_rows();
      if ($cek_tml == 0) {
        $dt_insert = array(
            'Encounter_Id' => $encounter_ID,
            'TML1_Id' => $tml1_id
        );
        $this->ETL1Model->insert($dt_insert);
      }

      //etl2
      $cek_tml2 = $this->ETL2Model->get_by_field('Encounter_Id', $encounter_ID, 'TML2_Id = ' . $tml3->TML2_ID)->num_rows();
      if ($cek_tml2 == 0) {
        $dt_insert = array(
            'Encounter_Id' => $dt_post['Encounter_ID'],
            'TML2_Id' => $tml3->TML2_ID
        );
        $this->ETL2Model->insert($dt_insert);
      }

      //etl3
      $cek_tml3 = $this->ETL3Model->get_by_field('Encounter_Id', $encounter_ID, 'TML3_Id = ' . $tml3->TML3_ID)->num_rows();
      $dt_insert = array(
          'Encounter_Id' => $encounter_ID,
          'TML3_Id' => $tml3->TML3_ID
      );
      if ($cek_tml3 == 0) {
        $this->ETL3Model->insert($dt_insert);
      } elseif ($checked != 1) {
        $this->ETL3Model->delete_where($dt_insert);
      }

      //etl3_input
      $cek_tml3_input = $this->ETL3InputModel->get_by_field('Encounter_Id', $encounter_ID, 'TML3_Id = ' . $tml3->TML3_ID);
      $dt_insert = array(
          'Encounter_Id' => $encounter_ID,
          'TML3_Id' => $tml3->TML3_ID,
          'ETL3Input' => $this->input->post('input')
      );
      if ($cek_tml3_input->num_rows() == 0) {
        $this->ETL3InputModel->insert($dt_insert);
      } else {
        $row = $cek_tml3_input->row();
        $this->ETL3InputModel->update_where(array('Encounter_Id' => $encounter_ID, 'TML3_Id' => $tml3->TML3_ID), array('ETL3Input' => $this->input->post('input')));
        if ($checked != 1) {
          $this->ETL3InputModel->delete_where(array('Encounter_Id' => $encounter_ID, 'TML3_Id' => $tml3->TML3_ID));
        }
      }

      //PHQ2 
      $phq2_ids = explode(',', $this->PhqModel->phq2_ids);
      if (in_array($tml3->TML3_TBotMaster_ID, $phq2_ids)) { 
        $this->PhqModel->phq2($tml1_id, $encounter_ID);
      } 
      $phq2_total = $this->PhqModel->phq2($tml1_id, $encounter_ID, TRUE);

      //PHQ9 PROCESS
      $phqr_ids_arr = explode(',', $this->PhqModel->phq9_ids);
      if (in_array($tml3->TML3_TBotMaster_ID, $phqr_ids_arr)) {
        $this->PhqModel->phq9($tml1_id, $encounter_ID);
      }
      $phq9_total =  $this->PhqModel->phq9($tml1_id, $encounter_ID, TRUE);

      $dt_patient = $this->EncounterHistoryModel->patient_history('EncounterHistory.Encounter_ID', $encounter_ID,array('EncounterHistory.Org_ID' => $this->current_user->Org_Id))->row();
      if(!empty($dt_patient->Patient_ID)){
        $this->PatientProfileModel->update($dt_patient->Patient_ID, array('LastAWVDate' => date('Y-m-d H:i:s')));
      }

      $data_theo = array(
        'theoanswer_ID' => $theoanswer_ID,
        'theoquestion_ID' => $theoquestion_ID,
        'theosession_ID' => $theosession_ID,
        'theovideoplay_ID' => $theovideoplay_ID,
        'theoaccount_ID' => $theoaccount_ID
      );

      $data_template = array(
        'tml1_id' => $tml1_id,
        'tml2_id' => $tml2_id,
        'tml3_id' => $tml3_id,
        'encounter_ID' => $encounter_ID,
        'checked' => $checked
      );

      //Theo push
      if(!empty($theoanswer_ID)
          && !empty($theoquestion_ID)
          && !empty($theosession_ID)
          && !empty($theovideoplay_ID)){

        $this->send_theo_answer($data_theo, $data_template, $tml3);
      }

      //Theo Vital Push
      $patient_vitals = array();
      $tmaster_vitals =  array(423 , 424 , 425 , 426 );
      if(in_array($tml3->TML3_TBotMaster_ID, $tmaster_vitals)
         && !empty($theosession_ID)
         && !empty($theoaccount_ID)
       ){
        $patient_vitals = $this->send_theo_viatals($data_theo, $data_template, $tml3);
      }



      $ress = array(
          'status' => 'true',
          'phq9_total' => (int)$phq9_total,
          'phq2_total' => (int)$phq2_total,
          'patient_vitals' => $patient_vitals
      );


    }


    $this->json_output($ress);
  }


  private function send_theo_viatals($data_theo, $data_template, $tml3){
    $session_id = $data_theo['theosession_ID'];
    $account_id = $data_theo['theoaccount_ID'];
    $encounter = $this->EncounterHistoryModel->get_by_id($data_template['encounter_ID'])->row();
    $patient_id = !empty($encounter->Patient_ID) ? $encounter->Patient_ID : 0;
    $provider_id= !empty($encounter->Provider_ID) ? $encounter->Provider_ID : 0;

    $url = THEO_LINK."/api/session/vitals/manual/$session_id/$account_id/$patient_id/$provider_id";


    $tmaster_vitals = array(423 , 424 , 425 , 426 );

    $vitals_value = array();
    foreach ($tmaster_vitals as $tmaster_vital) {
      $con = "(Hidden = 0 OR Hidden IS NULL)
              AND TypeInput = 'text_input'
              AND SubTitle = 0
              AND TML3_TBotMaster_ID = $tmaster_vital ";
      $tml_3_vitals = $this->Tml3Model->get_by_field('TML2_ID', $data_template['tml2_id'], $con)->row();
      $get_tml3 = !empty($tml_3_vitals->TML3_ID) ? $tml_3_vitals->TML3_ID : 0;
      $con = "(Status <> 'X' OR Status is null)
              AND TML3_ID = $get_tml3
              ";
      $tml_3_vital = $this->TabletInputModel->get_by_field('Encounter_ID', $data_template['encounter_ID'], $con)->row();
      $vitals_value[] =  !empty($tml_3_vital->TML3_Value) ? $tml_3_vital->TML3_Value : 0;
    }


    $height     = !empty($vitals_value[0]) ? $vitals_value[0]: 0;
    $weight     = !empty($vitals_value[1]) ? $vitals_value[1]: 0;
    $systolic   = !empty($vitals_value[2]) ? $vitals_value[2]: 0;
    $diastolic  = !empty($vitals_value[3]) ? $vitals_value[3]: 0;


    $patient_vitals = array(
      "password" =>  "",
      "languageIdentifier" => "en",
      "systolic" =>  (int)$systolic,
      "diastolic" =>  (int)$diastolic,
      "weight" => (int)$weight,
      "height" => (int)$height,
      "provider_id" => $provider_id,
    );

    $content    = json_encode($patient_vitals);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    $response = json_decode($json_response, true);

    return $content;

  }

  private function send_theo_answer($data_theo, $data_template, $tml3){

    $url = THEO_LINK."/api/answer/manual/".$data_theo['theovideoplay_ID'];

    $ids = array();
    if($tml3->TypeInput == "radio_btn"){
      $ids = array($data_theo['theoanswer_ID']);
    }elseif($tml3->TypeInput == "checkbox"){
      $con = "(Hidden = 0 OR Hidden IS NULL)
              AND TheoQuestion_ID = ".$data_theo['theoquestion_ID']."
              AND TypeInput = 'checkbox'
              AND SubTitle = 0
              AND (TheoAnswer_ID is not NULL OR TheoAnswer_ID > 0 )";
      $tml_3 = $this->Tml3Model->get_by_field('TML2_ID', $data_template['tml2_id'], $con)->result();

      $tml3_checkbo_ids = array();
      foreach ($tml_3 as $tm) {
        $tml3_checkbo_ids[] = $tm->TML3_ID;
      }
      $tml3_checkbo_ids = implode(',',$tml3_checkbo_ids);
      $tml3_checkbo_ids = !empty($tml3_checkbo_ids) ? $tml3_checkbo_ids : '0';

      $con = "(Status <> 'X' OR Status is null)
              AND TML3_ID IN ($tml3_checkbo_ids)
              AND (Hidden = 0 OR Hidden IS NULL)";
      $tml_3 = $this->TabletInputModel->get_by_field('Encounter_ID', $data_template['encounter_ID'], $con)->result();

      foreach ($tml_3 as $tm) {
        $row = $this->Tml3Model->get_by_id($tm->TML3_ID)->row();
        if(!empty($row->TheoAnswer_ID)){
          $ids[] = $row->TheoAnswer_ID;
        }
      }

    }

    $content = json_encode(
        array(
          "language" =>  "en",
          "answerIds" =>  $ids,
          "questionId" =>  $data_theo['theoquestion_ID'],
          "orgId" => $this->current_user->Org_Id,
        )
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

    $json_response = curl_exec($curl);

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    $response = json_decode($json_response, true);


  }

  private function check_user() {
    $this->current_user = $this->sessionlib->current_user_ajax();
    if ($this->current_user == FALSE) {
      $this->output->set_status_header(401, 'Session TimeOut!');
      exit();
    }
  }

  private function json_output($ress) {
    $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($ress));
  }

}
