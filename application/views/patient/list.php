<?php
$con = '(Hidden = 0 OR Hidden IS NULL)';
$provider = $this->ProviderProfileModel->get_by_field('Org_Id', $this->current_user->Org_Id, $con)->result();
?>

<div class="page-header">
  <h1>
    Patient
  </h1>
</div>

<div class="widget-box">
  <div class="widget-header widget-header-blue ">
    <h4 class="lighter">Patient Search</h4>
  </div>

  <div class="widget-body">
    <div class="widget-main">
      <form role="form" action="<?php echo site_url('patient/search') ?>" method="post" accept-charset="UTF-8">
        <input type="hidden" name="current_select" value="<?php echo $this->input->get('current_select'); ?>" id="current_select">
        <input type="hidden" name="current_time" value="<?php echo $this->input->get('current_time'); ?>" id="current_time">

        <div class="row">
          <div class="col-xs-6">
            <div class="form-group ">
              <label for=text_field">Search By Field</label>
              <div class="row">
                <div class="col-xs-6">
                  <?php $text_field = isset($form_search['text_field']) ? $form_search['text_field'] : null;?>
                  <input type="text" name="text_field" class="form-control" id="text_field" placeholder="Search By Last Name" value="<?php echo $text_field ?>">
                </div>
                <div class="col-xs-6">
                  <?php
                //field search array
                  $search_by_field = array(
                      1 => array("LastName", "Last Name"),
                      0 => array("FirstName", "First Name"),
                      2 => array("SSN", "Social Security Number"),
                      3 => array("MedicalRecordNumber", "Medical Record Number"),
                      4 => array("AccountNumber", "Account Number"),
                  );
                  ?>
                  <select class="form-control" id="by_field" name="by_field">
                    <!--                                        <option value="all">ALL</option>-->
                    <?php
                    $by_field = NULL;
                    foreach ($search_by_field as $sbf) {
                      if (isset($form_search['by_field'])) {
                        if ($sbf['0'] == $form_search['by_field']) {
                          $sel = "selected";
                          $by_field = $form_search['by_field'];
                        } else {
                          $sel = "";
                        }
                      } else {
                        $sel = "";
                      }
                      echo '<option value="' . $sbf['0'] . '" ' . $sel . '>' . $sbf['1'] . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xs-6">
            <div class="form-group">
              <label for="dob">Date of Birth</label>
              <?php
              $dob = null;
              if (isset($form_search['dob'])) {
                $dob = $form_search['dob'];
              } 
              ?>
              <input  name="dob" class="form-control date-form"    placeholder="mm/dd/yyyy"  value="<?php echo $dob ?>" />

            </div>
          </div>
        </div>

        <div class="row " >
          <div class="col-xs-6">
            <div class="form-group ">
              <label for="exampleInputEmail1">Patient's Provider</label>
              <?php
              $Provider_ID = "";
              if (isset($form_search['Provider_ID'])) {
                $Provider_ID = $form_search['Provider_ID'];
              }
              $option[''] = '[Select]';
              foreach ($provider as $p) {
                $option[$p->Provider_ID] = $p->ProviderLastName . ', ' . $p->ProviderFirstName;
              }
              echo form_dropdown('Provider_ID', $option, $Provider_ID, 'class = "form-control"');
              ?> 
            </div>
          </div>

          <div class="col-xs-6">
            <div class="form-group">
              <label for="Hidden">Patient's Status</label>
              <?php
              $patient_status = array("BOTH" => "Both", "ACTIVE" => "Active", "NON_ACTIVE" => "Non Active");
              $hidden_field = 'ACTIVE';
              if (isset($form_search['Hidden'])) {
                $hidden_field = $form_search['Hidden'];
              }
              echo form_dropdown('Hidden', $patient_status, $hidden_field, 'class = "form-control" id="Hidden"');
              ?>
            </div>
          </div>
        </div>

        <div class="clearfix">
          <button type="submit" class="btn btn-info">Search Patients</button>
          <?php echo anchor("patient/reset_search/", "Reset Search", 'class="btn btn-info"'); ?> &nbsp;
          <a href="#"  data-toggle="modal" data-target="#myModal">Advanced Search </span></a>
          <?php echo anchor('patient/demographics/', 'Add Patient', array('class' => 'btn btn-info pull-right')); ?>
        </div>
      </form>
    </div>
  </div>
</div>


<br/>

<div class="widget-box">
  <div class="widget-header widget-header-blue ">
    <h4 class="lighter">Search Results</h4>
  </div>

  <div class="widget-body">
    <div class="widget-main">

      <div class="table-responsive table-patien">

        <table class="table table-striped table-bordered table-hover ">
          <thead>
            <tr>
              <th class="center">#</th>
              <th class="center">Patient Name</th>
              <th class="center">Account Number</th>
              <th class="center">DOB</th>
              <th class="center">Age</th>
              <th class="center">MRN</th>
              <th class="center">Provider</th>
              <th width="10%">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $n = 1 + ($pages * 10);
            $params = 'current_select=' . $this->input->get('current_select') . '&current_time=' . $this->input->get('current_time');
            if ($list) {
              foreach ($list as $lt) {
                if ($lt->Hidden == 0) {
                  $drop = '<li><a href="' . site_url('patient/demographics/' . $lt->Patient_ID) . '"><i class="icon-search"></i> Patient Details</a></li>';
                  $view_profile = anchor("patient/demographics/$lt->Patient_ID", '<i class="icon-search"></i>', array('class' => '', 'title' => 'Patient Detail', 'data-toggle' => "tooltip", 'data-placement' => "bottom"));
                } else {
                  $drop = NULL;
                  $view_profile = '<a href="javascript:void(0)" class="" title="Patient Detail" data-toggle="tooltip" data-placement="bottom"><i class="icon-lock"></i></a>';
                }
                $view_appointment_history = anchor("patient/patient_appointment_history/$lt->Patient_ID", '<span class="icon-time"></span>', array('class' => '', 'title' => 'Appointment History', 'data-toggle' => "tooltip", 'data-placement' => "bottom"));
                $view_appointment = anchor("schedule/appointment_new/$lt->Patient_ID" . '?' . $params, '<i class="icon-calendar"></i>', array('class' => '', 'title' => 'Add Appointment', 'data-toggle' => "tooltip", 'data-placement' => "bottom"));

                echo "<tr>";
                echo "<td class='center'>$n</td>";
                if ($lt->LastName == "" or $lt->LastName == NULL) {
                  $name_patient = $lt->FirstName . " " . $lt->MiddleName;
                  //echo "<td> $link_name" . anchor("patient/demographics/$lt->Patient_ID", $lt->FirstName . " " . $lt->MiddleName, array('class' => '', 'title' => 'Patient Detail', 'data-toggle' => "tooltip", 'data-placement' => "bottom")) . "</td>";
                } else {
                  $name_patient = $lt->LastName . ", " . $lt->FirstName . " " . $lt->MiddleName;
                  //echo "<td> $link_name " . anchor("patient/demographics/$lt->Patient_ID", $lt->LastName . ", " . $lt->FirstName . " " . $lt->MiddleName, array('class' => '', 'title' => 'Patient Detail', 'data-toggle' => "tooltip", 'data-placement' => "bottom")) . "</td>";
                }


                $link_name = '<div class="btn-group">
            <a href="#" data-toggle="dropdown">' . $name_patient . ' &nbsp;<i class="icon-caret-down"></i></a> 
            <ul class="dropdown-menu" role="menu">
              ' . $drop . '
              <li><a href="' . site_url('patient/patient_appointment_history/' . $lt->Patient_ID) . '"><span class="icon-time"></span> Appointment History</a></li>
              <li><a href="' . site_url('schedule/appointment_new/' . $lt->Patient_ID . '?' . $params . '&provider=' . $lt->Provider_ID) . '"><i class="icon-calendar"></i> Add Appointment</a></li>
              <li><a href="' . site_url('patient/patient_encounter_history/' . $lt->Patient_ID) . '"><span class="icon-bell"></span> Encounters</a></li>
            </ul>
          </div>';


                echo "<td>" . $link_name . "</td>";
                echo "<td>" . sprintf('%s', $lt->AccountNumber)  . "</td>";
                echo "<td class='center'>" . date("d M Y", strtotime($lt->DOB)) . "</td>";
                echo "<td class='center'>" . dob_to_age($lt->DOB) . " Years</td>";
                echo "<td>" . $lt->MedicalRecordNumber . "</td>";
                echo "<td> " . $lt->ProviderFirstName . " " . $lt->ProviderMiddleName . " " . $lt->ProviderLastName . " </td>";
                echo "<td class='center'>$view_profile &nbsp; $view_appointment_history &nbsp;  $view_appointment</td>";
                echo "</tr>";
                $n++;
              }
            }
            ?>

          </tbody>
        </table>
      </div>

      <?php
      echo "<center>" . $link_pagging . "</center>";
      ?>
    </div>
  </div>
</div>




<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="false" >
  <div class="modal-dialog">
    <?php
    $params = 'current_select=' . $this->input->get('current_select') . '&current_time=' . $this->input->get('current_time');
    ?>
    <form role="form" action="<?php echo site_url('patient/search?' . $params) ?>" method="post" accept-charset="UTF-8">
      <input type="hidden" name="current_select" value="<?php echo $this->input->get('current_select'); ?>" >
      <input type="hidden" name="current_time" value="<?php echo $this->input->get('current_time'); ?>" >
      <input type="hidden" name="dob" value="<?php echo $dob; ?>" >
      <input type="hidden" name="by_field" value="<?php echo $by_field; ?>" >
      <input type="hidden" name="text_field" value="<?php echo $text_field; ?>" >

      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Advanced Search</h4>
        </div>
        <div class="modal-body">


          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label for="lastName" >Last Name</label>
                <input type="text" name="lastName" class="form-control" id="lastName" placeholder="Last Name"  value="<?php echo isset($form_search["lastName"]) ? $form_search["lastName"] : null; ?>">
              </div>
              <div class="form-group">
                <label for="SSN">Social Security Number</label>
                <input type="text" class="form-control" id="SSN" name="SSN" placeholder="Social Security Number" value="<?php echo isset($form_search["SSN"]) ? $form_search["SSN"] : null; ?>">
              </div>
              <div class="form-group">
                <label for="MedicalRecordNumber" >Medical Account Number</label>
                <input type="text" class="form-control" id="MedicalRecordNumber" name="MedicalRecordNumber" placeholder="Medical Account Number" value="<?php echo isset($form_search["MedicalRecordNumber"]) ? $form_search["MedicalRecordNumber"] : null; ?>">
              </div>

              <div class="form-group">
                <label for="Hidden2">Patient's Status</label>
                <?php
                echo form_dropdown('Hidden', $patient_status, $hidden_field, 'class = "form-control" id="Hidden"');
                ?>
              </div>

            </div>

            <div class="col-xs-6">
              <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" class="form-control" name="firstName" id="firstName" placeholder="First Name" value="<?php echo isset($form_search["firstName"]) ? $form_search["firstName"] : null; ?>">
              </div>

              <div class="form-group">
                <label for="AccountNumber" >Patient's Account Number</label>
                <input type="text" name="AccountNumber" class="form-control" id="AccountNumber" placeholder="Patient's Account Number" value="<?php echo isset($form_search["AccountNumber"]) ? $form_search["AccountNumber"] : null; ?>">
              </div>
              <div class="form-group">
                <label for="phoneNumber" >Phone Number</label>
                <input type="text" class="form-control" id="phoneNumber" name="PhoneHome" placeholder="Phone Number" value="<?php echo isset($form_search["PhoneHome"]) ? $form_search["PhoneHome"] : null; ?>">
              </div>
              <div class="form-group">
                <label for="dob" >Patient Provider</label>
                <?php
                echo form_dropdown('Provider_ID', $option, $Provider_ID, 'class = "form-control"');
                ?> 
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Search Patient</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div><!-- /.modal-content -->
    </form>

  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
