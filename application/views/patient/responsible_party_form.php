<?php
$con = '(Hidden = 0 OR Hidden IS NULL)';
$patient_id = 0;
if (!empty($dt->Patient_ID)) {
  $patient_id = $dt->Patient_ID;
}
$relationship = $this->RelationshipModel->get_all()->result();
$state = $this->StateModel->get_all()->result();
?>

<div class="page-header">
  <h1>
    Patient Detail
  </h1>
</div>

<ul id="myTab" class="nav nav-tabs">
  <li class=""><?php echo anchor("patient/demographics/$patient_id", "Patient Information", 'id="demographics" class="tab_patients"'); ?></li>
  <li class=""><?php echo anchor("patient/office_information/$patient_id", "Office Information", 'id="office_information" class="tab_patients"'); ?> </li>
  <li class="active"><?php echo anchor("patient/responsible_party/$patient_id", "Responsible Party", 'id="responsible_party" class="tab_patients"'); ?></a></li>
  <li class=""><?php echo anchor("patient/user_defined_fields/$patient_id", "User Defined Fields", 'id="user_defined_fields" class="tab_patients"'); ?></a></li>
</ul>
<br/>
<form class="form-horizontal"  id="patient_form" role="form" action="<?php echo site_url("patient/save_responsible_party"); ?>" method="POST" accept-charset="UTF-8">

  <input type="hidden" name="Patient_ID" id="Patient_ID" value="<?php echo $patient_id ?>">
  <input type="hidden" name="form_active" id="form_active" value="responsible_party">
  <input type="hidden" name="next_form" id="form_active" value="">


  <div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading">Responsible Party Information</div>
    <div class="panel-body">

      <div class="row">
        <div class="col-md-6">


          <div class="form-group">
            <label for="inputEmail3" class="col-sm-3 control-label">Relationship</label>
            <div class="col-sm-9">
              <?php
              $option = option_select($relationship, 'Relationship_ID', 'Relationship');
              echo form_dropdown('Relationship_ID', $option, form_value('Relationship_ID', $dt), 'class = "form-control" id = "Relationship_ID"');
              ?>           
            </div>
          </div>
          <div class="form-group">
            <label for="RPFirstName" class="col-sm-3 control-label">First Name </label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="RPFirstName" name="RPFirstName" placeholder="First Name" value="<?php echo form_value('RPFirstName', $dt) ?>">
              <?php echo form_error('RPFirstName'); ?>
            </div>
          </div>
          <div class="form-group">
            <label for="RPMiddleName" class="col-sm-3 control-label">Midle Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="RPMiddleName" name="RPMiddleName" placeholder="Midle Name" value="<?php echo form_value('RPMiddleName', $dt) ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="RPLastName" class="col-sm-3 control-label">Last Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="RPLastName" name="RPLastName" placeholder="Last Name" value="<?php echo form_value('RPLastName', $dt) ?>">
            </div>
          </div>


          <div class="form-group">

            <label for="RPSSN" class="col-sm-3 control-label">SSN</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="RPSSN" name="RPSSN" placeholder="SSN" value="<?php echo form_value('RPSSN', $dt) ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="RPAddr1" class="col-sm-3 control-label">Address</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="RPAddr1" name="RPAddr1" placeholder="Address" value="<?php echo form_value('RPAddr1', $dt) ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="RPAddr2" class="col-sm-3 control-label">Address2</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="RPAddr2" name="RPAddr2" placeholder="Other Address" value="<?php echo form_value('RPAddr2', $dt) ?>">
            </div>
          </div>

        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label for="RPCity" class="col-sm-3 control-label">City</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="RPCity" name="RPCity" placeholder="City" value="<?php echo form_value('RPCity', $dt) ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="RPState" class="col-sm-3 control-label">State</label>
            <div class="col-sm-9">
              <?php
              $option = option_select($state, 'States_Abbr', 'States_FullName');
              echo form_dropdown('RPState', $option, form_value('RPState', $dt), 'class = "form-control" id = "RPState"');
              ?>
            </div>
          </div>
          <div class="form-group">
            <label for="RPPhoneHome" class="col-sm-3 control-label">Home Phone</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="RPPhoneHome" name="RPPhoneHome" placeholder="Home Phone" value="<?php echo form_value('RPPhoneHome', $dt) ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="RPPhoneWork" class="col-sm-3 control-label">Work Phone</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="RPPhoneWork" name="RPPhoneWork" placeholder="Work Phone" value="<?php echo form_value('RPPhoneWork', $dt) ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="RPPhoneCell" class="col-sm-3 control-label">Cell Phone</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="RPPhoneCell" name="RPPhoneCell" placeholder="Cell Phone" value="<?php echo form_value('RPPhoneCell', $dt) ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="RPEmail" class="col-sm-3 control-label">Email Adress</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="RPEmail" name="RPEmail" placeholder="Email Address" value="<?php echo form_value('RPEmail', $dt) ?>">
              <?php echo form_error('RPEmail');?>
            </div>
          </div>
        </div>



      </div>
    </div>



  </div>

  <button type="submit" class="btn btn-primary" ><i class="icon-save"></i>&nbsp;&nbsp; Save</button>


</form>
