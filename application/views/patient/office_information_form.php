<?php
$con = '(Hidden = 0 OR Hidden IS NULL)';
$patient_id = 0;
if (!empty($dt->Patient_ID)) {
  $patient_id = $dt->Patient_ID;
}
$provider = $this->ProviderProfileModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con)->result();
?>

<div class="page-header">
  <h1>
    Patient Detail
  </h1>
</div>
<?php $this->load->view('patient/show_alert'); ?>
<ul id="myTab" class="nav nav-tabs">
  <li class=""><?php echo anchor("patient/demographics/$patient_id", "Patient Information", 'id="demographics" class="tab_patients"'); ?></li>
  <li class="active"><?php echo anchor("patient/office_information/$patient_id", "Office Information", 'id="office_information" class="tab_patients"'); ?> </li>
  <li class=""><?php echo anchor("patient/responsible_party/$patient_id", "Responsible Party", 'id="responsible_party" class="tab_patients"'); ?></a></li>
  <li class=""><?php echo anchor("patient/user_defined_fields/$patient_id", "User Defined Fields", 'id="user_defined_fields" class="tab_patients"'); ?></a></li>
</ul>
<br/>
<form class="form-horizontal" id="patient_form" role="form" action="<?php echo site_url("patient/save_office_information"); ?>" method="POST" accept-charset="UTF-8">

  <input type="hidden" name="Patient_ID" id="Patient_ID" value="<?php echo $patient_id ?>">
  <input type="hidden" name="form_active" id="form_active" value="office_information">
  <input type="hidden" name="next_form" id="form_active" value="">


  <div class="row">
    <div class="col-md-7">

      <div class="tab-pane fade active in" id="demographics">
        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading">Office Information</div>
          <div class="panel-body">
            <div class="form-group">
              <label for="AccountNumber" class="col-sm-3 control-label">Account Number</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="AccountNumber" name="AccountNumber" placeholder="Account Number" value="<?php echo sprintf('%s', form_value('AccountNumber', $dt)) ?>">
              </div>
            </div>
            <div class="form-group">
              <label for="Participant_Id" class="col-sm-3 control-label">External ID</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="Participant_Id" name="Participant_Id" placeholder="External ID" value="<?php echo form_value('Participant_Id', $dt) ?>">
              </div>
            </div>
            <div class="form-group">
              <label for="Provider_ID" class="col-sm-3 control-label">Provider</label>
              <div class="col-sm-9">
                <?php
                $result[''] = '[Select]';
                foreach ($provider as $p) {
                  $result[$p->Provider_ID] = $p->ProviderLastName . ', ' . $p->ProviderFirstName;
                }
                $option = $result;
                echo form_dropdown('Provider_ID', $option, form_value('Provider_ID', $dt), 'class = "form-control" id="Provider_ID"');
                echo form_error('Provider_ID');
                ?> 
              </div>
            </div>

            <div class="form-group">
              <label for="AlertNotes" class="col-sm-3 control-label">All Notes</label>
              <div class="col-sm-9">
                <textarea class="form-control" name="AlertNotes" id="AlertNotes" rows="5"><?php echo form_value('AlertNotes', $dt); ?></textarea>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading">Patient Status Information</div>
        <div class="panel-body">
          <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">Patient Status</label>
            <div class="col-sm-8">
              <div class="radio-inline">
                <label>
                  <input type="radio" name="" id="optionsRadios1" value="option1" checked>
                  <?php echo form_radio('Hidden', 0, (form_value('Hidden', $dt) == 0)) ?>
                  Active
                </label>
              </div>
              <div class="radio-inline">
                <label>
                  <?php echo form_radio('Hidden', 1, (form_value('Hidden', $dt) == 1)) ?>
                  Inactive
                </label>
              </div>
              <div class="clear"></div>

              <label class="checkbox-inline">
                <?php
                $Confidential = (form_value('Confidential', $dt) == 1);
                echo form_checkbox('Confidential', 1, $Confidential)
                ?>
                Confidential 
              </label>
            </div>
          </div>

          <div class="form-group">
            <label for="inputEmail3" class="col-sm-4 control-label">Date of Death</label>
            <div class="col-sm-8">
              <?php
              $dod = NULL;
              if (form_value('DOD', $dt) != NULL) {
                $dod = date("Y-m-d  h:i", strtotime(form_value('DOD', $dt)));
              } 
              ?>
              <input type="text" class="form-control datetimepicker" name="DOD" id="DOD" name="DOD" placeholder="Date of Death" value="<?php echo $dod; ?>">
            </div>
          </div> 
        </div>
      </div>
    </div>

  </div>


  <hr/>
  <button type="submit" class="btn btn-primary" ><i class="icon-save"></i>&nbsp;&nbsp; Save</button>


</form>
