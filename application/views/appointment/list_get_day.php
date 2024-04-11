<div class="page-header">
  <h1>
    Add appointment
  </h1>
</div>

<?php 
$params = 'current_select='.$this->input->get('current_select').'&current_time='.$this->input->get('current_time');
echo anchor('schedule/appointment_new?'.$params, '<i class="icon icon-book"></i>  &nbsp; &nbsp; Add appointment', array('class' => 'btn btn-default')); 
?>
<div class="clearfix"><br/></div>

<!-- Default panel contents -->
<div class="panel-heading"></div>
<div class="table-header">
  <b><?php echo date('F jS  Y', strtotime($date)) ?></b>
</div>


<div class="table-responsive">
  <table class="table table-striped table-bordered table-hover">
    <thead>
      <tr>
        <th>#</th>
        <th>Time</th>
        <th>Information</th>
        <th>Patient</th>
        <th width='120'>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $i = 0;
      foreach ($appts as $value) {
        $i ++;
        $patien = $this->PatientProfileModel->get_by_id($value->Patient_ID)->row();
        $facility = $this->FacilityListModel->get_by_id($value->Facility_ID)->row();
        $provider = $this->ProviderProfileModel->get_by_id($value->provider_id_appt)->row();
        $checkin = option_select($checkin_code, 'CodeOrder', 'Description');
        $get_last_check = $this->CheckInLogModel->get_last_check($value->Appointments_ID)->row();
        $checkIn_codes = $this->CheckInCodeModel->get_by_field('CodeOrder', ($get_last_check) ? $get_last_check->CodeOrder : 0, array('Org_Id' => $this->current_user->Org_Id))->row();
        
		
		$facility_name = ($facility) ? $facility->FacilityName: NULL;
		
        $drop = form_dropdown('check', $checkin, form_value('CodeOrder', $get_last_check), 'class = "form-control" id="CCodes_Id" onchange="change_status_chekin_data(this)" data-apptid="' . $value->Appointments_ID . '"  data-color="#color_' . $value->Appointments_ID . '"');
        $color = ($checkIn_codes) ? $checkIn_codes->Color : NULL;
        $start_encounter = anchor('encounter/start/' . $value->Appointments_ID, '<i class="icon icon-time"></i>', array('class' => ' btn btn-default btn-sm', 'title' => 'Start Encounter', 'data-toggle' => "tooltip", 'data-placement' => "bottom"));
        $delete_appt = anchor('appointment/destroy/' . $value->Appointments_ID . '/' , '<i class="icon icon-trash"></i>', array('class' => 'need_confrim btn-default btn-sm  btn', 'title' => 'Delete Appointment', 'data-toggle' => "tooltip", 'data-placement' => "bottom"));
        $adit_appt = anchor('schedule/appointment_edit/' . $value->Appointments_ID, '<i class="icon icon-book"></i>', array('class' => ' btn btn-default btn-sm', 'title' => 'Edit Appointment', 'data-toggle' => "tooltip", 'data-placement' => "bottom"));

        echo "<tr>
                      <td>$i</td>
                      <td> " . date('h:i A', strtotime($value->ApptStart)) . "<br/> " . date('H:i A', strtotime($value->ApptStop)) . "</td>
                      <td> <small><strong >$provider->ProviderLastName, 
					  $provider->ProviderFirstName</strong></small> <br/> 
					  $facility_name <br/> 
					  $value->appt_notes</td>
                      <td>$patien->LastName, $patien->FirstName<br/> "
        . "<span id='color_$value->Appointments_ID' style='background: #$color; width: 130px;display: inline-block;height: 5px;'></span>$drop<br/>
                        </td>
                        <td>$start_encounter $adit_appt $delete_appt</td>
                    </tr>";
      }
      ?>

    </tbody>
  </table>
</div>



<?php echo anchor('schedule', '<i class="icon icon-arrow-left"></i> &nbsp;  Back', array('class' => 'btn btn-default')); ?>



