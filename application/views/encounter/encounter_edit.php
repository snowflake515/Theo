<div class="page-header">
  <h1>
    Encounters
  </h1>
</div>

<?php
$check_review = 'form-encounter';
if ($dt && $dt->ClinicalTriggerView == 1) {
  $check_review = NULL;
}
?>
<div class="form-horizontal <?php echo $check_review ?>" >
  <?php echo form_open()?>
    <?php $this->load->view('encounter/encounter_form'); ?>
    <div class="form-group">
      <div class="col-sm-6">
        <p>
          <a href="<?php echo site_url('patient')?>" class="btn btn-default btn-sm"><i class="icon icon-arrow-left"></i>&nbsp;&nbsp;  Back</a>
          <a class="btn save-encounter btn-success btn-sm <?php echo ($dt->EncounterSignedOff == 1) ? 'disabled' : ''?>" onclick="saveEnncounterAjax(); document.getElementById('select_template_form').submit();" ><i class="icon icon-file"></i>&nbsp;&nbsp;  Templates</a>
          <button type="submit" name="submit" value="save" class="btn btn-primary btn-sm" <?php echo disabled_ecnounter($dt->EncounterSignedOff)?>><i class="icon icon-save"></i>&nbsp;&nbsp;  Save</button>
        

          <?php
          //echo anchor('clinical_trigger/patient/' . $this->EncounterHistoryModel->encript($dt->Encounter_ID), ' Clinical Triggers', array('class' => 'btn btn-danger', 'id' => 'clinical_triggers', 'data-id' => $this->EncounterHistoryModel->encript($dt->Encounter_ID)));
          ?>
        </p>
      </div>
      <div class="col-sm-6  " style="text-align: right;">
        <?php if ($this->mylib->only_supper_admin() && $dt->EncounterSignedOff == 1) : ?>
          <button type="submit" name="submit" value="unlock" class="btn btn-sm btn-primary"><i class="icon icon-unlock"></i>&nbsp;&nbsp;  Unlock</button>
        <?php endif;?>
        <button type="button" class="btn btn-default btn-sm no-openalert" href="<?php echo site_url("encounter/generate_report/$dt->Encounter_ID")?>" id="generate-en-report"> Generate Report</button>
        <div class="btn-group dropup " style="display:inline-block;">
          <a href="javascript:void(0);" style=" padding: 6px 10px;" class="btn btn-default btn-sm no-openalert" data-toggle="dropdown">
            View Report &nbsp;<i class="icon-caret-down"></i>
          </a>
          <ul class="dropdown-menu text-left" role="menu">
            <li>
              <a  href="javascript:void(0);"  class="popup no-openalert" data-target="<?php echo site_url("encounter/report/provider/$dt->Encounter_ID")?>">Provider Report</a>
            </li>
            <li>
              <a  onclick="alert('This is a new feature that is coming soon!')" target="_blank"  class=" no-openalert" >Clinical Track</a>
            </li>
          </ul>
        </div>
        <div class="btn-group dropup text-left" style="display:inline-block;">
          <a href="javascript:void(0);" style=" padding: 6px 10px;" class="btn btn-default btn-sm no-openalert" data-toggle="dropdown">Save Report &nbsp;<i class="icon-caret-down"></i></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="<?php echo  site_url('encounter/save_report/provider/' . $dt->Encounter_ID) ?>" target="_blank" class=" no-openalert" >Save Provider Report</a></li>
            <li><a href="<?php echo site_url('encounter/save_report/patient/' . $dt->Encounter_ID) ?>" target="_blank" class=" no-openalert" >Save Patient Report</a></li>
            <li><a onclick="alert('This is a new feature that is coming soon!')" target="_blank" class=" no-openalert" >Save Clinical Track</a></li>
          </ul>
        </div>
        <div class="btn-group dropup text-left" style="display:inline-block;">
          <a href="javascript:void(0);" style=" padding: 6px 10px;" class="btn btn-default btn-sm no-openalert" data-toggle="dropdown">
            Print Report &nbsp;<i class="icon-caret-down"></i>
          </a>
          <ul class="dropdown-menu" role="menu" style="left:auto; right:0">
            <li>
              <a href="javascript:void(0);"  class="popup no-openalert" data-target="<?php echo site_url("encounter/report/provider/$dt->Encounter_ID/print")?>">View Provider Report</a>
            </li>
            <?php echo  ($sm) ? '<li><a href="javascript:void(0);"  class="popup no-openalert" data-target="' . site_url("encounter/report/summary/$dt->Encounter_ID/print") . '">View Summary Report </a></li>' : NULL;?>
            <li>
              <a href="javascript:void(0);"  class="popup no-openalert" data-target="<?php echo site_url("encounter/report/patient/$dt->Encounter_ID/print/1")?>">Print Patient Report</a>
            </li>
            <li>
              <a onclick="alert('This is a new feature that is coming soon!')">Print Clinical Track</a>
            </li>
          </ul>
        </div>



        <?php
        // echo anchor("encounter/generate_report/$dt->Encounter_ID", 'Generate Report', array('class' => 'btn btn-default no-openalert ', 'id' => 'generate-en-report'));
        //
        // $link_view_report = '<a href="javascript:void(0);" class="popup btn btn-default no-openalert"
        //  title="View Report"
        // data-target="' . site_url("encounter/report/$dt->Encounter_ID") . '" >View Report</a>';
        //
        //
        // $sumarry_report_link = ($sm) ? '<li><a href="javascript:void(0);"  class="popup no-openalert" data-target="' . site_url("encounter/summary_report/$dt->Encounter_ID/print") . '">View Summary Report </a></li>' : NULL;
        //
        // $link_print_report = '<div class="btn-group " style="display:inline-block;">
        //       <a href="javascript:void(0);" style=" padding: 8px 10px;" class="btn btn-default no-openalert" data-toggle="dropdown">Print Report &nbsp;<i class="icon-caret-down"></i></a>
        //       <ul class="dropdown-menu" role="menu" style="top:-91px">
        //         <li><a href="javascript:void(0);"  class="popup no-openalert" data-target="' . site_url("encounter/report/$dt->Encounter_ID/print") . '">View Provider Report</a></li>
        //         ' . $sumarry_report_link . '
        //         <li><a href="javascript:void(0);"  class="popup no-openalert" data-target="' . site_url("encounter/report/$dt->Encounter_ID/print/1") . '">Print Patient Report</a></li>
        //       </ul>
        //     </div>';
        //
        // $sumarry_report_link = ($sm) ? '<li><a href="' . site_url('encounter/download_summary_report/' . $dt->Encounter_ID) . '" target="_blank" class=" no-openalert" >Save Summary Report </a></li>' : NULL;
        // $css_sm = ($sm) ? '-91px' : '-65px';
        // $link_save_report = '<div class="btn-group " style="display:inline-block;">
        //       <a href="javascript:void(0);" style=" padding: 8px 10px;" class="btn btn-default no-openalert" data-toggle="dropdown">Save Report &nbsp;<i class="icon-caret-down"></i></a>
        //       <ul class="dropdown-menu" role="menu" style="top:' . $css_sm . '">
        //         <li><a href="' . site_url('encounter/save_pdf/' . $dt->Encounter_ID) . '" target="_blank" class=" no-openalert" data-target="' . site_url('encounter/save_pdf/' . $dt->Encounter_ID) . '">Save Provider Report</a></li>
        //         ' . $sumarry_report_link . '
        //         <li><a href="' . site_url('encounter/download_patient_report/' . $dt->Encounter_ID) . '" target="_blank" class=" no-openalert" data-target="' . site_url('encounter/download_patient_report/' . $dt->Encounter_ID) . '">Save Patient Report</a></li>
        //       </ul>
        //     </div>';
        //
        // $link_unlock_report = NULL;
        // if ($this->mylib->only_supper_admin()) {
        //   $link_unlock_report = '<a href="' . site_url('encounter/unlock/' . $dt->Encounter_ID) . '" class="btn btn-primary no-openalert"
        //  title="Unlock Report"  ><i class="icon icon-unlock"></i>&nbsp; Unlock</a>';
        // }
        // echo $link_unlock_report;
        // echo $link_view_report;
        // echo $link_save_report;
        // //echo anchor("encounter/save_pdf/$dt->Encounter_ID", 'Save Report', array('class' => 'btn btn-default no-openalert'));
        // echo $link_print_report;
        ?>
      </div>
    </div>
  <?php echo form_close()?>
</div>

<?php $this->load->view('encounter/modal_patient')?>
