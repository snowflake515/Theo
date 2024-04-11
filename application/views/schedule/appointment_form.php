<?php
$con = '(Hidden = 0 OR Hidden IS NULL)';
$encounter = $this->EncounterDescriptionListModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con);
$provider = $this->ProviderProfileModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con);
$facility = $this->FacilityListModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con);
$checkin_code = $this->CheckInCodeModel->get_by_field('Org_Id', $this->current_user->Org_Id)->result();

$get_last_check = NULL;
if(!empty($dt->Appointments_ID)){
  $get_last_check = $this->CheckInLogModel->get_last_check($dt->Appointments_ID)->row();
  $get_last_check = (!empty($get_last_check->CodeOrder)) ? $get_last_check->CodeOrder : NULL; 
  $provider_id = (int) $dt->Provider_ID;
  $con = '(Hidden = 0 OR Hidden IS NULL) and Provider_ID = ' .$provider_id;
  $encounter = $this->EncounterDescriptionListModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con);
}


$patient_ID = (!empty($patient->Patient_ID)) ? $patient->Patient_ID : NULL;
?>
<div class="widget-box">
  <div class="widget-header widget-header-blue widget-header-flat">
    <h4 class="lighter"><?php echo (!empty($dt)) ? "Edit" : "Add" ?> Appointment</h4>
  </div>
  <div class="widget-body">
    <div class="widget-main"> 
      <div class="form-group">
        <label class="col-sm-4 control-label col-xs-12">Patient</label>
        <div class="col-sm-5 col-xs-10">
          <input type="text" class="form-control" readonly  value="<?php echo ($patient) ? $patient->LastName . ', ' . $patient->FirstName . ' ' . $patient->MiddleName : "" ?>" id="patien_name">
          <input type="hidden" name="Patient_ID" value="<?php echo $patient_ID ?>" id="Patient_ID"/>
          <?php
          if ($patient_ID == NULL) {
            echo '<div class="alert alert-danger">Please select patient first.</div>';
          }
          echo form_error('Patient_ID');
          ?>
        </div>
        <div class="col-sm-1 col-xs-2"> 
          <?php
          $params = 'current_select=' . $this->input->get('current_select') . '&current_time=' . $this->input->get('current_time');
          echo anchor('patient?' . $params, '<i class="icon icon-user"></i>', array("class" => 'btn btn-warning btn-block btn-sm pull-right', 'title' => 'Select Patient'));
          ?>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label">Appointment Date </label>
        <div class="col-sm-6">
          <?php
          $value = date_format_only(form_value('ApptStart', $dt));
          $value = ($value == "" && $this->input->get('current_select')) ? $this->input->get('current_select') : date("m-d-Y");
          ?>
          <input type="text" readonly="" class="form-control datepicker" id="dateappt"  name="ApptStart" value="<?php echo $value ?>" <?php echo disabled(form_value('Patient_ID', $patient)) ?>>
          <?php echo form_error('ApptStart'); ?>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label">Start Time</label>
        <div class="col-sm-6">
          <?php
          $temp_time = explode(":", date("h:i:sa"));
          $t_time = date("h:i a");
          if (intval($temp_time[1] % 5 != 0)) {
            if ((5 * (intval($temp_time[1] / 5) + 1)) < 10) {
              if (5 * (intval($temp_time[1] / 5) + 1) == 60) {
                $t_time = strval(intval($temp_time[0]) + 1) . ":00";
              }else{
                $t_time = $temp_time[0] . ":0" . strval(5 * (intval($temp_time[1] / 5) + 1));
              }
            }else{
              if (5 * (intval($temp_time[1] / 5) + 1) == 60) {
                $t_time = strval(intval($temp_time[0]) + 1) . ":00";
              }else{
                $t_time = $temp_time[0] . ":" . strval(5 * (intval($temp_time[1] / 5) + 1));
              }
            }
            if (intval($temp_time[0]) >= 12 ){
              $t_time = $t_time . " pm";
            }else{
              $t_time = $t_time . " am";
            }
          }
          $time = (!empty($dt->ApptStart)) ? time_format($dt->ApptStart) : $t_time;
          $value_start_date =  set_value('ApptStartTime', $time);
          //$value_start_date = time_format(form_value('ApptStart', $dt));
          $value_start_date = ($value_start_date == NULL && $this->input->get('current_time') != NULL) ? $this->input->get('current_time') : $value_start_date;
          ?>
          <input type="text"  readonly="" class="form-control " id="starttime" name="ApptStartTime" value="<?php echo $value_start_date ?>"  <?php echo disabled(form_value('Patient_ID', $patient)) ?>>
          <?php echo form_error('ApptStartTime'); ?>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label">Stop Time</label>
        <div class="col-sm-6">
          <?php
          $time = (!empty($dt->ApptStop)) ? time_format($dt->ApptStop) : NULL;
          $stop_time =  set_value('ApptStopTime', $time);
          //$stop_time = time_format(form_value('ApptStop', $dt));
          $stop_time = ((form_value('ApptStop', $dt) == NULL) && ($this->input->get('current_time') != NULL) ) ? strtolower(date("h:i A", strtotime('+30 minutes', strtotime($this->input->get('current_time'))))) : $stop_time;
          ?>
          <input type="text" readonly="" class="form-control " id="stoptime" name="ApptStopTime" value="<?php echo $stop_time ?>"  <?php echo disabled(form_value('Patient_ID', $patient)) ?>>
          <?php echo form_error('ApptStopTime'); ?>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label">Facility</label>
        <div class="col-sm-6">
          <?php
          $blank = ($facility->num_rows() == 1) ? TRUE : FALSE;
          $option = option_select($facility->result(), 'Facility_ID', 'FacilityName', "[Select]", $blank);
          echo form_dropdown('Facility_ID', $option, form_value('Facility_ID', $dt), 'class = "form-control" ' . disabled(form_value('Patient_ID', $patient)));
          echo form_error('Facility_ID');
          ?>  
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label">Provider</label>
        <div class="col-sm-6">
          <?php
          $blank = ($provider->num_rows() == 1) ? TRUE : FALSE;
          $result = array();
          $result[''] = '[Select]';
          foreach ($provider->result() as $p) {
            $result[$p->Provider_ID] = $p->ProviderLastName . ', ' . $p->ProviderFirstName;
          }
          $option = $result;
          //===========
          $vl_provider = (form_value('Provider_ID', $dt) == "") ? $this->input->get('provider') : form_value('Provider_ID', $dt);
          $action = ($dt == "") ? "add" : 'edit'; 
          echo form_dropdown('Provider_ID', $option, $vl_provider, 'class = "form-control" id="select_provider" data-select="' . form_value('EncounterDescription_ID', $dt) . '" data-action="' . $action . '" data-target="#option_encounter_type"' . disabled(form_value('Patient_ID', $patient)));
          echo form_error('Provider_ID');
          ?>  
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label">Encounter Type</label>
        <div class="col-sm-6" >
          <div id="option_encounter_type"> 
            <?php
            $blank = ($encounter->num_rows() == 1) ? TRUE : FALSE;
            $option = option_select($encounter->result(), 'EncounterDescription_ID', 'EncounterDescription', "[Select]", $blank);
            echo form_dropdown('EncounterDescription_ID', $option, form_value('EncounterDescription_ID', $dt), 'class = "form-control"' . disabled(form_value('Patient_ID', $patient)));
            ?>
          </div>
          <?php
          echo form_error('EncounterDescription_ID');
          ?>  
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-4 control-label" >Status</label>
        <div class="col-sm-6">
          <?php
          $checkin = option_select($checkin_code, 'CodeOrder', 'Description');
          echo form_dropdown('status', $checkin, set_value('status', $get_last_check), 'class = "form-control" ' . disabled(form_value('Patient_ID', $patient)));
          echo form_error('status');
          ?>  
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-4 control-label">Notes</label>
        <div class="col-sm-6">
          <textarea name="Notes" class="form-control" rows="4" <?php echo disabled(form_value('Patient_ID', $patient)) ?> ><?php echo form_value('Notes', $dt); ?></textarea>
          <?php echo form_error('Notes'); ?>
        </div>
      </div> 
    </div><!-- /widget-main -->
  </div><!-- /widget-body -->
</div>

