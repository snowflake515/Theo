<?php
$id_patient = $this->EncounterHistoryModel->get_by_id($id_encounter)->row();
$dt_patien = $this->PatientProfileModel->get_by_id($id_patient->Patient_ID)->row();
if($id_patient):
?>
<div class="page-header">
  <h1>
    Template For "<?php echo $dt_patien->LastName . ', ' . $dt_patien->FirstName ?>"
  </h1>
</div>

<div class="widget-box">
  <div class="widget-header">
    <h4>Select Template</h4>
  </div>
  <div class="widget-body">
    <form class="form-horizontal" role="form" method="post" action="<?php echo site_url('template/tml2') ?>" accept-charset="UTF-8">
      <!-- Default panel contents -->
      <div class="panel-body">
        <div class="form-group">
          <label class="col-sm-2 control-label">Template</label>
          <div class="col-sm-10">
            <?php
            echo form_hidden('id_appt', $id_patient->Appointments_ID);
            echo form_hidden('id_encounter', $id_encounter);
            $option = option_select($template1, 'TML1_ID', 'TML1_Description', '', TRUE);
            echo form_dropdown('TML1_ID', $option, form_value('Appointments[Provider_ID]', $dt), 'class = "form-control" data-target="#option_encounter_type"');
            echo form_error('TML1_ID');
            ?>
          </div>
        </div>
      </div>
      <div class="panel-footer">
        <div >
          <?php echo anchor('encounter/start/' . $id_patient->Appointments_ID, '<i class="icon icon-arrow-left"></i>&nbsp;&nbsp; Back', array('class' => 'btn btn-default')); ?>
          <button type="submit" class="btn btn-primary">Next &nbsp;&nbsp; <i class="icon icon-arrow-right"></i></button>

        </div>
      </div>

    </form>
  </div>
</div>

<?php else:?>
<?php echo 'Encounter not found';?>
<?php endif;?>
