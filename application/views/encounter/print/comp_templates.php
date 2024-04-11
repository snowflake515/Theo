<?php
$sql = "Select TOP 1
       ETLSaved
  From ETL
 Where Encounter_ID = $PrimaryKey";
$ETLProcess = $this->ReportModel->data_db->query($sql);
$ETLProcess_num = $ETLProcess->num_rows();
$ETLProcess_row = $ETLProcess->row();

if ($ETLProcess_num != 0 && $ETLProcess_row->ETLSaved == 1) {
  $this->load->view('encounter/print/template_pt1');
}
