<div class="widget-box">
  <div class="widget-header widget-header-blue ">
    <h4 class="lighter">Encounter Details - <?php echo $patient->LastName ?>, <?php echo $patient->FirstName ?> <?php echo $patient->MiddleName ?></h4>
  </div>

  <div class="widget-body">
    <div class="widget-main">
      <div class="row">
        <div class="col-md-3">
          <div style="max-height: 400px; overflow-y: auto;">
            <h4>Encounter History</h4>
            <ul class="nav list-unstyled" id="list_encounter_history">
              <?php
              if ($dt && !empty($encounter_history)) {
                foreach ($encounter_history as $encounter_history_dt) {
                  $ic = '<i class="icon  icon-check-empty"></i>';
                  $act = '';
                  if ($encounter_history_dt->Encounter_ID == $dt->Encounter_ID) {
                    $ic = '<i class="icon icon-check"></i>';
                    $act = 'active';
                  }
                  if ($encounter_history_dt->Appointments_ID) {
                    echo '<li class="' . $act . '"><a href="' . site_url('encounter/start/' . $encounter_history_dt->Appointments_ID) . '" class="no-openalert">' . $ic . ' &nbsp;  ' . date('m/d/Y', strtotime($encounter_history_dt->EncounterDate)) . ' &nbsp; <span>' . $encounter_history_dt->ChiefComplaint . '</span></a></li>';
                  }
                }
              }
              ?>
            </ul>
          </div>
        </div>
        <div class="col-md-9">
          <div class="form-group">
            <label class="col-sm-4 col-md-2 control-label">Encounter Date</label>
            <div class="col-sm-6 col-md-8">
              <input type="text" readonly=""  class="form-control datepicker" onchange="changeDateEncounter()" name="EncounterDate" id="EncounterDate" <?php echo disabled_ecnounter($dt->EncounterSignedOff) ?> value="<?php echo date_format_only(form_value('EncounterDate', $dt)) ?>">
              <?php echo form_error('EncounterDate'); ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 col-md-2 control-label">Problem</label>
            <div class="col-sm-6 col-md-8">
              <textarea name="ChiefComplaint"  id="ChiefComplaint" class="form-control" rows="4" <?php echo disabled_ecnounter($dt->EncounterSignedOff) ?>><?php echo form_value('ChiefComplaint', $dt); ?></textarea>
              <?php echo form_error('ChiefComplaint'); ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-4 col-md-2 control-label">Encounter Type</label>
            <div class="col-sm-6 col-md-8" id="option_encounter_type_form">
              <?php
              $blank = ($encounter->num_rows() == 1) ? TRUE : FALSE;
              $option = option_select($encounter->result(), 'EncounterDescription_ID', 'EncounterDescription', "[Select]", $blank);
              echo form_dropdown('EncounterDescription_ID', $option, form_value('EncounterDescription_ID', $dt), 'class = "form-control" id="EncounterDescription_ID" ' . disabled_ecnounter($dt->EncounterSignedOff));
              echo form_error('EncounterDescription_ID');
              ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 col-md-2 control-label">Rendering Provider</label>
            <div class="col-sm-6 col-md-8">
              <div class="row">
                <div class="col-xs-10">
                  <?php
                  $blank = ($provider->num_rows() == 1) ? TRUE : FALSE;
                  // $option = option_select($provider->result(), 'Provider_ID', 'ProviderLastName', "[Select]", $blank);
                  $result = array();
                  $result[''] = '[Select]';
                  foreach ($provider->result() as $p) {
                    $result[$p->Provider_ID] = $p->ProviderLastName . ', ' . $p->ProviderFirstName;
                  }
                  $option = $result;
                  echo form_dropdown('Provider_ID', $option, form_value('Provider_ID', $dt), ' class = "form-control select_provider" id="select_provider"  data-select="' . form_value('EncounterDescription_ID', $dt) . '" data-target="#option_encounter_type_form" ' . disabled_ecnounter($dt->EncounterSignedOff));
                  echo form_error('Provider_ID');
                  ?>
                </div>
                <div class="col-xs-2">
                  <div class="checkbox">
                    <label> <?php echo form_checkbox(array('name' => 'params[Rendering_id]', 'id' => 'Rendering_id', 'class' => ""), '1', ($dt->RenderingSignedOffDate != NULL) ? TRUE : FALSE); ?> SignOff</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 col-md-2 control-label">Supervising Provider</label>
            <div class="col-sm-6 col-md-8">
              <div class="row">
                <div class="col-xs-10">
                  <?php
                  $blank = ($provider->num_rows() == 1) ? TRUE : FALSE;
                  //$option = option_select($provider->result(), 'Provider_ID', 'ProviderLastName', "[Select]", $blank);
                  $result = array();
                  $result[''] = '[Select]';
                  foreach ($sprovider->result() as $p) {
                    $result[$p->Provider_ID] = $p->ProviderLastName . ', ' . $p->ProviderFirstName;
                  }
                  $option = $result;
                  echo form_dropdown('SupProvider_ID', $option, form_value('SupProvider_Id', $dt), 'class = "form-control select_provider"  id="select_supprovider_id" ' . disabled_ecnounter($dt->EncounterSignedOff));
                  echo form_error('SupProvider_ID');
                  ?>
                </div>
                <div class="col-xs-2">
                  <div class="checkbox">
                    <div id="check_provider" class="sr-only"><?php echo form_value('RenderingSignedOffDate', $dt) ?></div>
                    <label> <?php echo form_checkbox(array('name' => 'SignedOffSupervising', 'id' => 'SignedOffSupervising', 'class' => "signoff"), '1', $dt->SignedOffSupervising); ?> SignOff</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 col-md-2 control-label">Department</label>
            <div class="col-sm-6 col-md-8">
              <?php
              $blank = ($department->num_rows() == 1) ? TRUE : FALSE;
              $option = option_select($department->result(), 'Dept_ID', 'DeptName', "[Select]", $blank);
              echo form_dropdown('Dept_ID', $option, form_value('Dept_ID', $dt), ' class = "form-control" id="Dept_ID" ' . disabled_ecnounter($dt->EncounterSignedOff));
              echo form_error('Dept_ID');
              ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 col-md-2 control-label">Facility</label>
            <div class="col-sm-6 col-md-8">
              <?php
              $blank = ($facility->num_rows() == 1) ? TRUE : FALSE;
              $option = option_select($facility->result(), 'Facility_ID', 'FacilityName', "[Select]", $blank);
              echo form_dropdown('Facility_ID', $option, form_value('Facility_ID', $dt), ' class = "form-control" id="Facility_ID" ' . disabled_ecnounter($dt->EncounterSignedOff));
              echo form_error('Facility_ID');
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<br />

<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="background: transparent; box-shadow:none; border: 0">
      <div class="modal-body" style="max-width: 200px; background: #fff; margin: 0 auto;">
        <p class="text-center" style="margin: 0; font-weight: 700">Please Wait ...</p>
      </div>
    </div>
  </div>
</div>

<div id="myModalConfm" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog " role="document">
    <div class="modal-content">
      <div class="modal-body">
        <h4 class="modal-title">Do you want to create a new encounter for this patient?</h4>
      </div>
      <div class="modal-footer" style="margin-top: 0">
        <button type="button" onclick="confirmDateEncounter('NO')" class="btn btn-default btn-sm">NO</button>
        <button type="button" onclick="confirmDateEncounter('YES')" class="btn btn-primary btn-sm">YES</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
  let currentEncounterDate = '';
  let defaultdate = '<?php echo date_format_only(form_value('EncounterDate', $dt)) ?>';
  let encounter_id = <?php echo form_value('Encounter_ID', $dt) ?>;
  let appt_id = <?php echo form_value('Appointments_ID', $dt) ?>;

  function changeDateEncounter() {
    currentEncounterDate = $('#EncounterDate').val();
    if ((defaultdate && currentEncounterDate) && defaultdate != currentEncounterDate) {
      $('#myModalConfm').modal({
        keyboard: false,
        backdrop: 'static',
        show: true
      });
    }
  }

  function saveEnncounterAjax(){
    $.ajax({
        data: {
          Appointments_ID: appt_id,
          encounter_id: encounter_id, 
          ChiefComplaint: $('#ChiefComplaint').val(),
          EncounterDescription_ID: $('#EncounterDescription_ID').val(),
          Provider_ID: $('#select_provider').val(),
          SupProvider_ID: $('#select_supprovider_id').val(),
          Dept_ID: $('#Dept_ID').val(),
          Facility_ID: $('#Facility_ID').val()
        },
        method: 'POST',
        url: mysite + '/js/encounter/save_encounter'
      });
  }

  function confirmDateEncounter(answer) {
    $('#myModalConfm').modal('hide');
    if (answer == 'NO') {
      $('#EncounterDate').val(defaultdate);
    } else {
      $('#myModal').modal({
        keyboard: false,
        backdrop: 'static',
        show: true
      });
      $.ajax({
        data: {
          encounter_id: encounter_id,
          encounter_date: currentEncounterDate,
          ChiefComplaint: $('#ChiefComplaint').val(),
          EncounterDescription_ID: $('#EncounterDescription_ID').val(),
          Provider_ID: $('#select_provider').val(),
          SupProvider_ID: $('#select_supprovider_id').val(),
          Dept_ID: $('#Dept_ID').val(),
          Facility_ID: $('#Facility_ID').val()
        },
        method: 'POST',
        url: mysite + '/js/encounter/changedate'
      }).done(function(data) {
        if (data.url) {
          window.location.href = data.url;
        } else {
          alert('Ups something wrong, Please try agaian!');
        }
        $('#myModal').modal('hide');
      });
    }
  }
</script>