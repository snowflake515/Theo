<?php
$con = '(Hidden = 0 OR Hidden IS NULL)';
$patient_id = 0;
if (!empty($dt->Patient_ID)) {
  $patient_id = $dt->Patient_ID;
}
$state = $this->StateModel->get_all()->result();
$language = $this->LanguagemasterModel->get_where($con)->result();
$ethnicity = $this->EthnicitymasterModel->get_by_field("Dropdown_ID", "2", $con)->result();
$race = $this->EthnicitymasterModel->get_by_field("Dropdown_ID", "1", $con)->result();
$appointmen_history = $this->AppointmentModel->get_appointments_by_patient($patient_id)->result();
$provider = $this->ProviderProfileModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con)->result();
?>

<div class="page-header">
  <h1>
    Patient Detail
  </h1>
</div>

<?php $this->load->view('patient/show_alert'); ?>

<ul id="myTab" class="nav nav-tabs">
  <li class="active"><?php echo anchor("patient/demographics/$patient_id", "Patient Information", 'id="demographics" class="tab_patients"'); ?></li>
  <?php
  if ($patient_id) {
    ?>
    <li class=""><?php echo anchor("patient/office_information/$patient_id", "Office Information", 'id="office_information" class="tab_patients" '); ?> </li>
    <li class=""><?php echo anchor("patient/responsible_party/$patient_id", "Responsible Party", 'id="responsible_party" class="tab_patients" '); ?></a></li>
    <li class=""><?php echo anchor("patient/user_defined_fields/$patient_id", "User Defined Fields", 'id="user_defined_fields" class="tab_patients"'); ?></a></li>
    <?php
  }
  ?>
</ul>
<br/>
<form class="form-horizontal" id="patient_form" role="form" action="<?php echo site_url("patient/save_demographics"); ?>" method="POST" accept-charset="UTF-8">

  <input type="hidden" name="Patient_ID" id="Patient_ID" value="<?php echo $patient_id ?>">
  <input type="hidden" name="form_active" id="form_active" value="demographics">
  <input type="hidden" name="next_form" id="form_active" value="">


  <div class="tab-pane fade active in" id="demographics">
    <div class="panel panel-default">
      <!-- Default panel contents -->
      <div class="panel-heading">Patient Information &nbsp; &nbsp;
        <?php if ($patient_id) : ?>
          <a href="#appointment_modal"  data-toggle="modal" title="Appointment History" data-patient="<?php echo $patient_id ?>" onclick="appt_history_req(this)"><i class="icon-calendar"></i> Appointment History</a>
          <span>&nbsp;</span>
        <?php endif; ?>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="FirstName" class="col-sm-4 control-label">First Name</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="FirstName" name="FirstName" placeholder="First Name" value="<?php echo form_value('FirstName', $dt) ?>">
                <?php echo form_error('FirstName'); ?>
              </div>
            </div>
            <div class="form-group">
              <label for="MiddleName" class="col-sm-4 control-label">Middle Name</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="MiddleName" name="MiddleName" placeholder="Middle Name" value="<?php echo form_value('MiddleName', $dt) ?>">
                <?php echo form_error('MiddleName'); ?>
              </div>
            </div>
            <div class="form-group">
              <label for="LastName" class="col-sm-4 control-label">Last Name</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="LastName" id="LastName" placeholder="Last Name" value="<?php echo form_value('LastName', $dt) ?>">
                <?php echo form_error('LastName'); ?>
              </div>
            </div>
            <div class="form-group">
              <label for="NickName" class="col-sm-4 control-label">Nick Name</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" name="NickName" id="NickName" placeholder="Nick Name" value="<?php echo form_value('NickName', $dt) ?>">
                <?php echo form_error('NickName'); ?>
              </div>
              <label for="Suffix" class="col-sm-1 control-label">Suffix</label>
              <div class="col-sm-3">
                <input type="text" class="form-control" id="Suffix" name="Suffix" placeholder="Suffix" value="<?php echo form_value('Suffix', $dt) ?>">
                <?php echo form_error('Suffix'); ?>
              </div>
            </div>
            <div class="form-group">
              <label for="DOB" class="col-sm-4 control-label">DOB</label>
              <div class="col-sm-8">
                <?php
                $date_patient = "";
                if (form_value('DOB', $dt) != "") {
                  $date_patient = date("m-d-Y", strtotime(form_value('DOB', $dt)));
                }
                ?>
                <input type="text"  class="form-control date-form" name="DOB"  placeholder="mm/dd/yyyy" value="<?php echo $date_patient; ?>">
                <?php echo form_error('DOB'); ?>
              </div>

            </div>
            <div class="form-group">
              <label for="inputPassword3" class="col-sm-4 control-label">Gender</label>

              <div class="col-sm-8">
                <?php
                $sex = (object) array("U" => "Unknown", "M" => "Male", "F" => "Female");
                echo form_dropdown('Sex', $sex, form_value('Sex', $dt), 'class = "form-control"');
                echo form_error('Sex');
                ?>
              </div>
            </div>
            <div class="form-group">
              <label for="SSN" class="col-sm-4 control-label">SSN</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="SSN" name="SSN" placeholder="SSN" value="<?php echo form_value('SSN', $dt) ?>">
                <?php echo form_error('SSN'); ?>
              </div>
            </div>
            <div class="form-group">
              <label for="MedicalRecordNumber" class="col-sm-4 control-label">MRN</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="MedicalRecordNumber" name="MedicalRecordNumber" placeholder="Medical Record Number" value="<?php echo form_value('MedicalRecordNumber', $dt) ?>">
                <?php echo form_error('MedicalRecordNumber'); ?>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="LanguageMaster_ID" class="col-sm-4 control-label">Language</label>
              <div class="col-sm-8">
                <?php
                $option = option_select($language, 'LanguageMaster_ID', 'Language');
                echo form_dropdown('LanguageMaster_ID', $option, form_value('LanguageMaster_ID', $dt), 'class = "form-control" id="LanguageMaster_ID"');
                echo form_error('LanguageMaster_ID');
                ?>

              </div>
            </div>
            <div class="form-group">
              <label for="MaritalStatus" class="col-sm-4 control-label">Marital Status</label>
              <div class="col-sm-8">
                <?php
                $marital = array("" => "[Select]", "0" => "Single", "S" => "Separated", "D" => "Divorced", "M" => "Married", "W" => "Widowed");
                echo form_dropdown('MaritalStatus', $marital, form_value('MaritalStatus', $dt), 'class = "form-control" id="MaritalStatus"');
                echo form_error('MaritalStatus');
                ?>
              </div>
            </div>
            <div class="form-group">
              <label for="BloodType" class="col-sm-4 control-label">Blood Type</label>
              <div class="col-sm-8">
                <?php
                $bloodType = array("" => "[Select]", "AP" => "A Positive", "AN" => "A Negative", "BP" => "B Positive", "BN" => "B Negative", "ABP" => "AB Positive", "ABN" => "AB Negative", "OP" => "O Positive", "ON" => "O Negative");
                echo form_dropdown('BloodType', $bloodType, form_value('BloodType', $dt), 'class = "form-control" id="BloodType"');
                echo form_error('BloodType');
                ?>
              </div>
            </div>
            <div class="form-group">
              <label for="Race_EthnicityMaster_ID" class="col-sm-4 control-label">Race</label>
              <div class="col-sm-8">
                <?php
                $option = option_select($race, 'EthnicityMaster_ID', 'Description');
                echo form_dropdown('Race_EthnicityMaster_ID', $option, form_value('Race_EthnicityMaster_ID', $dt), 'class = "form-control" id = "Race_EthnicityMaster_ID"');
                echo form_error('Race_EthnicityMaster_ID');
                ?>
              </div>
            </div>
            <div class="form-group">
              <label for="Ethnicity_EthnicityMaster_ID" class="col-sm-4 control-label">Ethnicity</label>
              <div class="col-sm-8">
                <?php
                $option = option_select($ethnicity, 'EthnicityMaster_ID', 'Description');
                echo form_dropdown('Ethnicity_EthnicityMaster_ID', $option, form_value('Ethnicity_EthnicityMaster_ID', $dt), 'class = "form-control" id="Ethnicity_EthnicityMaster_ID"');
                echo form_error('Ethnicity_EthnicityMaster_ID');
                ?>
              </div>
            </div>
            <div class="form-group">
              <label for="PreferredContact" class="col-sm-4 control-label">Reminder Preference</label>
              <div class="col-sm-8">
                <?php
                $reminder = array("" => "[Select]", "E" => "Email", "P" => "Phone", "N" => "Letter");
                echo form_dropdown('PreferredContact', $reminder, form_value('PreferredContact', $dt), 'class = "form-control"');
                echo form_error('PreferredContact');
                ?>
              </div>
            </div>

            <div class="form-group">
              <label for="Notes" class="col-sm-4 control-label">Patient Memo</label>
              <div class="col-sm-8">
                <textarea class="form-control" rows="3" id="Notes" name="Notes"><?php echo form_value('Notes', $dt) ?></textarea>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>


  <div class="row">
    <div class="col-md-6">
      <div class="tab-pane fade active in" id="demographics">
        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading">Address and Contact Information</div>
          <div class="panel-body">
            <div class="form-group">
              <label for="AddressType" class="col-sm-3 control-label">Address Type</label>
              <div class="col-sm-9">
                <?php
                $adress_type = (object) array(" " => "[Select]", "H" => "Home", "P" => "Phone", "T" => "Temporary");
                echo form_dropdown('AddressType', $adress_type, form_value('AddressType', $dt), 'class = "form-control" id="AddressType"');
                echo form_error('AddressType');
                ?>
              </div>
            </div>
            <div class="form-group">
              <label for="Addr1" class="col-sm-3 control-label">Address </label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="Addr1" name="Addr1" placeholder="Address" value="<?php echo form_value('Addr1', $dt) ?>">
                <?php echo form_error('Addr1'); ?>
              </div>
            </div>
            <div class="form-group">
              <label for="Addr2" class="col-sm-3 control-label">Address </label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="Addr2" name="Addr2" placeholder="Other Address" value="<?php echo form_value('Addr2', $dt) ?>">
                <?php echo form_error('Addr2'); ?>
              </div>
            </div>
            <div class="form-group">
              <label for="City" class="col-sm-3 control-label">City</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="City" name="City" placeholder="City" value="<?php echo form_value('City', $dt) ?>">
                <?php echo form_error('City'); ?>
              </div>
            </div>
            <div class="form-group">
              <label for="State" class="col-sm-3 control-label">State</label>
              <div class="col-sm-5">
                <?php
                $option = option_select($state, 'States_Abbr', 'States_FullName');
                echo form_dropdown('State', $option, form_value('State', $dt), 'class = "form-control" id = "State"');
                echo form_error('States[States_Id]');
                ?>
              </div>
              <label for="Zip" class="col-sm-1 control-label">Zip</label>
              <div class="col-sm-3">
                <input type="text" class="form-control" id="Zip" name="Zip" placeholder="Zip" value="<?php echo form_value('Zip', $dt) ?>">
                <?php echo form_error('Zip'); ?>
              </div>
            </div>
            <div class="form-group">
              <label for="PhoneHome" class="col-sm-3 control-label">Home Phone</label>
              <div class="col-sm-3">
                <input type="text" class="form-control" id="PhoneHome" name="PhoneHome" placeholder="Phone Home"
                       value="<?php echo sprintf('%s', form_value('PhoneHome', $dt)) ?>">
                       <?php echo form_error('PhoneHome'); ?>
              </div>
              <label for="PhoneWork" class="col-sm-2 control-label"> Cell Phone</label>
              <div class="col-sm-4">
                <input type="text" class="form-control" id="PhoneWork" name="PhoneCell" placeholder="PhoneCell"
                       value="<?php echo sprintf('%s', form_value('PhoneCell', $dt)) ?>">
                       <?php echo form_error('PhoneCell'); ?>
              </div>
            </div>
            <div class="form-group">
              <label for="Email" class="col-sm-3 control-label">Email Address</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="Email" name="Email" placeholder="Email" value="<?php echo form_value('Email', $dt) ?>" >
                <?php echo form_error('Email'); ?>
              </div>
            </div>
            <div class="form-group">
              <label for="EmerContact" class="col-sm-3 control-label">Emergency Contact</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="EmerContact" name="EmerContact" placeholder="Emergency Contact" value="<?php echo form_value('EmerContact', $dt) ?>">
                <?php echo form_error('EmerContact'); ?>
              </div>
            </div>
            <br/>
          </div>
        </div>
      </div>


    </div>
    <div class="col-md-6">
      <div class="tab-pane fade active in" id="demographics">
        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading">Office Information</div>
          <div class="panel-body">
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
          </div>
        </div>

        <div class="tab-pane fade active in" id="demographics">
          <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">Primary Insurance</div>
            <div class="panel-body">
              <div class="form-group">
                <label for="PrimaryIns" class="col-sm-3 control-label">Provider / Plan</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="PrimaryIns" name="PrimaryIns" placeholder="Insurance"  value="<?php echo form_value('PrimaryIns', $dt) ?>">
                  <?php echo form_error('PrimaryIns'); ?>
                </div>
              </div>
              <div class="form-group">
                <label for="PrimaryInsPol" class="col-sm-3 control-label">Policy Number</label>
                <div class="col-sm-3">
                  <input type="text" class="form-control" id="PrimaryInsPol" name="PrimaryInsPol" placeholder="Primary Number"  value="<?php echo form_value('PrimaryInsPol', $dt) ?>">
                  <?php echo form_error('PrimaryInsPol'); ?>
                </div>
                <label for="PrimaryInsEffectiveDate" class="col-sm-3 control-label">Effective Date</label>
                <div class="col-sm-3">
                  <?php
                  $PrimaryInsEffectiveDate = "";
                  if (form_value('PrimaryInsEffectiveDate', $dt) != "") {
                    $PrimaryInsEffectiveDate = date("m-d-Y", strtotime(form_value('PrimaryInsEffectiveDate', $dt)));
                  }
                  ?>
                  <input type="text" readonly="" class="form-control datepicker" id="PrimaryInsEffectiveDate" name="PrimaryInsEffectiveDate" placeholder="Date"  value="<?php echo $PrimaryInsEffectiveDate ?>">
                  <?php echo form_error('PrimaryInsEffectiveDate'); ?>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="tab-pane fade active in" id="demographics">
          <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">Secondary Insurance</div>
            <div class="panel-body">
              <div class="form-group">
                <label for="SecondaryIns" class="col-sm-3 control-label">Provider / Plan</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="SecondaryIns" name="SecondaryIns" placeholder="Secondary Insurance"  value="<?php echo form_value('SecondaryIns', $dt) ?>">
                  <?php echo form_error('SecondaryIns'); ?>
                </div>
              </div>
              <div class="form-group">
                <label for="SecondaryInsPol" class="col-sm-3 control-label">Policy Number</label>
                <div class="col-sm-3">
                  <input type="text" class="form-control" id="SecondaryInsPol" name="SecondaryInsPol" placeholder="Policy Number"  value="<?php echo form_value('SecondaryInsPol', $dt) ?>">
                  <?php echo form_error('SecondaryInsPol'); ?>
                </div>
                <label for="SecondaryInsEffectiveDate" class="col-sm-3 control-label">Effective Date</label>
                <div class="col-sm-3">
                  <?php
                  $SecondaryInsEffectiveDate = "";
                  if (form_value('SecondaryInsEffectiveDate', $dt) != "") {
                    $SecondaryInsEffectiveDate = date("m-d-Y", strtotime(form_value('SecondaryInsEffectiveDate', $dt)));
                  }
                  ?>
                  <input type="text"  readonly="" class="form-control datepicker" id="SecondaryInsEffectiveDate" name="SecondaryInsEffectiveDate" placeholder="Date"  value="<?php echo $SecondaryInsEffectiveDate; ?>">
                  <?php echo form_error('SecondaryInsEffectiveDate'); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <hr/>
  <button type="submit" name="back" value="back"  class="btn btn-default" ><i class="icon icon-arrow-left"></i> Back</button>
  <button type="submit" class="btn btn-primary" ><i class="icon-save"></i>&nbsp;&nbsp; Save</button>

</form>



<div class="modal  fade" id="appointment_modal" >
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="appointment_modal_body">

    </div>
  </div>
</div>

<div class="modal fade" id="eligibility_modal" >
  <div class="modal-dialog" role="document">
    <div class="modal-content" id="eligibility_modal_body">

    </div>
  </div>
</div>


<script>
  var site_url = '<?php echo site_url() ?>/';
  function eligibility_req(el) {
    $('#eligibility_modal_body').html('<div class="modal-body text-center"><strong>Please Wait...</strong></div>');
    $.ajax({
      type: "POST",
      url: site_url + "service/eligibility",
      data: {patient_id: $(el).data('patient')},
      success: function (data) {
        if (data.error) {
          $('#eligibility_modal_body').html('<div class="modal-body"><strong>' + data.html + '</strong></div>');
        } else {
          $('#eligibility_modal_body').html('<div class="modal-body"> <strong>Saved to patient memo!</strong></div>');
          $('#Notes').val(data.html);
        }

      },
      error: function (ress, status, error) {

      }
    });
  }

  function appt_history_req(el) {

    $('#appointment_modal_body').html('<div class="modal-body text-center"><strong>Please Wait...</strong></div>');
    $.ajax({
      type: "POST",
      url: site_url + "service/appointment_history",
      data: {patient_id: $(el).data('patient')},
      success: function (data) {
        $('#appointment_modal_body').html(data.html);
      },
      error: function (ress, status, error) {

      }
    });
  }
</script>
