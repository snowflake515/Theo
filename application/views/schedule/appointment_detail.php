<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
      <h4 class="modal-title">Appointment Detail for "<?php echo $patient->LastName . ', ' . $patient->FirstName.' '.$patient->MiddleName ?>"</h4>
    </div>
    <div class="modal-body">
      <h4 class="text-center"><?php echo date("l F d , Y", strtotime($dt->ApptStart)) ?></h4>
      <hr/>
      <div class="form-horizontal">
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-4 control-label" >Appt Start</label>
          <div class="col-sm-7">
            <div class="form-control text-only" ><?php echo date("h:i A", strtotime($dt->ApptStart)) ?></div>
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-4 control-label" >Appt Stop </label>
          <div class="col-sm-7">
            <div class="form-control text-only" ><?php echo date("h:i A", strtotime($dt->ApptStop)) ?></div>
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-4 control-label" >Patient Name</label>
          <div class="col-sm-7">
            <div class="form-control text-only" ><?php echo $patient->LastName . ', ' . $patient->FirstName ?> </div>
          </div>
        </div>
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-4 control-label" >Status</label>
          <div class="col-sm-7">
            <?php
            $checkin = option_select($checkin_code, 'CodeOrder', 'Description');
            echo form_dropdown('heck', $checkin, form_value('CodeOrder', $get_last_check), 'class = "form-control" id="CCodes_Id" onchange="change_status_chekin_data(this)" data-apptid="' . $dt->Appointments_ID . '"');
            ?>  
          </div>
        </div>
      </div>
      <div class="modal-footer">  
        <?php echo anchor('schedule/appointment_edit/' . $dt->Appointments_ID, 'Edit appointment', array('class' => 'btn btn-primary')); ?>
        <?php echo anchor('encounter/start/' . $dt->Appointments_ID, '<i class="icon icon-time"></i> &nbsp;Start Encounter', array('class' => 'btn btn-success')); ?>
        <button type="button" class="btn btn-default noRadius" data-dismiss="modal">Save</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->