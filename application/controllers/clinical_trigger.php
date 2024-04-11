<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Clinical_trigger extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->current_user = $this->sessionlib->current_user();
    $this->self = $this->router->fetch_class();
  }

  public function encounter($encounter_id = 0, $print = NULL) {
    $ecounter = $this->get_db_encounter_row($encounter_id);

    $data['err'] = NULL;
    $data['patient'] = NULL;

    //update view report
    $data_up = array('ClinicalTriggerView' => 1);
    $this->EncounterHistoryModel->update($ecounter->Encounter_ID, $data_up);


    $data['patient'] = $this->PatientProfileModel->get_by_id($ecounter->Patient_ID)->row();
    $data['ecounter'] = $ecounter;


    //get trigger master
    $trigger_master = $this->TabletTriggersModel->get_by_data($this->current_user->Org_Id, $ecounter->Provider_ID);
    if ($trigger_master->num_rows() > 0) {
      $trigger_master_result = $trigger_master->result();
    } else {
      $trigger_master = $this->TabletTriggersModel->get_by_data($this->current_user->Org_Id, 0);
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
      $data['err'] = NULL;
    }


    $data['clinical_trigger'] = NULL;
    if ($data['err'] == NULL) {
      $data['clinical_trigger'] = $this->TabletTriggersDataModel->show_trigger_data($data['patient'], $ecounter);
    }

    $data['partial'] = $this->self . "/show_clinical_trigger";
    if ($print == "print") {
      $html = $this->opening_report_html();
      if ($data['patient']) {
        $html.='<h2 class="cts_for">Clinical Triggers for ' . $data['patient']->LastName . ', ' . $data['patient']->FirstName . '</h2>';
      }
      $html.= $this->load->view($this->self . "/loop_trigger", $data, TRUE);
      $html.="</div>";
      $html .= '</body></html> ';

      //echo $html;
      $url_html = "./reports/cts/" . $encounter_id . '.html';
      file_put_contents($url_html, $html);
      $this->save_as_pdf($encounter_id);
    } else {
      $this->load->view('layout', $data);
    }
  }

  private function get_db_encounter_row($encounterKey = null){
    $dt = $this->EncounterHistoryModel->select_db()
    ->where('Org_ID', $this->current_user->Org_Id)
    ->where($this->EncounterHistoryModel->key, (int)$encounterKey)
    ->get()->row();
    if(empty($dt)){
      $this->encounter_not_found();
    }
    return $dt;
  }

  private function encounter_not_found(){
    $data['msg'] = 'Encounter not found!';
    $data['partial'] = "encounter/encounter_not_found";
    echo $this->load->view('layout', $data, TRUE);
    exit();
  }

  private function opening_report_html() {
    $html = '<html lang="en"><head><meta charset="utf-8" /> <link href="' . base_url() . 'assets/ace/css/bootstrap.min.css" rel="stylesheet" />
              </head><body>';
    $html .= '<div style="width: 800px; margin: 40px auto;"> ';
    $html.= '<style>

            /*new cts helper*/
            .cts_for{
              margin-bottom: 20px;
              padding-top: 30px;
            }
            .ctshelper2 {
              font-family: "Century Gothic", Tahoma, Geneva, Verdana, sans-serif;
            }

            .ctshelper2 .subtitle {
              font-size: 21px;
              color: rgb(0,102,166) !important;
              text-decoration: underline;
              background-color: rgb(255,255,255);
            }

            .ctshelper2 a:link {
              color: rgb(33,64,154);
              text-decoration: underline;
            }

            .ctshelper2 a:visited {
              color: rgb(33,64,154);
              text-decoration: underline;
            }

            .ctshelper2 a:hover {
              color: rgb(255,255,255);
              background-color: rgb(33,64,154);
            }

            .ctshelper2 a:active {
              color: rgb(33,64,154);
              text-decoration: underline;
            }

            .ctshelper2 .wt1-box {
              border: 1px solid rgb(0,0,0);
              padding: 10px;
              font-family: "Century Gothic" !important;
              font-size: 16px !important;
              color: rgb(0,0,0);
            }

            .ctshelper2 .title_line > span {
              background-color: rgb(171,186,195);
              color: rgb(255,255,255);
              border: none !important;
              padding: 5px !important;
              font-size: 24px !important;
              font-weight: normal !important;
              font-family: "Century Gothic" !important;
            }
            </style>';

    return $html;
  }

  private function save_as_pdf($encounter_id) {
    $url_html = "./reports/cts/" . $encounter_id . '.html';
    $url_pdf = "./reports/cts/" . $encounter_id . '.pdf';
    $Encounter_dt = $this->EncounterHistoryModel->get_by_id($encounter_id)->row();
    $data_db = $this->load->database('data', TRUE)->database;
    $sql = "Select TOP 1
       AccountNumber,
       FirstName+' '+MiddleName+' '+LastName AS PatientFullName
        From " . $data_db . ".dbo.PatientProfile
       Where Patient_Id = $Encounter_dt->Patient_ID  ";
    $PatientHeader = $this->ReportModel->data_db->query($sql)->row();

    $this->load->helper('download');
    $exe = realpath('./assets/wkhtmltopdf/bin/wkhtmltopdf.exe');
    $temp_ex = explode('/', $exe);
    $exe_url = "";
    foreach ($temp_ex as $temp_dt) {
      if (strpos($temp_dt, " ") !== false) {
        $temp_dt = '"' . $temp_dt . '"';
      }
      $exe_url = $exe_url . $temp_dt;
    }

    //header

    // $html_tmp = 'Patient Name: ' . $string = preg_replace('/\s/', ' ', $PatientHeader->PatientFullName) ;
    //
    // $url_header = "./reports/footer/name_" . $Encounter_dt->Patient_ID . ".html";
    // file_put_contents($url_header, $html_tmp, LOCK_EX);

    $out = shell_exec($exe_url . ' --margin-top 15mm  --header-font-size 10 --header-right "Patient Name: '.preg_replace('!\s+!', ' ', ucwords($PatientHeader->PatientFullName)).'" '. $url_html . ' ' . $url_pdf . ' 2>&1');

    if (file_exists($url_pdf)) {
      $pdf = file_get_contents($url_pdf);
      force_download('[' . SERVER_APP . ']-[' . ucwords($PatientHeader->PatientFullName) . ']-CTs Report.pdf', $pdf);
    } else {
      //echo "Please Generate Report First!";
    }
  }

  private function insert_trgger_data($ecounter, $trigger) {
    $data_inst = array(
        'Org_ID' => $this->current_user->Org_Id,
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
      $check = $check->row();
      // $this->TabletTriggersDataModel->update($check->TabletTriggersData_ID, $data_inst);
    } else {
      $this->TabletTriggersDataModel->insert($data_inst);

      //log
      $id = $this->TabletTriggersDataModel->get_last_insert();
      $patient_id = ($ecounter) ? $ecounter->Patient_ID : 0;
      $ApplicationSpecificText = "Insert CTs";
      $this->mylib->action_audit_log($ApplicationSpecificText, "CTS", "A", $id, $patient_id);
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

  private function print_this($html = NULL) {
    require_once(APPPATH . 'third_party/html2pdf/html2pdf.class.php');
    $tidy = tidy_parse_string($html);
    $html = $tidy->html();
    try {
      $html2pdf = new HTML2PDF('P', 'A4', 'en');
      $html2pdf->writeHTML($html->value);
      $html2pdf->Output('clinical-trigger.pdf', 'D');
    } catch (HTML2PDF_exception $e) {
      echo $e;
      exit;
    }
  }

}
